<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\EscPosReceiptPrinter;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StrukController extends Controller
{
    public function struk(
        Request $request,
        Transaction $transaction,
        EscPosReceiptPrinter $receiptPrinter
    ): JsonResponse|RedirectResponse {
        $this->authorize('viewReceipt', $transaction);

        try {
            $receiptPrinter->print($transaction);
        } catch (Exception $exception) {
            report($exception);
            $message = 'Struk tidak dapat dicetak. Periksa printer dan nama printer di konfigurasi.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 503);
            }

            return to_route('kasir.receipt', $transaction)->with('error', $message);
        }

        $message = 'Struk berhasil dikirim ke printer thermal.';

        if ($request->expectsJson()) {
            return response()->json(['message' => $message]);
        }

        return to_route('kasir.receipt', $transaction)->with('success', $message);
    }
}
