<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(): Response
    {
        $products = Product::query()
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Products/Index', [
            'products' => $products->map(fn (Product $product) => $this->productData($product)),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Products/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        Product::create($this->validatedProductData($request));

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Prodotto creato.');
    }

    public function edit(Product $product): Response
    {
        return Inertia::render('Admin/Products/Edit', [
            'product' => $this->productData($product),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $product->update($this->validatedProductData($request));

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Prodotto aggiornato.');
    }

    private function validatedProductData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0'],
            'unit_type' => ['required', 'string', 'max:50'],
            'is_active' => ['required', 'boolean'],
        ]);
    }

    private function productData(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'unit_type' => $product->unit_type,
            'is_active' => $product->is_active,
        ];
    }
}
