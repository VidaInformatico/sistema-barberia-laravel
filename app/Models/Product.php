<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'type',
        'description',
        'sku',
        'price',
        'cost_price',
        'stock',
        'stock_min',
        'sort_order',
        'duration_minutes',
        'tax',
        'is_exempt',
        'is_inventoriable',
        'image',
        'is_active',
        'branch_id'
    ];

    protected function casts(): array
    {
        return [
            'price'      => 'decimal:2',
            'cost_price' => 'decimal:2',
            'stock'      => 'integer',
            'stock_min'  => 'integer',
            'is_active'  => 'boolean',
            'is_exempt'  => 'boolean',
        ];
    }

    public function branch() {
        return $this->belongsTo(Branch::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function barbers()
    {
        return $this->belongsToMany(Barber::class, 'barber_services', 'product_id', 'barber_id')
            ->withPivot('custom_price')
            ->withTimestamps();
    }

    public function ratings()
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function comboItems(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class, 
            'product_combo', 
            'combo_id', 
            'product_id'
        )->withPivot('quantity')->withTimestamps();
    }

    public function hasLowStock(): bool
    {
        return $this->stock <= $this->stock_min;
    }

    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
    }

    public function decrementStock(int $quantity, string $reason = '', ?Model $reference = null): StockMovement
    {
        $before = $this->stock;
        $this->decrement('stock', $quantity);

        return $this->stockMovements()->create([
            'user_id'        => Auth::id(),
            'type'           => 'out',
            'quantity'       => $quantity,
            'stock_before'   => $before,
            'stock_after'    => $this->stock,
            'reason'         => $reason,
            'reference_id'   => $reference?->id,
            'reference_type' => $reference ? get_class($reference) : null,
        ]);
    }

    public function incrementStock(int $quantity, string $reason = ''): StockMovement
    {
        $before = $this->stock;
        $this->increment('stock', $quantity);

        return $this->stockMovements()->create([
            'user_id'      => Auth::id(),
            'type'         => 'in',
            'quantity'     => $quantity,
            'stock_before' => $before,
            'stock_after'  => $this->stock,
            'reason'       => $reason,
        ]);
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset("storage/{$this->image}") : null;
    }

    protected function formattedDuration(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->type === 'product' || !$this->duration_minutes) {
                    return null;
                }

                $minutes = $this->duration_minutes;

                if ($minutes < 60) {
                    return "{$minutes} min";
                }

                $hours = floor($minutes / 60);
                $remainingMinutes = $minutes % 60;

                if ($remainingMinutes === 0) {
                    return $hours === 1 ? "1 hora" : "{$hours} horas";
                }

                return $hours === 1
                    ? "1 hora {$remainingMinutes} min"
                    : "{$hours} horas {$remainingMinutes} min";
            }
        );
    }
}
