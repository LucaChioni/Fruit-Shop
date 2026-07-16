<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Feature\Concerns\CreatesShopModels;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use CreatesShopModels;
    use RefreshDatabase;

    public function test_products_index_shows_only_active_products_ordered_by_name(): void
    {
        $this->createProduct(['name' => 'Zucchine']);
        $this->createProduct(['name' => 'Arance', 'price' => 2.5, 'unit_type' => 'kg', 'image_url' => 'https://example.com/arance.jpg']);
        $this->createProduct(['name' => 'Banane', 'is_active' => false]);

        $response = $this->get(route('products.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Products/Index')
                ->has('products', 2)
                ->where('products.0.name', 'Arance')
                ->where('products.0.image_url', 'https://example.com/arance.jpg')
                ->where('products.0.price', '2.50')
                ->where('products.0.quantity_step', 0.1)
                ->where('products.1.name', 'Zucchine')
                ->where('filters.search', '')
                ->where('filters.sort', 'name')
                ->where('filters.sort_direction', 'asc'));
    }

    public function test_products_index_can_be_filtered_and_sorted(): void
    {
        $this->createProduct(['name' => 'Arance Navel', 'price' => 3.20]);
        $this->createProduct(['name' => 'Arance Tarocco', 'price' => 2.80]);
        $this->createProduct(['name' => 'Zucchine', 'price' => 1.90]);

        $response = $this->get(route('products.index', [
            'search' => 'Arance',
            'sort' => 'price',
            'sort_direction' => 'desc',
        ]));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Products/Index')
                ->has('products', 2)
                ->where('products.0.name', 'Arance Navel')
                ->where('products.1.name', 'Arance Tarocco')
                ->where('filters.search', 'Arance')
                ->where('filters.sort', 'price')
                ->where('filters.sort_direction', 'desc'));
    }

    public function test_products_index_shows_current_cart_quantity(): void
    {
        $user = User::factory()->create();
        $product = $this->createProduct(['name' => 'Arance', 'unit_type' => 'kg']);
        $cart = $this->createCart(['user_id' => $user->id]);
        $cartItem = $this->createCartItem($cart, $product, 1.5);

        $this->actingAs($user)
            ->get(route('products.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Products/Index')
                ->where('products.0.id', $product->id)
                ->where('products.0.cart_quantity', '1.50')
                ->where('products.0.cart_item_id', $cartItem->id));
    }

    public function test_products_index_filters_by_translated_name_for_current_locale(): void
    {
        $this->createProduct([
            'name' => 'Mele Golden',
            'name_en' => 'Golden apples',
            'description_en' => 'Sweet and crunchy apples.',
        ]);
        $this->createProduct(['name' => 'Lattuga']);

        $this->withSession(['locale' => 'en'])
            ->get(route('products.index', ['search' => 'Golden']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Products/Index')
                ->has('products', 1)
                ->where('products.0.name', 'Golden apples')
                ->where('products.0.description', 'Sweet and crunchy apples.')
                ->where('filters.search', 'Golden'));

        $this->withSession(['locale' => 'it'])
            ->get(route('products.index', ['search' => 'Mele']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Products/Index')
                ->has('products', 1)
                ->where('products.0.name', 'Mele Golden')
                ->where('filters.search', 'Mele'));
    }

    public function test_products_index_falls_back_to_the_italian_name_when_english_name_is_missing(): void
    {
        $this->createProduct([
            'name' => 'Lattuga',
            'name_en' => null,
            'description' => 'Lattuga fresca.',
            'description_en' => null,
        ]);

        $this->withSession(['locale' => 'en'])
            ->get(route('products.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('products.0.name', 'Lattuga')
                ->where('products.0.description', 'Lattuga fresca.'));
    }
}
