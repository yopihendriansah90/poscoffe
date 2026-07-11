<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'code',
        'user_id',
        'customer_id',
        'dining_table_id',
        'order_type',
        'status',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount_amount',
        'discount_name',
        'total',
        'notes',
        'ordered_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'integer',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'integer',
            'discount_amount' => 'integer',
            'total' => 'integer',
            'ordered_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function diningTable(): BelongsTo
    {
        return $this->belongsTo(DiningTable::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
