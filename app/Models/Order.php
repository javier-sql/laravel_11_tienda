<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_email',
        'shipping_name',
        'shipping_email',
        'shipping_phone',
        'shipping_street',
        'shipping_number',
        'shipping_unit',
        'shipping_city',
        'shipping_commune',
        'shipping_zip',
        'total',
        'shipping_type',
        'shipping_cost',
        'status',
        'flow_order_id',
        'flow_response',
    ];



    // 🔗 Relación con OrderItem
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    // 🔗 Relación con User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }

}
