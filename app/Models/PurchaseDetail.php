<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'product_id',
        'product_name',
        'quantity',
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

    protected $casts = [
        'is_exempt' => 'boolean',
        'price' => 'decimal:2',
        'itbis_amount' => 'decimal:2',
        'gross_subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'net_subtotal' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
