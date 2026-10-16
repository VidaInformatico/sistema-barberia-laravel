<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'paymentable_id',
        'paymentable_type',
        'payment_method_id',
        'amount',
        'received_amount',
        'returned_amount',
        'reference'
    ];

    public function paymentable()
    {
        return $this->morphTo();
    }

    public function method()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
}
