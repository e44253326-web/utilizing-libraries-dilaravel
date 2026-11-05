<?php

namespace App\Events;

use App\Models\Transaction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class TransactionPlaced implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Transaction $transaction) {}

    /**
     * @return array<int, PrivateChannel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.'.$this->transaction->user_id),
            new PrivateChannel('admin.transactions'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'transaction.placed';
    }

    /**
     * @return array<string, int|float|string|bool|null>
     */
    public function broadcastWith(): array
    {
        $createdAt = $this->transaction->created_at;

        return [
            'transaction_id' => $this->transaction->id,
            'transaction_number' => $this->transaction->transaction_number,
            'grand_total' => (float) $this->transaction->grand_total,
            'change_amount' => (float) $this->transaction->change_amount,
            'payment_method' => $this->transaction->payment_method,
            'cashier_name' => $this->transaction->user?->name ?? 'Kasir',
            'receipt_url' => route('kasir.receipt', $this->transaction),
            'thermal_print_url' => route('kasir.receipt.print', $this->transaction),
            'created_at' => $createdAt?->timezone('Asia/Jakarta')->format('d/m/Y H:i'),
            'is_today' => $createdAt !== null
                && Carbon::now('Asia/Jakarta')->isSameDay($createdAt->timezone('Asia/Jakarta')),
        ];
    }
}
