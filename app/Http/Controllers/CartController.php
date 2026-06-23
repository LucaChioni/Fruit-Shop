<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\CartService;

class CartController extends Controller
{
    public function index(Request $request, CartService $cartService)
    {
        $cart = $cartService->getCurrentCart($request);
        $cart->load('items.product');
        $items = $cart->items->map(function ($item) {
            $product = $item->product;
            return [
                'id' => $item->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'unit_type' => $product->unit_type,
                'unit_price' => $product->price,
                'quantity' => $item->quantity,
                'line_total' => number_format($item->quantity * $product->price, 2, '.', ''),
            ];
        });
        $total = number_format($items->sum(function ($item) {
            return $item['line_total'];
        }), 2, '.', '');

        return Inertia::render('Cart/Index', [
            'cartId' => $cart->id,
            'items' => $items,
            'total' => $total,
        ]);
    }

    public function update(Request $request, CartService $cartService)
    {
        $validated = $request->validate([
            'quantities' => ['required', 'array'],
            'quantities.*' => ['required', 'numeric', 'min:0.01'],
        ]);

        $cart = $cartService->getCurrentCart($request);

        foreach ($validated['quantities'] as $cartItemId => $quantity) {
            $cart->items()
                ->where('id', $cartItemId)
                ->update([
                    'quantity' => $quantity,
                ]);
        }

        return redirect()
            ->route('cart.index')
            ->with('success', 'Carrello aggiornato.');
    }
}
