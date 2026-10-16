<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
 
class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'branch_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'reason',
        'reference_id',
        'reference_type',
    ];
 
    protected function casts(): array
    {
        return [
            'quantity'     => 'integer',
            'stock_before' => 'integer',
            'stock_after'  => 'integer',
        ];
    }

    public function branch() {
        return $this->belongsTo(Branch::class);
    }
 
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
 
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
 
    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
 