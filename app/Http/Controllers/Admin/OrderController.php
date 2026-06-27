<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::query()
            ->latest()
            ->get();

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'customer_name' => $order->customer_name,
                    'status' => $order->status,
                    'total_amount' => $order->total_amount,
                    'created_at' => $order->created_at->format('d/m/Y H:i'),
                ];
            }),
        ]);
    }

    public function show(Order $order)
    {
        $order->load('items');

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
            'isAdminView' => true,
        ]);
    }
}
