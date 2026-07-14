<?php

namespace App\Http\Controllers;

use App\Data\ProductData;
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
            'product_id.required' => __('ui.validation.product_required'),
            'product_id.exists' => __('ui.validation.product_exists'),
            'quantity.required' => __('ui.validation.quantity_required'),
            'quantity.numeric' => __('ui.validation.quantity_numeric'),
            'quantity.min' => __('ui.validation.quantity_min'),
        ]);

        $product = Product::query()
            ->where('is_active', true)
            ->findOrFail($validated['product_id']);

        if (ProductData::requiresWholeQuantity($product->unit_type) && floor((float) $validated['quantity']) !== (float) $validated['quantity']) {
            return back()->withErrors([
                'quantity' => __('ui.validation.quantity_integer'),
            ])->withInput();
        }

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

        return redirect()->route('products.index');
    }

    public function destroy(Request $request, CartService $cartService, CartItem $cartItem): RedirectResponse
    {
        $cart = $cartService->getCurrentCart($request);

        if ($cartItem->cart_id !== $cart->id) {
            abort(403);
        }

        $cartItem->delete();

        return redirect()->route('cart.index');
    }
}
