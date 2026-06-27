<?php

namespace App\Http\Controllers;

use App\Data\OrderData;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->latest()
            ->get();

        return Inertia::render('Orders/Index', [
            'orders' => OrderData::collection($orders),
        ]);
    }

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
            'order' => OrderData::detail($order),
        ]);
    }
}
