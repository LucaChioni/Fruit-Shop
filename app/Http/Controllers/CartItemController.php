<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartItemController extends Controller
{
    public function store(Request $request, CartService $cartService): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
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
}
