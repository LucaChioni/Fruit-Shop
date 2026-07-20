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

        if (filter_var($configuredUrl, FILTER_VALIDATE_URL)
            && strcasecmp((string) parse_url($configuredUrl, PHP_URL_SCHEME), 'https') === 0) {
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
        $legal = config('shop.legal', []);
        $legalDetails = [
            'brand' => (string) ($legal['brand'] ?? ''),
            'companyName' => (string) ($legal['company_name'] ?? ''),
            'registeredOffice' => (string) ($legal['registered_office'] ?? ''),
            'vatNumber' => (string) ($legal['vat_number'] ?? ''),
            'taxCode' => (string) ($legal['tax_code'] ?? ''),
            'rea' => (string) ($legal['rea'] ?? ''),
            'shareCapital' => (string) ($legal['share_capital'] ?? ''),
            'email' => (string) ($legal['email'] ?? ''),
            'pec' => (string) ($legal['pec'] ?? ''),
        ];

        return [
            'name' => (string) config('shop.name', 'Il Giardino della Frutta'),
            'address' => self::address(),
            'mapsUrl' => self::mapsUrl(),
            'legal' => $legalDetails,
        ];
    }
}
