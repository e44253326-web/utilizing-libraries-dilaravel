<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PdfController extends Controller
{
    public function invoice(?Transaction $transaction = null): Response
    {
        $transaction ??= Transaction::query()
            ->where('user_id', auth()->id())
            ->where('status', 'paid')
            ->latest()
            ->firstOrFail();

        $this->authorize('viewReceipt', $transaction);

        $transaction->load(['details', 'user']);

        return Pdf::loadView(
            'invoices.invoice',
            compact('transaction')
        )->download('invoice.pdf');
    }
}