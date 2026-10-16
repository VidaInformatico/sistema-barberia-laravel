<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'supplier_id',
        'user_id',
        'gross_total',
        'purchase_number',
        'cash_register_id',
        'use_cash_register',
        'tax_total',
        'net_total',
        'discount_total',
        'total',
        'credit',
        'paid_amount',
        'change',
        'purchase_date',
        'paid_at',
        'status',
        'note',
        'branch_id'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'use_cash_register' => 'boolean'
    ];

    public function branch() {
        return $this->belongsTo(Branch::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }
}
