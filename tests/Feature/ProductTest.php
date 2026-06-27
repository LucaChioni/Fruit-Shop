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
        $this->createProduct(['name' => 'Arance', 'price' => 2.5, 'unit_type' => 'kg']);
        $this->createProduct(['name' => 'Banane', 'is_active' => false]);

        $response = $this->get(route('products.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Products/Index')
                ->has('products', 2)
                ->where('products.0.name', 'Arance')
                ->where('products.0.price', '2.50')
                ->where('products.0.quantity_step', 0.1)
                ->where('products.1.name', 'Zucchine'));
    }
}
