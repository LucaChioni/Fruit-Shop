<?php

namespace App\Http\Controllers\Admin;

use App\Data\OrderData;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        $sort = request()->string('sort', 'created_at')->toString();
        $sortDirection = request()->string('sort_direction', 'desc')->toString();

        $filters = [
            'search' => request()->string('search')->toString(),
            'status' => request()->string('status', 'all')->toString(),
            'sort' => in_array($sort, ['created_at', 'total_amount'], true) ? $sort : 'created_at',
            'sort_direction' => in_array($sortDirection, ['asc', 'desc'], true) ? $sortDirection : 'desc',
        ];

        $orders = Order::query()
            ->with('user')
            ->when($filters['search'], fn ($query, $search) => $query->where(function ($query) use ($search) {
                $query->whereHas('user', fn ($query) => $query->where('name', 'like', "%{$search}%"))
                    ->orWhere('order_number', 'like', "%{$search}%");
            }))
            ->when($filters['status'] !== 'all', fn ($query) => $query->where('status', $filters['status']))
            ->orderBy($filters['sort'], $filters['sort_direction'])
            ->get();

        return Inertia::render('Admin/Orders/Index', [
            'orders' => OrderData::collection($orders),
            'filters' => $filters,
            'orderStatuses' => OrderData::STATUSES,
        ]);
    }

    public function show(Order $order)
    {
        $order->load('items.product', 'user');

        return Inertia::render('Orders/Show', [
            'order' => OrderData::detail($order),
            'isAdminView' => true,
            'orderStatuses' => OrderData::STATUSES,
        ]);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(OrderData::STATUSES)],
        ], [
            'status.required' => __('ui.validation.status_required'),
            'status.in' => __('ui.validation.status_in'),
        ]);

        $order->update([
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.orders.show', $order);
    }
}
