<?php

namespace App\Http\Controllers;

use App\Data\ProductData;
use App\Services\CartService;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
                'product_image_url' => $product->image_url,
                'unit_type' => $product->unit_type,
                'unit_price' => number_format((float) $product->price, 2, '.', ''),
                'quantity' => $item->quantity,
                'quantity_step' => ProductData::quantityStep($product->unit_type),
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
        ], [
            'quantities.required' => 'Inserisci almeno una quantità da aggiornare.',
            'quantities.array' => 'Le quantità inviate non sono valide.',
            'quantities.*.required' => 'Inserisci una quantità per ogni prodotto.',
            'quantities.*.numeric' => 'La quantità deve essere un numero.',
            'quantities.*.min' => 'La quantità minima è 0,01.',
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
