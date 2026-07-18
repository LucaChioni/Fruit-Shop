<?php

namespace App\Http\Controllers;

use App\Mail\OrderPlaced;
use App\Models\Order;
use App\Models\User;
use App\Services\CartService;
use App\Services\PickupSchedule;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function create(Request $request, CartService $cartService, PickupSchedule $pickupSchedule)
    {
        $cart = $cartService->getCurrentCart($request);

        if ($cart->items()->doesntExist()) {
            return redirect()
                ->route('cart.index')
                ->with('error', __('ui.flash.cart_empty'));
        }

        return Inertia::render('Checkout/Create', [
            'pickupAtDefault' => $pickupSchedule->earliestPickupAt()->format('Y-m-d\TH:i'),
            'pickupAtMin' => now()->addHours(2)->format('Y-m-d\TH:i'),
            'pickupDateMax' => now()->addDays(369)->format('Y-m-d'),
            'closedPickupDates' => $pickupSchedule->closedDates(now()->startOfDay()),
        ]);
    }

    public function store(Request $request, CartService $cartService, PickupSchedule $pickupSchedule): RedirectResponse
    {
        $cart = $cartService->getCurrentCart($request);

        $cart->load('items.product');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', __('ui.flash.cart_empty'));
        }

        $validated = $request->validate([
            'pickup_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ], [
            'pickup_at.required' => __('ui.validation.pickup_required'),
            'pickup_at.date' => __('ui.validation.pickup_date'),
            'notes.max' => __('ui.validation.notes_max'),
        ]);

        $pickupAt = Carbon::parse($validated['pickup_at'])->seconds(0);
        $pickupSchedule->validate($pickupAt);

        $order = DB::transaction(function () use ($request, $validated, $cart, $pickupAt) {
            $totalAmount = 0;

            foreach ($cart->items as $item) {
                $totalAmount += $item->quantity * $item->product->price;
            }

            $order = Order::create([
                'user_id' => $request->user()->id,
                'status' => 'pending',
                'total_amount' => $totalAmount,
                'notes' => $validated['notes'] ?? null,
                'pickup_at' => $pickupAt,
            ]);

            foreach ($cart->items as $item) {
                $product = $item->product;
                $lineTotal = $item->quantity * $product->price;

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_name_en' => $product->name_en,
                    'unit_type' => $product->unit_type,
                    'unit_price' => $product->price,
                    'quantity' => $item->quantity,
                    'line_total' => $lineTotal,
                ]);
            }

            $cart->items()->delete();

            return $order;
        });

        $adminRecipient = config('mail.order_notifications.address');
        $customerRecipient = $request->user()->email;

        if ($adminRecipient) {
            $adminLocale = User::query()
                ->where('email', $adminRecipient)
                ->where('is_admin', true)
                ->value('locale') ?? 'it';

            Mail::to($adminRecipient)->locale($adminLocale)->send(new OrderPlaced($order));
        }

        if ($customerRecipient !== $adminRecipient) {
            Mail::to($customerRecipient)->locale(app()->getLocale())->send(new OrderPlaced($order));
        }

        return redirect()
            ->route('orders.show', $order)
            ->with('success', __('ui.flash.order_created'));
    }
}
