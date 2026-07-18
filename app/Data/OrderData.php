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
            'customer_name' => $order->user->name,
            'customer_email' => $order->user?->email,
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
            'pickup_at_input' => $order->pickup_at?->format('Y-m-d\TH:i'),
            'items' => $order->items->map(fn ($item) => [
                'id' => $item->id,
                'product_name' => ProductData::translatedName(
                    $item->product_name,
                    $item->product_name_en ?? $item->product?->name_en,
                ),
                'unit_type' => ProductData::translatedUnitType($item->unit_type, $item->quantity),
                'unit_price' => $item->unit_price,
                'quantity' => ProductData::displayQuantity($item->quantity, $item->unit_type),
                'line_total' => $item->line_total,
            ]),
        ]);
    }
}
