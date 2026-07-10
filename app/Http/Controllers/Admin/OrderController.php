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
        $filters = [
            'search' => request()->string('search')->toString(),
            'status' => request()->string('status', 'all')->toString(),
            'customer_type' => request()->string('customer_type', 'all')->toString(),
            'sort' => request()->string('sort', 'newest')->toString(),
        ];

        $orders = Order::query()
            ->with('user')
            ->when($filters['search'], fn ($query, $search) => $query->where(function ($query) use ($search) {
                $query->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('order_number', 'like', "%{$search}%");
            }))
            ->when($filters['status'] !== 'all', fn ($query) => $query->where('status', $filters['status']))
            ->when($filters['customer_type'] === 'registered', fn ($query) => $query->whereNotNull('user_id'))
            ->when($filters['customer_type'] === 'guest', fn ($query) => $query->whereNull('user_id'))
            ->when($filters['sort'] === 'oldest', fn ($query) => $query->oldest())
            ->when($filters['sort'] === 'total_desc', fn ($query) => $query->orderByDesc('total_amount'))
            ->when($filters['sort'] === 'total_asc', fn ($query) => $query->orderBy('total_amount'))
            ->when(! in_array($filters['sort'], ['oldest', 'total_desc', 'total_asc'], true), fn ($query) => $query->latest())
            ->get();

        return Inertia::render('Admin/Orders/Index', [
            'orders' => OrderData::collection($orders),
            'filters' => $filters,
            'orderStatuses' => OrderData::STATUSES,
        ]);
    }

    public function show(Order $order)
    {
        $order->load('items');

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

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', __('ui.flash.order_status_updated'));
    }
}
