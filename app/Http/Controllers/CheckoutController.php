<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\Order;
use App\Services\CartService;

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
        ]);
    }

    public function store(Request $request, CartService $cartService): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ], [
            'customer_name.required' => 'Inserisci il nome per il ritiro.',
            'customer_name.max' => 'Il nome non può superare 255 caratteri.',
            'notes.max' => 'Le note non possono superare 2000 caratteri.',
        ]);

        $cart = $cartService->getCurrentCart($request);

        $cart->load('items.product');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Il carrello è vuoto.');
        }

        $order = DB::transaction(function () use ($request, $validated, $cart) {
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

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Ordine creato con successo.');
    }
}
