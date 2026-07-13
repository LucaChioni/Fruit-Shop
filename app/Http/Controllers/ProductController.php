<?php

namespace App\Http\Controllers;

use App\Data\ProductData;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request, CartService $cartService): Response
    {
        $sort = $request->string('sort', 'name')->toString();
        $sortDirection = $request->string('sort_direction', 'asc')->toString();

        $filters = [
            'search' => $request->string('search')->toString(),
            'sort' => in_array($sort, ['name', 'price'], true) ? $sort : 'name',
            'sort_direction' => in_array($sortDirection, ['asc', 'desc'], true) ? $sortDirection : 'asc',
        ];

        $products = Product::query()
            ->where('is_active', true)
            ->orderBy($filters['sort'], $filters['sort_direction'])
            ->get()
            ->filter(fn (Product $product) => ProductData::matchesTranslatedName($product, $filters['search']))
            ->values();

        $cartQuantities = $cartService->findCurrentCart($request)
            ?->items()
            ->pluck('quantity', 'product_id')
            ?? collect();

        return Inertia::render('Products/Index', [
            'products' => $products->map(function (Product $product) use ($cartQuantities) {
                return [
                    ...ProductData::catalog($product),
                    'cart_quantity' => $cartQuantities->get($product->id),
                ];
            }),
            'filters' => $filters,
        ]);
    }
}
