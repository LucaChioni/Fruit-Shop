<?php

namespace App\Data;

use App\Models\Product;

class ProductData
{
    public const UNIT_TYPES = [
        'kg',
        'pz',
        'g',
        'vaschetta',
    ];

    public static function catalog(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'image_url' => $product->image_url,
            'price' => number_format((float) $product->price, 2, '.', ''),
            'unit_type' => $product->unit_type,
            'is_active' => $product->is_active,
            'quantity_step' => self::quantityStep($product->unit_type),
        ];
    }

    public static function quantityStep(string $unitType): float
    {
        return match ($unitType) {
            'kg' => 0.1,
            default => 1.0,
        };
    }
}
