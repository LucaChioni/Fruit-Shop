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
        $sort = $request->string('sort', 'created_at')->toString();
        $sortDirection = $request->string('sort_direction', 'desc')->toString();

        $filters = [
            'search' => $request->string('search')->toString(),
            'status' => $request->string('status', 'all')->toString(),
            'sort' => in_array($sort, ['created_at', 'total_amount'], true) ? $sort : 'created_at',
            'sort_direction' => in_array($sortDirection, ['asc', 'desc'], true) ? $sortDirection : 'desc',
        ];

        $orders = $request->user()
            ->orders()
            ->when($filters['search'], fn ($query, $search) => $query->where(function ($query) use ($search) {
                $query->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('order_number', 'like', "%{$search}%");
            }))
            ->when($filters['status'] !== 'all', fn ($query) => $query->where('status', $filters['status']))
            ->orderBy($filters['sort'], $filters['sort_direction'])
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

        if ($order->user_id !== $user->id) {
            abort(403);
        }

        return Inertia::render('Orders/Show', [
            'order' => OrderData::detail($order),
        ]);
    }
}
