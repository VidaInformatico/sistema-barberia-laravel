<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
 
class BookingService extends Model
{
    protected $fillable = [
        'booking_id',
        'product_id',
        'service_name',
        'duration_minutes',
        'price',
    ];
 
    protected function casts(): array
    {
        return [
            'price'            => 'decimal:2',
            'duration_minutes' => 'integer',
        ];
    }
 
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
 
    public function service(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}