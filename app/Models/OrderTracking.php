<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderTracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'order_state_id',
        'user_id',
        'comment',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(OrderState::class, 'order_state_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}