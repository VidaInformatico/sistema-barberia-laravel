<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GalleryPhoto extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'barber_id',
        'title',
        'description',
        'image',
        'is_featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'sort_order'  => 'integer',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function barber(): BelongsTo
    {
        return $this->belongsTo(Barber::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getImageUrlAttribute(): ?string
    {
        return $this->image
            ? asset("storage/{$this->image}")
            : null;
    }
}