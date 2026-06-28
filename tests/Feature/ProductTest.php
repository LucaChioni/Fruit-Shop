<?php

namespace Tests\Feature;

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
                ->where('filters.sort', 'name'));
    }

    public function test_products_index_can_be_filtered_and_sorted(): void
    {
        $this->createProduct(['name' => 'Arance Navel', 'price' => 3.20]);
        $this->createProduct(['name' => 'Arance Tarocco', 'price' => 2.80]);
        $this->createProduct(['name' => 'Zucchine', 'price' => 1.90]);

        $response = $this->get(route('products.index', [
            'search' => 'Arance',
            'sort' => 'price_desc',
        ]));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Products/Index')
                ->has('products', 2)
                ->where('products.0.name', 'Arance Navel')
                ->where('products.1.name', 'Arance Tarocco')
                ->where('filters.search', 'Arance')
                ->where('filters.sort', 'price_desc'));
    }
}
