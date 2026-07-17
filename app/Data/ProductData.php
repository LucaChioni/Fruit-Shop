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

    public const CATEGORIES = [
        'fruit',
        'vegetable',
        'dried_fruit',
        'herbs',
        'mushrooms',
    ];

    public static function catalog(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => self::translatedName($product),
            'description' => self::translatedDescription($product),
            'category' => $product->category,
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

    public static function displayQuantity(string $quantity, string $unitType): string
    {
        if (! self::requiresWholeQuantity($unitType)) {
            return $quantity;
        }

        return number_format((float) $quantity, 0, '.', '');
    }

    public static function translatedName(Product|string $product, ?string $nameEn = null): string
    {
        if ($product instanceof Product) {
            $nameEn = $product->name_en;
            $product = $product->name;
        }

        return app()->getLocale() === 'en' && $nameEn ? $nameEn : $product;
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

        return app()->getLocale() === 'en' && $product->description_en
            ? $product->description_en
            : $product->description;
    }

    public static function translatedUnitType(string $unitType, ?string $quantity = null): string
    {
        $key = 'ui.units.'.$unitType;

        if ($quantity !== null && (float) $quantity !== 1.0 && Lang::has($key.'_plural')) {
            $key .= '_plural';
        }

        return Lang::has($key) ? trans($key) : $unitType;
    }
}
