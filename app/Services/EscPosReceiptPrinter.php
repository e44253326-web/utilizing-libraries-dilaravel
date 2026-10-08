<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Str;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class EscPosReceiptPrinter
{
    public function print(Transaction $transaction): void
    {
        $transaction->loadMissing(['details', 'user']);
        $printer = new Printer(
            new WindowsPrintConnector((string) config('escpos.printer_name'))
        );

        try {
            $this->printHeader($printer);
            $this->printTransactionDetails($printer, $transaction);
            $this->printItems($printer, $transaction);
            $this->printTotals($printer, $transaction);

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Terima kasih telah berbelanja\n");
            $printer->feed(3);
            $printer->cut();
        } finally {
            $printer->close();
        }
    }

    private function printHeader(Printer $printer): void
    {
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $this->printWrappedText($printer, (string) config('escpos.store_name'));
        $printer->setEmphasis(false);
        $printer->text($this->separator()."\n");
    }

    private function printTransactionDetails(Printer $printer, Transaction $transaction): void
    {
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $this->printWrappedText($printer, 'No: '.$transaction->transaction_number);
        $this->printWrappedText($printer, 'Tanggal: '.$transaction->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i'));
        $this->printWrappedText($printer, 'Kasir: '.($transaction->user?->name ?? 'Kasir'));
        $this->printWrappedText($printer, 'Pelanggan: '.$transaction->customer_name);
        $printer->text($this->separator()."\n");
    }

    private function printItems(Printer $printer, Transaction $transaction): void
    {
        foreach ($transaction->details as $detail) {
            $this->printWrappedText($printer, $detail->product_name);
            $this->printWrappedText(
                $printer,
                $detail->qty.' x Rp'.number_format((float) $detail->price, 0, ',', '.')
            );
            $this->printWrappedText(
                $printer,
                'Subtotal: Rp'.number_format((float) $detail->subtotal, 0, ',', '.')
            );
        }

        $printer->text($this->separator()."\n");
    }

    private function printTotals(Printer $printer, Transaction $transaction): void
    {
        $this->printSummaryLine($printer, 'Subtotal', (float) $transaction->subtotal);
        $this->printSummaryLine($printer, 'Diskon', (float) $transaction->discount);
        $this->printSummaryLine($printer, 'Pajak / PPN', (float) $transaction->tax);
        $this->printSummaryLine($printer, 'Biaya lain', (float) $transaction->other_fee);

        $printer->setEmphasis(true);
        $this->printSummaryLine($printer, 'TOTAL', (float) $transaction->grand_total);
        $printer->setEmphasis(false);

        $this->printSummaryLine($printer, 'Dibayar', (float) $transaction->paid_amount);
        $this->printSummaryLine($printer, 'Kembalian', (float) $transaction->change_amount);
        $this->printWrappedText($printer, 'Metode: '.$transaction->payment_method);
        $printer->text($this->separator()."\n");
    }

    private function printSummaryLine(Printer $printer, string $label, float $amount): void
    {
        $printer->text($label.': Rp'.number_format($amount, 0, ',', '.')."\n");
    }

    private function printWrappedText(Printer $printer, string $text): void
    {
        $printer->text(wordwrap(Str::ascii($text), 32, "\n", true)."\n");
    }

    private function separator(): string
    {
        return str_repeat('-', 32);
    }
}
