<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'client_id',
        'order_state_id',
        'payment_gateway',
        'payment_id',
        'shipping_address',
        'gross_total',
        'tax_total',
        'discount_total',
        'total',
        'notes',
        'branch_id'
    ];

    protected function casts(): array
    {
        return [
            'gross_total'    => 'decimal:2',
            'tax_total'      => 'decimal:2',
            'discount_total' => 'decimal:2',
            'total'          => 'decimal:2',
        ];
    }

    public function branch() {
        return $this->belongsTo(Branch::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(OrderState::class, 'order_state_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function trackings(): HasMany
    {
        return $this->hasMany(OrderTracking::class);
    }

    public function changeState(OrderState $newState, ?int $userId = null, ?string $comment = null): void
    {
        DB::transaction(function () use ($newState, $userId, $comment) {
            $this->update([
                'order_state_id' => $newState->id,
            ]);

            $this->trackings()->create([
                'order_state_id' => $newState->id,
                'user_id'        => $userId,
                'comment'        => $comment,
            ]);

            if ($newState->is_final) {
                $this->convertToSale($userId);
            }
        });
    }

    public function convertToSale(?int $userId = null): Sale
    {
        return DB::transaction(function () use ($userId) {
            $sale = Sale::create([
                'sale_number'     => 'SALE-' . $this->order_number,
                'client_id'       => $this->client_id,
                'created_by'      => $userId,
                'gross_total'     => $this->gross_total,
                'tax_total'       => $this->tax_total,
                'net_total'       => $this->gross_total - $this->discount_total,
                'discount_total'  => $this->discount_total,
                'total'           => $this->total,
                'paid_amount'     => $this->total,
                'change'          => 0,
                'notes'           => $this->notes,
            ]);

            foreach ($this->details as $detail) {
                $sale->items()->create([
                    'product_id'      => $detail->product_id,
                    'product_name'    => $detail->product_name,
                    'quantity'        => $detail->quantity,
                    'cost'            => $detail->cost,
                    'price'           => $detail->price,
                    'itbis_rate'      => $detail->itbis_rate,
                    'discount_rate'   => $detail->discount_rate,
                    'itbis_amount'    => $detail->itbis_amount,
                    'gross_subtotal'  => $detail->gross_subtotal,
                    'discount_amount' => $detail->discount_amount,
                    'net_subtotal'    => $detail->net_subtotal,
                    'total'           => $detail->total,
                    'is_exempt'       => $detail->is_exempt,
                ]);
            }

            return $sale;
        });
    }
}