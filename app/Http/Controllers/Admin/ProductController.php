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
        $sort = request()->string('sort', 'name')->toString();
        $sortDirection = request()->string('sort_direction', 'asc')->toString();

        $filters = [
            'search' => request()->string('search')->toString(),
            'status' => request()->string('status', 'all')->toString(),
            'sort' => in_array($sort, ['name', 'price', 'created_at'], true) ? $sort : 'name',
            'sort_direction' => in_array($sortDirection, ['asc', 'desc'], true) ? $sortDirection : 'asc',
        ];

        $products = Product::query()
            ->when($filters['status'] === 'active', fn ($query) => $query->where('is_active', true))
            ->when($filters['status'] === 'inactive', fn ($query) => $query->where('is_active', false))
            ->orderBy($filters['sort'], $filters['sort_direction'])
            ->get()
            ->filter(fn (Product $product) => ProductData::matchesTranslatedName($product, $filters['search']))
            ->values();

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
            ->with('success', __('ui.flash.product_created'));
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
            ->with('success', __('ui.flash.product_updated'));
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->deleteStoredProductImage($product);

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', __('ui.flash.product_deleted'));
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
            'name.required' => __('ui.validation.product_name_required'),
            'name.max' => __('ui.validation.product_name_max'),
            'description.max' => __('ui.validation.product_description_max'),
            'image.image' => __('ui.validation.image_valid'),
            'image.max' => __('ui.validation.image_max'),
            'price.required' => __('ui.validation.price_required'),
            'price.numeric' => __('ui.validation.price_numeric'),
            'price.min' => __('ui.validation.price_min'),
            'unit_type.required' => __('ui.validation.unit_type_required'),
            'unit_type.in' => __('ui.validation.unit_type_in'),
            'is_active.required' => __('ui.validation.is_active_required'),
            'is_active.boolean' => __('ui.validation.is_active_boolean'),
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
            'name' => ProductData::translatedName($product),
            'source_name' => $product->name,
            'description' => ProductData::translatedDescription($product),
            'source_description' => $product->description,
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
