<?php

namespace App\Http\Controllers;

use App\Data\OrderData;
use App\Models\Order;
use App\Services\PickupSchedule;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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
            ->with('user')
            ->when($filters['search'], fn ($query, $search) => $query->where(function ($query) use ($search) {
                $query->whereHas('user', fn ($query) => $query->where('name', 'like', "%{$search}%"))
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

    public function show(Request $request, Order $order, PickupSchedule $pickupSchedule)
    {
        $order->load('items.product', 'user');
        $this->ensureOwner($request, $order);

        return Inertia::render('Orders/Show', [
            'order' => OrderData::detail($order),
            'pickupAtMin' => now()->addHours(2)->format('Y-m-d\TH:i'),
            'pickupDateMax' => now()->addDays(369)->format('Y-m-d'),
            'closedPickupDates' => $pickupSchedule->closedDates(now()->startOfDay()),
        ]);
    }

    public function updatePickup(Request $request, Order $order, PickupSchedule $pickupSchedule): RedirectResponse
    {
        $this->ensureOwner($request, $order);
        $this->ensurePending($order);
        $this->ensurePickupCanBeUpdated($order);

        $validated = $request->validate([
            'pickup_at' => ['required', 'date'],
        ], [
            'pickup_at.required' => __('ui.validation.pickup_required'),
            'pickup_at.date' => __('ui.validation.pickup_date'),
        ]);

        $pickupAt = Carbon::parse($validated['pickup_at'])->seconds(0);
        $pickupSchedule->validate($pickupAt);

        $order->update([
            'pickup_at' => $pickupAt,
            'pickup_reminder_sent_at' => null,
        ]);

        return redirect()
            ->route('orders.show', $order)
            ->with('success', __('ui.flash.order_pickup_updated'));
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        $this->ensureOwner($request, $order);
        $this->ensurePending($order);

        $order->update(['status' => 'cancelled']);

        return redirect()
            ->route('orders.show', $order)
            ->with('success', __('ui.flash.order_cancelled'));
    }

    private function ensureOwner(Request $request, Order $order): void
    {
        if ($order->user_id !== $request->user()->id) {
            abort(403);
        }
    }

    private function ensurePending(Order $order): void
    {
        if ($order->status !== 'pending') {
            throw ValidationException::withMessages([
                'order' => __('ui.validation.order_not_editable'),
            ]);
        }
    }

    private function ensurePickupCanBeUpdated(Order $order): void
    {
        if ($order->pickup_reminder_sent_at !== null) {
            throw ValidationException::withMessages([
                'pickup_at' => __('ui.validation.pickup_reminder_sent'),
            ]);
        }
    }
}
