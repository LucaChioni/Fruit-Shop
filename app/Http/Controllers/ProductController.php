<?php

namespace App\Http\Controllers;

use App\Data\ProductData;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = [
            'search' => $request->string('search')->toString(),
            'sort' => $request->string('sort', 'name')->toString(),
        ];

        $products = Product::query()
            ->where('is_active', true)
            ->when($filters['search'], fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->when($filters['sort'] === 'price_asc', fn ($query) => $query->orderBy('price'))
            ->when($filters['sort'] === 'price_desc', fn ($query) => $query->orderByDesc('price'))
            ->when(! in_array($filters['sort'], ['price_asc', 'price_desc'], true), fn ($query) => $query->orderBy('name'))
            ->get();

        return Inertia::render('Products/Index', [
            'products' => $products->map(fn (Product $product) => ProductData::catalog($product)),
            'filters' => $filters,
        ]);
    }
}
