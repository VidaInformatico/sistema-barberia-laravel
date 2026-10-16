<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetailComboItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_detail_id',
        'product_id',
        'product_name',
        'unit_quantity',
        'total_quantity',
    ];

    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}