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
        Product::updateOrCreate(
            ['name' => 'Mele Golden'],
            [
                'name_en' => 'Golden apples',
                'description' => 'Mele dolci e croccanti.',
                'description_en' => 'Sweet and crunchy apples.',
                'price' => 2.50,
                'unit_type' => 'kg',
                'is_active' => true,
            ]
        );

        Product::updateOrCreate(
            ['name' => 'Arance Tarocco'],
            [
                'name_en' => 'Tarocco oranges',
                'description' => 'Arance succose di stagione.',
                'description_en' => 'Juicy seasonal oranges.',
                'price' => 2.20,
                'unit_type' => 'kg',
                'is_active' => true,
            ]
        );

        Product::updateOrCreate(
            ['name' => 'Lattuga'],
            [
                'name_en' => 'Lettuce',
                'description' => 'Lattuga fresca.',
                'description_en' => 'Fresh lettuce.',
                'price' => 1.30,
                'unit_type' => 'pz',
                'is_active' => true,
            ]
        );

        Product::updateOrCreate(
            ['name' => 'Fragole'],
            [
                'name_en' => 'Strawberries',
                'description' => 'Vaschetta di fragole fresche.',
                'description_en' => 'Punnet of fresh strawberries.',
                'price' => 3.80,
                'unit_type' => 'vaschetta',
                'is_active' => true,
            ]
        );

        Product::updateOrCreate(
            ['name' => 'Prezzemolo'],
            [
                'name_en' => 'Parsley',
                'description' => 'Mazzo di prezzemolo fresco.',
                'description_en' => 'Bunch of fresh parsley.',
                'price' => 0.90,
                'unit_type' => 'pz',
                'is_active' => true,
            ]
        );
    }
}
