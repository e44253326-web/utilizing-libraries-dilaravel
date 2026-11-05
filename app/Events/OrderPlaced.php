<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPlaced implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order_id;

    public $total;

    /**
     * Create a new event instance.
     */
    public function __construct($order_id, $total)
    {
        $this->order_id = $order_id;
        $this->total = $total;
    }

    /**
     * Get the channels the event should broadcast on.
     * Gabungan Channel 1 (user) + Channel 2 (admin) - sesuai channels.php
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.'.auth()->id()),
            new PrivateChannel('admin.transactions'),
        ];
    }

    /**
     * Nama event yang didengar di Echo
     */
    public function broadcastAs(): string
    {
        return 'OrderPlaced';
    }

    /**
     * Data yang dikirim ke frontend
     */
    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order_id,
            'total' => $this->total,
        ];
    }
}
