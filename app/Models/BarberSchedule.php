<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
 
class BarberSchedule extends Model
{
    protected $fillable = [
        'barber_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_active',
    ];
 
    protected function casts(): array
    {
        return [
            'is_active'   => 'boolean',
            'day_of_week' => 'integer',
        ];
    }
 
    public function barber(): BelongsTo
    {
        return $this->belongsTo(Barber::class);
    }
 
    public function getDayNameAttribute(): string
    {
        $days = [
            0 => 'Domingo',
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
        ];
 
        return $days[$this->day_of_week] ?? '—';
    }
}