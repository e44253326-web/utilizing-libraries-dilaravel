<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
// use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class ReceiptController extends Controller
{
    public function print()
    {
        // Untuk tugas, kita print ke file dulu biar bisa dibuktikan
        $connector = new FilePrintConnector(storage_path('app/struk.txt'));
        
        // Kalau sudah ada printer asli, pakai ini:
        // $connector = new WindowsPrintConnector("POS-58");

        $printer = new Printer($connector);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer->text("TOKO CONTOH\n");
        $printer->setEmphasis(false);
        $printer->text("Jl. Mawar No. 123\n");
        $printer->text("============================\n");
        
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("Barang A       2 x 5.000\n");
        $printer->text("Barang B       1 x 10.000\n");
        $printer->text("----------------------------\n");
        $printer->setEmphasis(true);
        $printer->text("Total:      Rp20.000\n");
        $printer->setEmphasis(false);
        $printer->text("============================\n");
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("Terima kasih!\n");

        $printer->cut();
        $printer->close();

        return response()->json(['message' => 'Struk berhasil dicetak di storage/app/struk.txt']);
    }
}