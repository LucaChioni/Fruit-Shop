<?php

namespace App\Http\Controllers\Admin;

use App\Data\ProductData;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(): Response
    {
        $filters = [
            'search' => request()->string('search')->toString(),
            'status' => request()->string('status', 'all')->toString(),
            'sort' => request()->string('sort', 'name')->toString(),
        ];

        $products = Product::query()
            ->when($filters['search'], fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->when($filters['status'] === 'active', fn ($query) => $query->where('is_active', true))
            ->when($filters['status'] === 'inactive', fn ($query) => $query->where('is_active', false))
            ->when($filters['sort'] === 'price_asc', fn ($query) => $query->orderBy('price'))
            ->when($filters['sort'] === 'price_desc', fn ($query) => $query->orderByDesc('price'))
            ->when($filters['sort'] === 'newest', fn ($query) => $query->latest())
            ->when(! in_array($filters['sort'], ['price_asc', 'price_desc', 'newest'], true), fn ($query) => $query->orderBy('name'))
            ->get();

        return Inertia::render('Admin/Products/Index', [
            'products' => $products->map(fn (Product $product) => $this->productData($product)),
            'filters' => $filters,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Products/Create', [
            'unitTypes' => ProductData::UNIT_TYPES,
        ]);
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
            'unitTypes' => ProductData::UNIT_TYPES,
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validatedProductData($request);

        if (array_key_exists('image_url', $data)) {
            $this->deleteStoredProductImage($product);
        }

        $product->update($data);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Prodotto aggiornato.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->deleteStoredProductImage($product);

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Prodotto eliminato.');
    }

    private function validatedProductData(Request $request): array
    {
        $request->merge([
            'remove_image' => $request->boolean('remove_image'),
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'image', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
            'price' => ['required', 'numeric', 'min:0'],
            'unit_type' => ['required', Rule::in(ProductData::UNIT_TYPES)],
            'is_active' => ['required', 'boolean'],
        ], [
            'name.required' => 'Inserisci il nome del prodotto.',
            'name.max' => 'Il nome del prodotto non può superare 255 caratteri.',
            'description.max' => 'La descrizione non può superare 2000 caratteri.',
            'image.image' => 'Carica un file immagine valido.',
            'image.max' => 'L\'immagine non può superare 2 MB.',
            'price.required' => 'Inserisci il prezzo del prodotto.',
            'price.numeric' => 'Il prezzo deve essere un numero.',
            'price.min' => 'Il prezzo non può essere negativo.',
            'unit_type.required' => 'Scegli l\'unità di misura.',
            'unit_type.in' => 'Scegli un\'unità di misura valida.',
            'is_active.required' => 'Indica se il prodotto è attivo.',
            'is_active.boolean' => 'Il valore attivo/non attivo non è valido.',
        ]);

        unset($data['image']);
        $removeImage = (bool) ($data['remove_image'] ?? false);
        unset($data['remove_image']);

        if ($removeImage) {
            $data['image_url'] = null;
        } elseif ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
            $disk = Storage::disk('public');
            $data['image_url'] = $disk->url($path);
        }

        return $data;
    }

    private function productData(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'image_url' => $product->image_url,
            'price' => $product->price,
            'unit_type' => $product->unit_type,
            'is_active' => $product->is_active,
        ];
    }

    private function deleteStoredProductImage(Product $product): void
    {
        $path = parse_url((string) $product->image_url, PHP_URL_PATH);

        if (! is_string($path) || ! str_starts_with($path, '/storage/')) {
            return;
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        $disk->delete(substr($path, strlen('/storage/')));
    }
}
