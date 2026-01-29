<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orders';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'invoice_number',
        'purchaser_id',
        'order_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'order_date' => 'date',
        ];
    }

    /**
     * Get the purchaser (user) who placed this order.
     */
    public function purchaser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'purchaser_id');
    }

    /**
     * Get the order items for this order.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    /**
     * Calculate the total amount for this order.
     *
     * @return float
     */
    public function getTotalAttribute(): float
    {
        return $this->orderItems()
            ->with('product')
            ->get()
            ->sum(function ($item) {
                return $item->product ? ($item->quantity * $item->product->price) : 0;
            });
    }
}
