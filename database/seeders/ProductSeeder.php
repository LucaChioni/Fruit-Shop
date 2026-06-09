<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Mele Golden',
            'description' => 'Mele dolci e croccanti.',
            'price' => 2.50,
            'unit_type' => 'kg',
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Arance Tarocco',
            'description' => 'Arance succose di stagione.',
            'price' => 2.20,
            'unit_type' => 'kg',
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Lattuga',
            'description' => 'Lattuga fresca.',
            'price' => 1.30,
            'unit_type' => 'piece',
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Fragole',
            'description' => 'Vaschetta di fragole fresche.',
            'price' => 3.80,
            'unit_type' => 'box',
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Prezzemolo',
            'description' => 'Mazzo di prezzemolo fresco.',
            'price' => 0.90,
            'unit_type' => 'bunch',
            'is_active' => true,
        ]);
    }
}
