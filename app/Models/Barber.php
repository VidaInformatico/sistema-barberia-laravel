<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class Barber extends Model
{
    use HasFactory, SoftDeletes;
 
    protected $fillable = [
        'user_id',
        'bio',
        'specialty',
        'experience_years',
        'is_available',
        'instagram',
    ];
 
    protected function casts(): array
    {
        return [
            'is_available'     => 'boolean',
            'experience_years' => 'integer',
        ];
    }
 
    /* ------------------------------------------------------------------ */
    /*  Relationships                                                       */
    /* ------------------------------------------------------------------ */
 
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ratings()
    {
        return $this->morphMany(Rating::class, 'rateable');
    }
 
    public function schedules(): HasMany
    {
        return $this->hasMany(BarberSchedule::class);
    }
 
    public function blockedTimes(): HasMany
    {
        return $this->hasMany(BarberBlockedTime::class);
    }
 
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'barber_services')
                    ->withPivot('custom_price')
                    ->withTimestamps();
    }
 
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
 
    public function galleryPhotos(): HasMany
    {
        return $this->hasMany(GalleryPhoto::class);
    }
 
    public function isAvailableAt(\Carbon\Carbon $dateTime, int $durationMinutes = 30): bool
    {
        $dayOfWeek = $dateTime->dayOfWeek;
        $time      = $dateTime->format('H:i:s');
        $endTime   = $dateTime->copy()->addMinutes($durationMinutes)->format('H:i:s');
 
        // Verificar que tenga horario ese día
        $schedule = $this->schedules()
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->where('start_time', '<=', $time)
            ->where('end_time', '>=', $endTime)
            ->first();
 
        if (! $schedule) return false;
 
        // Verificar que no esté bloqueado
        $blocked = $this->blockedTimes()
            ->where('date', $dateTime->toDateString())
            ->where(function ($q) use ($time, $endTime) {
                $q->whereNull('start_time') // día completo bloqueado
                  ->orWhere(function ($q2) use ($time, $endTime) {
                      $q2->where('start_time', '<', $endTime)
                         ->where('end_time', '>', $time);
                  });
            })
            ->exists();
 
        if ($blocked) return false;
 
        // Verificar que no tenga reservas confirmadas en ese horario
        return ! $this->bookings()
            ->where('booking_date', $dateTime->toDateString())
            ->whereIn('status', ['confirmed', 'pending', 'in_progress'])
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $time)
            ->exists();
    }
 
    public function getFullNameAttribute(): string
    {
        return $this->user->full_name;
    }
}