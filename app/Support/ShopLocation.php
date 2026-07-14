<?php

namespace App\Support;

class ShopLocation
{
    public static function address(): string
    {
        return (string) config('shop.address', '');
    }

    public static function mapsUrl(): string
    {
        $configuredUrl = (string) config('shop.maps_url', '');

        if ($configuredUrl !== '') {
            return $configuredUrl;
        }

        $address = self::address();

        if ($address === '') {
            return '';
        }

        return 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($address);
    }

    public static function inertia(): array
    {
        return [
            'name' => (string) config('shop.name', 'Il Giardino della Frutta'),
            'address' => self::address(),
            'mapsUrl' => self::mapsUrl(),
        ];
    }
}
