<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
 
class BarberBlockedTime extends Model
{
    protected $fillable = [
        'barber_id',
        'date',
        'start_time',
        'end_time',
        'reason',
    ];
 
    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }
 
    public function barber(): BelongsTo
    {
        return $this->belongsTo(Barber::class);
    }
 
    public function isFullDay(): bool
    {
        return is_null($this->start_time);
    }
}