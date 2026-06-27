<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Order;

class OrderController extends Controller
{
    public function show(Request $request, Order $order)
    {
        $order->load('items');
        $user = $request->user();

        if ($order->user_id !== null) {
            if (! $user || $order->user_id !== $user->id) {
                abort(403);
            }
        } else {
            if ($request->session()->get('last_order_id') !== $order->id) {
                abort(403);
            }
        }

        return Inertia::render('Orders/Show', [
            'order' => [
                'id' => $order->id,
                'customer_name' => $order->customer_name,
                'status' => $order->status,
                'total_amount' => $order->total_amount,
                'notes' => $order->notes,
                'created_at' => $order->created_at->format('d/m/Y H:i'),
                'items' => $order->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_name' => $item->product_name,
                        'unit_type' => $item->unit_type,
                        'unit_price' => $item->unit_price,
                        'quantity' => $item->quantity,
                        'line_total' => $item->line_total,
                    ];
                }),
            ],
        ]);
    }
}
