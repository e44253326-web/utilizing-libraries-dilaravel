<?php

namespace App\Models;

use Database\Factories\TransactionDetailFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionDetail extends Model
{
    /** @use HasFactory<TransactionDetailFactory> */
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'product_id',
        'product_code',
        'product_name',
        'price',
        'qty',
        'discount',
        'subtotal',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
