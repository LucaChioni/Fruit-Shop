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
        $orders = Order::query()
            ->latest()
            ->get();

        return Inertia::render('Admin/Orders/Index', [
            'orders' => OrderData::collection($orders),
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
            'status.required' => 'Scegli lo stato dell\'ordine.',
            'status.in' => 'Lo stato selezionato non è valido.',
        ]);

        $order->update([
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Stato ordine aggiornato.');
    }
}
