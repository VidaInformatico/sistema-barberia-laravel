<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItemComboItem extends Model
{
    protected $fillable = [
        'sale_item_id',
        'product_id',
        'product_name',
        'unit_quantity',
        'total_quantity',
    ];

    public function saleDetail()
    {
        return $this->belongsTo(SaleItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
