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
        $filters = [
            'search' => $request->string('search')->toString(),
            'status' => $request->string('status', 'all')->toString(),
            'sort' => $request->string('sort', 'newest')->toString(),
        ];

        $orders = $request->user()
            ->orders()
            ->when($filters['search'], fn ($query, $search) => $query->where(function ($query) use ($search) {
                $query->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('order_number', 'like', "%{$search}%");
            }))
            ->when($filters['status'] !== 'all', fn ($query) => $query->where('status', $filters['status']))
            ->when($filters['sort'] === 'oldest', fn ($query) => $query->oldest())
            ->when($filters['sort'] === 'total_desc', fn ($query) => $query->orderByDesc('total_amount'))
            ->when($filters['sort'] === 'total_asc', fn ($query) => $query->orderBy('total_amount'))
            ->when(! in_array($filters['sort'], ['oldest', 'total_desc', 'total_asc'], true), fn ($query) => $query->latest())
            ->get();

        return Inertia::render('Orders/Index', [
            'orders' => OrderData::collection($orders),
            'filters' => $filters,
            'orderStatuses' => OrderData::STATUSES,
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
