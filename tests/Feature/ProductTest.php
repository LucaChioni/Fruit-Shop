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
        $this->createProduct(['name' => 'Arance']);
        $this->createProduct(['name' => 'Banane', 'is_active' => false]);

        $response = $this->get(route('products.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Products/Index')
                ->has('products', 2)
                ->where('products.0.name', 'Arance')
                ->where('products.1.name', 'Zucchine'));
    }
}
