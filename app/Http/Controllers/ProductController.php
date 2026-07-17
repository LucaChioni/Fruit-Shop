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
        $category = $request->string('category', 'all')->toString();

        $filters = [
            'search' => $request->string('search')->toString(),
            'category' => in_array($category, ProductData::CATEGORIES, true) ? $category : 'all',
            'sort' => in_array($sort, ['name', 'price'], true) ? $sort : 'name',
            'sort_direction' => in_array($sortDirection, ['asc', 'desc'], true) ? $sortDirection : 'asc',
        ];

        $products = Product::query()
            ->where('is_active', true)
            ->when($filters['category'] !== 'all', fn ($query) => $query->where('category', $filters['category']))
            ->orderBy($filters['sort'], $filters['sort_direction'])
            ->get()
            ->filter(fn (Product $product) => ProductData::matchesTranslatedName($product, $filters['search']))
            ->values();

        $cartItems = $cartService->findCurrentCart($request)
            ?->items()
            ->get()
            ->keyBy('product_id')
            ?? collect();

        return Inertia::render('Products/Index', [
            'products' => $products->map(function (Product $product) use ($cartItems) {
                $cartItem = $cartItems->get($product->id);

                return [
                    ...ProductData::catalog($product),
                    'cart_quantity' => $cartItem?->quantity,
                    'cart_item_id' => $cartItem?->id,
                ];
            }),
            'filters' => $filters,
        ]);
    }
}
