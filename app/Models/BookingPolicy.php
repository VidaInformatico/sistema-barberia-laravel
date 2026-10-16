<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'max_days_in_advance',
        'min_hours_before_booking',
        'cancel_before_hours',
        'allow_client_cancellation',
        'grace_period_minutes',
        'mark_no_show_after_grace',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'max_days_in_advance'      => 'integer',
            'min_hours_before_booking' => 'integer',
            'cancel_before_hours'      => 'integer',
            'grace_period_minutes'     => 'integer',
            'allow_client_cancellation'=> 'boolean',
            'mark_no_show_after_grace' => 'boolean',
            'is_active'                => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public static function current(): ?self
    {
        return self::active()->latest()->first();
    }

    public function canBeCancelled(\Carbon\Carbon $bookingDateTime): bool
    {
        return now()->diffInHours($bookingDateTime, false)
            >= $this->cancel_before_hours;
    }
}