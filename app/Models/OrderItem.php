<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name', // puedes guardar el nombre al momento de la compra
        'price',
        'quantity',
    ];

    // 🔗 Relación con Order
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    // 🔗 Relación con Products
    public function product(): BelongsTo
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}
