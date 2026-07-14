<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'status',
        'total_amount',
        'notes',
        'pickup_at',
        'pickup_reminder_sent_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (! $order->order_number) {
                $order->order_number = self::generateOrderNumber();
            }
        });
    }

    protected $casts = [
        'total_amount' => 'decimal:2',
        'pickup_at' => 'datetime',
        'pickup_reminder_sent_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    private static function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'FS-'.now()->format('ymd').'-'.Str::upper(Str::random(5));
        } while (self::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}
