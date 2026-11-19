<?php

namespace App\Services;

use Mike42\Escpos\PrintConnectors\PrintConnector;
use RuntimeException;

class WindowsSpoolerPrintConnector implements PrintConnector
{
    private ?array $buffer = [];

    public function __construct(private readonly string $printerName) {}

    public function __destruct()
    {
        if ($this->buffer !== null) {
            trigger_error('Print connector was not finalized.', E_USER_NOTICE);
        }
    }

    public function finalize(): void
    {
        $data = implode($this->buffer ?? []);
        $this->buffer = null;

        $this->sendToPrinter($data);
    }

    public function read(int $len): bool|string
    {
        return false;
    }

    public function write(string $data): void
    {
        if ($this->buffer === null) {
            throw new RuntimeException('The printer connector is already closed.');
        }

        $this->buffer[] = $data;
    }

    private function sendToPrinter(string $data): void
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            throw new RuntimeException('The Windows print connector can only run on Windows.');
        }

        $windowsDirectory = getenv('WINDIR');
        if (! is_string($windowsDirectory) || $windowsDirectory === '') {
            throw new RuntimeException('The Windows PowerShell executable could not be located.');
        }

        $powershell = $windowsDirectory.'\\System32\\WindowsPowerShell\\v1.0\\powershell.exe';
        if (! is_file($powershell)) {
            throw new RuntimeException('The Windows PowerShell executable could not be located.');
        }

        $payloadPath = tempnam(sys_get_temp_dir(), 'escpos-');
        if ($payloadPath === false) {
            throw new RuntimeException('A temporary print job could not be created.');
        }

        try {
            if (file_put_contents($payloadPath, $data) === false) {
                throw new RuntimeException('The temporary print job could not be written.');
            }

            $script = $this->powershellScript($payloadPath);
            $encodedScript = base64_encode(mb_convert_encoding($script, 'UTF-16LE', 'UTF-8'));
            $process = proc_open(
                [$powershell, '-NoProfile', '-NonInteractive', '-EncodedCommand', $encodedScript],
                [
                    0 => ['pipe', 'r'],
                    1 => ['pipe', 'w'],
                    2 => ['pipe', 'w'],
                ],
                $pipes
            );

            if (! is_resource($process)) {
                throw new RuntimeException('The Windows print spooler process could not be started.');
            }

            fclose($pipes[0]);
            stream_set_blocking($pipes[1], false);
            stream_set_blocking($pipes[2], false);

            $errorOutput = '';
            $deadline = microtime(true) + 15;
            $exitCode = null;

            try {
                do {
                    stream_get_contents($pipes[1]);
                    $errorOutput .= stream_get_contents($pipes[2]);
                    $status = proc_get_status($process);

                    if (! $status['running']) {
                        $exitCode = $status['exitcode'];
                        break;
                    }

                    if (microtime(true) >= $deadline) {
                        throw new RuntimeException('The printer did not respond within 15 seconds.');
                    }

                    usleep(100_000);
                } while (true);

                $errorOutput .= stream_get_contents($pipes[2]);
            } finally {
                foreach ($pipes as $pipe) {
                    if (is_resource($pipe)) {
                        fclose($pipe);
                    }
                }

                if (is_resource($process)) {
                    $status = proc_get_status($process);
                    if ($status['running']) {
                        proc_terminate($process);
                    }

                    proc_close($process);
                }
            }

            if ($exitCode !== 0) {
                throw new RuntimeException(trim($errorOutput) ?: 'The Windows print spooler rejected the job.');
            }
        } finally {
            if (is_file($payloadPath)) {
                unlink($payloadPath);
            }
        }
    }

    private function powershellScript(string $payloadPath): string
    {
        $printerName = base64_encode($this->printerName);
        $encodedPayloadPath = base64_encode($payloadPath);

        $script = <<<'POWERSHELL'
$ErrorActionPreference = 'Stop'
$printerName = [Text.Encoding]::UTF8.GetString([Convert]::FromBase64String('__PRINTER_NAME__'))
$payloadPath = [Text.Encoding]::UTF8.GetString([Convert]::FromBase64String('__PAYLOAD_PATH__'))

$source = @'
using System;
using System.Runtime.InteropServices;

public static class RawPrinterMethods
{
    [StructLayout(LayoutKind.Sequential, CharSet = CharSet.Unicode)]
    public class DocumentInfo
    {
        [MarshalAs(UnmanagedType.LPWStr)] public string pDocName;
        [MarshalAs(UnmanagedType.LPWStr)] public string pOutputFile;
        [MarshalAs(UnmanagedType.LPWStr)] public string pDatatype;
    }

    [DllImport("winspool.drv", EntryPoint = "OpenPrinterW", SetLastError = true, CharSet = CharSet.Unicode)]
    public static extern bool OpenPrinter(string printerName, out IntPtr printer, IntPtr defaults);

    [DllImport("winspool.drv", EntryPoint = "StartDocPrinterW", SetLastError = true, CharSet = CharSet.Unicode)]
    public static extern int StartDocPrinter(IntPtr printer, int level, [In] DocumentInfo documentInfo);

    [DllImport("winspool.drv", SetLastError = true)]
    public static extern bool StartPagePrinter(IntPtr printer);

    [DllImport("winspool.drv", SetLastError = true)]
    public static extern bool WritePrinter(IntPtr printer, byte[] data, int length, out int written);

    [DllImport("winspool.drv", SetLastError = true)]
    public static extern bool EndPagePrinter(IntPtr printer);

    [DllImport("winspool.drv", SetLastError = true)]
    public static extern bool EndDocPrinter(IntPtr printer);

    [DllImport("winspool.drv", SetLastError = true)]
    public static extern bool ClosePrinter(IntPtr printer);
}
'@

try {
    Add-Type -TypeDefinition $source
    $printer = [IntPtr]::Zero
    if (-not [RawPrinterMethods]::OpenPrinter($printerName, [ref]$printer, [IntPtr]::Zero)) {
        throw (New-Object ComponentModel.Win32Exception([Runtime.InteropServices.Marshal]::GetLastWin32Error()))
    }

    $documentStarted = $false
    $pageStarted = $false

    try {
        $document = New-Object -TypeName 'RawPrinterMethods+DocumentInfo'
        $document.pDocName = 'POS Transaction Receipt'
        $document.pOutputFile = $null
        $document.pDatatype = 'RAW'
        if ([RawPrinterMethods]::StartDocPrinter($printer, 1, $document) -eq 0) {
            throw (New-Object ComponentModel.Win32Exception([Runtime.InteropServices.Marshal]::GetLastWin32Error()))
        }
        $documentStarted = $true

        if (-not [RawPrinterMethods]::StartPagePrinter($printer)) {
            throw (New-Object ComponentModel.Win32Exception([Runtime.InteropServices.Marshal]::GetLastWin32Error()))
        }
        $pageStarted = $true

        $data = [IO.File]::ReadAllBytes($payloadPath)
        $written = 0
        if (-not [RawPrinterMethods]::WritePrinter($printer, $data, $data.Length, [ref]$written) -or $written -ne $data.Length) {
            throw (New-Object ComponentModel.Win32Exception([Runtime.InteropServices.Marshal]::GetLastWin32Error()))
        }

        if (-not [RawPrinterMethods]::EndPagePrinter($printer)) {
            throw (New-Object ComponentModel.Win32Exception([Runtime.InteropServices.Marshal]::GetLastWin32Error()))
        }
        $pageStarted = $false

        if (-not [RawPrinterMethods]::EndDocPrinter($printer)) {
            throw (New-Object ComponentModel.Win32Exception([Runtime.InteropServices.Marshal]::GetLastWin32Error()))
        }
        $documentStarted = $false
    } finally {
        if ($pageStarted) {
            [void][RawPrinterMethods]::EndPagePrinter($printer)
        }
        if ($documentStarted) {
            [void][RawPrinterMethods]::EndDocPrinter($printer)
        }
        [void][RawPrinterMethods]::ClosePrinter($printer)
    }
} catch {
    [Console]::Error.WriteLine($_.Exception.Message)
    exit 1
}
POWERSHELL;

        return str_replace(
            ['__PRINTER_NAME__', '__PAYLOAD_PATH__'],
            [$printerName, $encodedPayloadPath],
            $script
        );
    }
}
