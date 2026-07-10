<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Feature\Concerns\CreatesShopModels;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use CreatesShopModels;
    use RefreshDatabase;

    public function test_admin_products_index_shows_active_and_inactive_products(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->createProduct(['name' => 'Zucchine', 'image_url' => 'https://example.com/zucchine.jpg']);
        $this->createProduct(['name' => 'Arance', 'is_active' => false]);

        $response = $this->actingAs($admin)->get(route('admin.products.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Products/Index')
                ->has('products', 2)
                ->where('products.0.name', 'Arance')
                ->where('products.0.is_active', false)
                ->where('products.1.name', 'Zucchine')
                ->where('products.1.image_url', 'https://example.com/zucchine.jpg')
                ->where('products.1.is_active', true)
                ->where('filters.search', '')
                ->where('filters.status', 'all')
                ->where('filters.sort', 'name'));
    }

    public function test_admin_products_index_can_be_filtered_and_sorted(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->createProduct(['name' => 'Arance Navel', 'price' => 3.20, 'is_active' => false]);
        $this->createProduct(['name' => 'Arance Tarocco', 'price' => 2.80, 'is_active' => false]);
        $this->createProduct(['name' => 'Zucchine', 'price' => 1.90, 'is_active' => true]);

        $response = $this->actingAs($admin)->get(route('admin.products.index', [
            'search' => 'Arance',
            'status' => 'inactive',
            'sort' => 'price_asc',
        ]));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Products/Index')
                ->has('products', 2)
                ->where('products.0.name', 'Arance Tarocco')
                ->where('products.1.name', 'Arance Navel')
                ->where('filters.search', 'Arance')
                ->where('filters.status', 'inactive')
                ->where('filters.sort', 'price_asc'));
    }

    public function test_admin_products_index_filters_by_translated_name_for_current_locale(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->createProduct(['name' => 'Mele Golden']);
        $this->createProduct(['name' => 'Lattuga']);

        $this->actingAs($admin)
            ->withSession(['locale' => 'en'])
            ->get(route('admin.products.index', ['search' => 'Lettuce']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Products/Index')
                ->has('products', 1)
                ->where('products.0.name', 'Lettuce')
                ->where('filters.search', 'Lettuce'));

        $this->actingAs($admin)
            ->withSession(['locale' => 'it'])
            ->get(route('admin.products.index', ['search' => 'Lattuga']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Products/Index')
                ->has('products', 1)
                ->where('products.0.name', 'Lattuga')
                ->where('filters.search', 'Lattuga'));
    }

    public function test_non_admin_cannot_access_admin_products_index(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.products.index'))
            ->assertForbidden();
    }

    public function test_admin_can_open_create_product_page(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get(route('admin.products.create'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Products/Create')
                ->where('unitTypes', ['kg', 'pz', 'g', 'vaschetta']));
    }

    public function test_admin_can_create_product(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Storage::fake('public');

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.products.store'), [
                'name' => 'Fragole',
                'description' => 'Vaschetta di fragole fresche.',
                'image' => UploadedFile::fake()->image('fragole.jpg'),
                'price' => 3.80,
                'unit_type' => 'vaschetta',
                'is_active' => true,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.products.index'));

        $product = Product::firstOrFail();

        $this->assertSame('Fragole', $product->name);
        $this->assertStringContainsString('/storage/products/', $product->image_url);
        Storage::disk('public')->assertExists(
            substr(parse_url($product->image_url, PHP_URL_PATH), strlen('/storage/'))
        );
        $this->assertSame('3.80', $product->price);
        $this->assertSame('vaschetta', $product->unit_type);
        $this->assertTrue($product->is_active);
    }

    public function test_admin_can_open_edit_product_page(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $product = $this->createProduct(['name' => 'Mele']);

        $response = $this->actingAs($admin)->get(route('admin.products.edit', $product));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Products/Edit')
                ->where('product.id', $product->id)
                ->where('product.name', 'Mele')
                ->where('unitTypes', ['kg', 'pz', 'g', 'vaschetta']));
    }

    public function test_admin_can_update_and_deactivate_product(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $product = $this->createProduct(['name' => 'Mele', 'is_active' => true]);
        Storage::fake('public');

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.products.update', $product), [
                '_method' => 'patch',
                'name' => 'Mele Golden',
                'description' => 'Mele aggiornate.',
                'image' => UploadedFile::fake()->image('mele.jpg'),
                'price' => 2.90,
                'unit_type' => 'kg',
                'is_active' => false,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.products.index'));

        $product->refresh();

        $this->assertSame('Mele Golden', $product->name);
        $this->assertSame('Mele aggiornate.', $product->description);
        $this->assertStringContainsString('/storage/products/', $product->image_url);
        Storage::disk('public')->assertExists(
            substr(parse_url($product->image_url, PHP_URL_PATH), strlen('/storage/'))
        );
        $this->assertSame('2.90', $product->price);
        $this->assertFalse($product->is_active);
    }

    public function test_admin_can_remove_product_image(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Storage::fake('public');
        Storage::disk('public')->put('products/mele.jpg', 'image');
        $product = $this->createProduct([
            'name' => 'Mele',
            'image_url' => Storage::disk('public')->url('products/mele.jpg'),
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.products.update', $product), [
                '_method' => 'patch',
                'name' => 'Mele',
                'description' => $product->description,
                'remove_image' => true,
                'price' => $product->price,
                'unit_type' => $product->unit_type,
                'is_active' => $product->is_active,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.products.index'));

        $product->refresh();

        $this->assertNull($product->image_url);
        Storage::disk('public')->assertMissing('products/mele.jpg');
    }

    public function test_admin_can_delete_product(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $product = $this->createProduct(['name' => 'Mele']);

        $response = $this
            ->actingAs($admin)
            ->delete(route('admin.products.destroy', $product));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_non_admin_cannot_delete_product(): void
    {
        $user = User::factory()->create();
        $product = $this->createProduct(['name' => 'Mele']);

        $this
            ->actingAs($user)
            ->delete(route('admin.products.destroy', $product))
            ->assertForbidden();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
        ]);
    }

    public function test_product_unit_type_must_be_valid(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.products.store'), [
                'name' => 'Prodotto test',
                'description' => null,
                'price' => 1.50,
                'unit_type' => 'cassetta',
                'is_active' => true,
            ]);

        $response->assertSessionHasErrors([
            'unit_type' => 'Scegli un\'unità di misura valida.',
        ]);

        $this->assertSame(0, Product::count());
    }
}
