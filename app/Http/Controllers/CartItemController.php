<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CartItem;
use App\Services\CartService;

class CartItemController extends Controller
{
    public function store(Request $request, CartService $cartService): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
        ], [
            'product_id.required' => 'Seleziona un prodotto da aggiungere al carrello.',
            'product_id.exists' => 'Il prodotto selezionato non esiste.',
            'quantity.required' => 'Inserisci la quantità.',
            'quantity.numeric' => 'La quantità deve essere un numero.',
            'quantity.min' => 'La quantità minima è 0,01.',
        ]);

        $product = Product::query()
            ->where('is_active', true)
            ->findOrFail($validated['product_id']);

        $cart = $cartService->getCurrentCart($request);

        $cartItem = $cart->items()
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $cartItem->quantity + $validated['quantity'],
            ]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $validated['quantity'],
            ]);
        }

        return redirect()
            ->route('products.index')
            ->with('success', 'Prodotto aggiunto al carrello.');
    }

    public function destroy(Request $request, CartService $cartService, CartItem $cartItem): RedirectResponse
    {
        $cart = $cartService->getCurrentCart($request);

        if ($cartItem->cart_id !== $cart->id) {
            abort(403);
        }

        $cartItem->delete();

        return redirect()
            ->route('cart.index')
            ->with('success', 'Prodotto rimosso dal carrello.');
    }
}
