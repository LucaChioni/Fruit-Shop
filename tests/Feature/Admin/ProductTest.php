<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $this->createProduct(['name' => 'Zucchine']);
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
                ->where('products.1.is_active', true));
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
                ->component('Admin/Products/Create'));
    }

    public function test_admin_can_create_product(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.products.store'), [
                'name' => 'Fragole',
                'description' => 'Vaschetta di fragole fresche.',
                'price' => 3.80,
                'unit_type' => 'vaschetta',
                'is_active' => true,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.products.index'));

        $product = Product::firstOrFail();

        $this->assertSame('Fragole', $product->name);
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
                ->where('product.name', 'Mele'));
    }

    public function test_admin_can_update_and_deactivate_product(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $product = $this->createProduct(['name' => 'Mele', 'is_active' => true]);

        $response = $this
            ->actingAs($admin)
            ->patch(route('admin.products.update', $product), [
                'name' => 'Mele Golden',
                'description' => 'Mele aggiornate.',
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
        $this->assertSame('2.90', $product->price);
        $this->assertFalse($product->is_active);
    }
}
