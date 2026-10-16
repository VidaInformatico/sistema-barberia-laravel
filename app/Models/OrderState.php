<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderState extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'color',
        'is_initial',
        'is_canceled',
        'is_final',
    ];

    protected function casts(): array
    {
        return [
            'is_initial'  => 'boolean',
            'is_canceled' => 'boolean',
            'is_final'    => 'boolean',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function trackings(): HasMany
    {
        return $this->hasMany(OrderTracking::class);
    }
}