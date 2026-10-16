<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'quantity',
        'cost',
        'price',
        'itbis_rate',
        'discount_rate',
        'itbis_amount',
        'gross_subtotal',
        'discount_amount',
        'net_subtotal',
        'total',
        'is_exempt',
    ];

    protected function casts(): array
    {
        return [
            'quantity'        => 'integer',
            'cost'            => 'decimal:2',
            'price'           => 'decimal:2',
            'itbis_rate'      => 'decimal:2',
            'discount_rate'   => 'decimal:2',
            'itbis_amount'    => 'decimal:2',
            'gross_subtotal'  => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'net_subtotal'    => 'decimal:2',
            'total'           => 'decimal:2',
            'is_exempt'       => 'boolean',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function comboItems()
    {
        return $this->hasMany(OrderDetailComboItem::class, 'order_detail_id');
    }
}