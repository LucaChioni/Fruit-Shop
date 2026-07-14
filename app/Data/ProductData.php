<?php

namespace App\Data;

use App\Models\Product;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

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
            'name' => self::translatedName($product),
            'description' => self::translatedDescription($product),
            'image_url' => $product->image_url,
            'price' => number_format((float) $product->price, 2, '.', ''),
            'unit_type_key' => $product->unit_type,
            'unit_type' => self::translatedUnitType($product->unit_type),
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

    public static function requiresWholeQuantity(string $unitType): bool
    {
        return self::quantityStep($unitType) === 1.0;
    }

    public static function translatedName(Product|string $product): string
    {
        $name = $product instanceof Product ? $product->name : $product;
        $key = 'ui.'.self::translationKey($name).'.name';

        return Lang::has($key) ? trans($key) : $name;
    }

    public static function matchesTranslatedName(Product $product, string $search): bool
    {
        $search = Str::lower(Str::squish($search));

        if ($search === '') {
            return true;
        }

        return Str::contains(Str::lower(self::translatedName($product)), $search);
    }

    public static function translatedDescription(Product $product): ?string
    {
        if ($product->description === null) {
            return null;
        }

        $key = 'ui.'.self::translationKey($product->name).'.description';

        return Lang::has($key) ? trans($key) : $product->description;
    }

    public static function translatedUnitType(string $unitType): string
    {
        $key = 'ui.units.'.$unitType;

        return Lang::has($key) ? trans($key) : $unitType;
    }

    public static function translationKey(string $name): string
    {
        return 'products.items.'.Str::slug($name, '_');
    }
}
