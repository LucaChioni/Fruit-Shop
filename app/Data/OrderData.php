<?php

namespace App\Data;

use App\Models\Order;
use Illuminate\Support\Collection;

class OrderData
{
    public const STATUSES = [
        'pending',
        'ready',
        'completed',
        'cancelled',
    ];

    public static function collection(Collection $orders): Collection
    {
        return $orders->map(fn (Order $order) => self::summary($order));
    }

    public static function summary(Order $order): array
    {
        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'customer_name' => $order->customer_name,
            'customer_email' => $order->user?->email,
            'customer_type' => $order->user_id ? 'registered' : 'guest',
            'status' => $order->status,
            'total_amount' => $order->total_amount,
            'pickup_at' => $order->pickup_at?->format('d/m/Y H:i'),
            'created_at' => $order->created_at->format('d/m/Y H:i'),
        ];
    }

    public static function detail(Order $order): array
    {
        return array_merge(self::summary($order), [
            'notes' => $order->notes,
            'items' => $order->items->map(fn ($item) => [
                'id' => $item->id,
                'product_name' => ProductData::translatedName($item->product_name),
                'unit_type' => ProductData::translatedUnitType($item->unit_type),
                'unit_price' => $item->unit_price,
                'quantity' => $item->quantity,
                'line_total' => $item->line_total,
            ]),
        ]);
    }
}
