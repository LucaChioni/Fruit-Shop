<?php

namespace App\Http\Controllers;

use App\Mail\OrderPlaced;
use App\Models\Order;
use App\Services\CartService;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function create(Request $request, CartService $cartService)
    {
        $cart = $cartService->getCurrentCart($request);

        if ($cart->items()->doesntExist()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Il carrello è vuoto.');
        }

        return Inertia::render('Checkout/Create', [
            'customerName' => $request->user()?->name ?? '',
            'pickupAtDefault' => $this->earliestPickupAt()->format('Y-m-d\TH:i'),
            'pickupAtMin' => now()->addHours(2)->format('Y-m-d\TH:i'),
            'pickupAtMax' => now()->addDay()->setTime(19, 30)->format('Y-m-d\TH:i'),
        ]);
    }

    public function store(Request $request, CartService $cartService): RedirectResponse
    {
        $cart = $cartService->getCurrentCart($request);

        $cart->load('items.product');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Il carrello è vuoto.');
        }

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'pickup_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ], [
            'customer_name.required' => 'Inserisci il nome per il ritiro.',
            'customer_name.max' => 'Il nome non può superare 255 caratteri.',
            'pickup_at.required' => 'Scegli data e ora di ritiro.',
            'pickup_at.date' => 'Scegli una data e ora di ritiro valida.',
            'notes.max' => 'Le note non possono superare 2000 caratteri.',
        ]);

        $pickupAt = Carbon::parse($validated['pickup_at'])->seconds(0);
        $this->validatePickupAt($pickupAt);

        $order = DB::transaction(function () use ($request, $validated, $cart, $pickupAt) {
            $totalAmount = 0;

            foreach ($cart->items as $item) {
                $totalAmount += $item->quantity * $item->product->price;
            }

            $order = Order::create([
                'user_id' => $request->user()?->id,
                'customer_name' => $validated['customer_name'],
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
                    'unit_type' => $product->unit_type,
                    'unit_price' => $product->price,
                    'quantity' => $item->quantity,
                    'line_total' => $lineTotal,
                ]);
            }

            $cart->items()->delete();

            return $order;
        });

        $request->session()->put('last_order_id', $order->id);

        $recipients = collect([
            config('mail.order_notifications.address'),
            $request->user()?->email,
        ])->filter()->unique();

        foreach ($recipients as $recipient) {
            Mail::to($recipient)->send(new OrderPlaced($order));
        }

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Ordine creato con successo.');
    }

    private function earliestPickupAt(): CarbonInterface
    {
        $pickupAt = now()->addHours(2)->seconds(0);
        $minutes = ($pickupAt->hour * 60) + $pickupAt->minute;

        if ($minutes < 11 * 60) {
            return $pickupAt->setTime(11, 0);
        }

        if ($minutes > 13 * 60 && $minutes < 16 * 60) {
            return $pickupAt->setTime(16, 0);
        }

        if ($minutes > (19 * 60) + 30) {
            return $pickupAt->addDay()->setTime(11, 0);
        }

        return $pickupAt;
    }

    private function validatePickupAt(CarbonInterface $pickupAt): void
    {
        $minimum = now()->addHours(2);
        $maximum = now()->addDay()->endOfDay();
        $minutes = ($pickupAt->hour * 60) + $pickupAt->minute;
        $isOpen = ($minutes >= 11 * 60 && $minutes <= 13 * 60)
            || ($minutes >= 16 * 60 && $minutes <= (19 * 60) + 30);

        if ($pickupAt->lt($minimum)) {
            throw ValidationException::withMessages([
                'pickup_at' => 'Il ritiro deve essere almeno 2 ore dopo l\'ordine.',
            ]);
        }

        if ($pickupAt->gt($maximum)) {
            throw ValidationException::withMessages([
                'pickup_at' => 'Il ritiro può essere al massimo entro il giorno successivo.',
            ]);
        }

        if (! $isOpen) {
            throw ValidationException::withMessages([
                'pickup_at' => 'Scegli un orario di ritiro tra 11:00-13:00 o 16:00-19:30.',
            ]);
        }
    }
}
