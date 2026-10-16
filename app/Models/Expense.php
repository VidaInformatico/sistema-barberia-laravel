<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'cash_register_id',
        'payment_method_id',
        'user_id',

        // Montos
        'amount',
        'itbis',
        'other_taxes',
        'total_amount',

        'concept',
        'description',
        'expense_date',
        'branch_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'itbis'          => 'decimal:2',
        'other_taxes'    => 'decimal:2',
        'total_amount'   => 'decimal:2',
        'expense_date'   => 'date',
    ];

    public function branch() {
        return $this->belongsTo(Branch::class);
    }

    public function cashRegister(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
