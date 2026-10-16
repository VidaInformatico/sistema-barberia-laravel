<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_PENDING     = 'pending';
    const STATUS_CONFIRMED   = 'confirmed';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED   = 'completed';
    const STATUS_CANCELLED   = 'cancelled';
    const STATUS_NO_SHOW     = 'no_show';

    protected $fillable = [
        'booking_number',
        'payment_method',
        'client_id',
        'barber_id',
        'booking_date',
        'start_time',
        'end_time',
        'status',
        'total_price',
        'notes',
        'admin_notes',
        'cancellation_reason',
        'cancelled_at',
        'cancelled_by',
        'confirmed_at',
        'arrived_at',
        'branch_id'
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'total_price'  => 'decimal:2',
            'cancelled_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'arrived_at'   => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Booking $booking) {
            if (empty($booking->booking_number)) {
                $booking->booking_number = static::generateBookingNumber();
            }
        });
    }

    public static function generateBookingNumber(): string
    {
        $date  = now()->format('Ymd');
        $count = static::whereDate('created_at', today())->count() + 1;
        return "BK-{$date}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function barber(): BelongsTo
    {
        return $this->belongsTo(Barber::class);
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function services(): HasMany
    {
        return $this->hasMany(BookingService::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(BookingStatusHistory::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
    public function isConfirmed(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }
    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }
    public function isNoShow(): bool
    {
        return $this->status === self::STATUS_NO_SHOW;
    }

    public function canBeCancelledByClient(): bool
    {
        if (! in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED])) {
            return false;
        }

        $policy = BookingPolicy::first();
        $hoursBeforeBooking = now()->diffInHours($this->booking_date->setTimeFromTimeString($this->start_time), false);

        return $hoursBeforeBooking >= ($policy?->cancellation_hours ?? 2);
    }

    public function changeStatus(string $newStatus, User $changedBy, ?string $comment = null): void
    {
        $previousStatus = $this->status;

        $this->status = $newStatus;

        if ($newStatus === self::STATUS_CONFIRMED) {
            $this->confirmed_at = now();
        }

        if ($newStatus === self::STATUS_CANCELLED) {
            $this->cancelled_at = now();
            $this->cancelled_by = $changedBy->id;
        }

        $this->save();

        $this->statusHistory()->create([
            'changed_by'      => $changedBy->id,
            'previous_status' => $previousStatus,
            'new_status'      => $newStatus,
            'comment'         => $comment,
        ]);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING     => 'Pendiente',
            self::STATUS_CONFIRMED   => 'Confirmada',
            self::STATUS_IN_PROGRESS => 'En curso',
            self::STATUS_COMPLETED   => 'Finalizada',
            self::STATUS_CANCELLED   => 'Cancelada',
            self::STATUS_NO_SHOW     => 'No se presentó',
            default                  => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING     => 'yellow',
            self::STATUS_CONFIRMED   => 'blue',
            self::STATUS_IN_PROGRESS => 'indigo',
            self::STATUS_COMPLETED   => 'green',
            self::STATUS_CANCELLED   => 'red',
            self::STATUS_NO_SHOW     => 'gray',
            default                  => 'gray',
        };
    }

    public function scopeUpcoming($query)
    {
        return $query->where('booking_date', '>=', today())
            ->whereNotIn('status', [self::STATUS_CANCELLED, self::STATUS_NO_SHOW]);
    }

    public function scopeForDate($query, string $date)
    {
        return $query->where('booking_date', $date);
    }

    public function scopeForBarber($query, int $barberId)
    {
        return $query->where('barber_id', $barberId);
    }
}
