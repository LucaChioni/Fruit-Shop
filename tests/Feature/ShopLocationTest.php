<?php

namespace Tests\Feature;

use App\Support\ShopLocation;
use Tests\TestCase;

class ShopLocationTest extends TestCase
{
    public function test_valid_https_maps_url_is_returned_unchanged(): void
    {
        $url = 'https://maps.example.com/place/Fruit%20Shop?layer=map#entrance';

        config()->set('shop.maps_url', $url);

        $this->assertSame($url, ShopLocation::mapsUrl());
    }

    public function test_invalid_or_non_https_maps_url_is_ignored(): void
    {
        config()->set('shop.address', 'Via Roma 1, Torino');

        foreach (['http://maps.example.com/shop', 'javascript:alert(1)', 'not-a-url'] as $url) {
            config()->set('shop.maps_url', $url);

            $this->assertSame(
                'https://www.google.com/maps/search/?api=1&query=Via%20Roma%201%2C%20Torino',
                ShopLocation::mapsUrl(),
            );
        }
    }

    public function test_legal_details_are_exposed_to_inertia(): void
    {
        config()->set('shop.legal', [
            'brand' => 'Orto',
            'company_name' => 'Orto S.r.l.',
            'registered_office' => 'Via Roma 1',
            'vat_number' => 'IT123',
            'tax_code' => '123',
            'rea' => 'RM-123',
            'share_capital' => 'Euro 1.000 i.v.',
            'email' => 'info@example.com',
            'pec' => 'orto@pec.example.com',
        ]);

        $this->assertSame([
            'brand' => 'Orto',
            'companyName' => 'Orto S.r.l.',
            'registeredOffice' => 'Via Roma 1',
            'vatNumber' => 'IT123',
            'taxCode' => '123',
            'rea' => 'RM-123',
            'shareCapital' => 'Euro 1.000 i.v.',
            'email' => 'info@example.com',
            'pec' => 'orto@pec.example.com',
        ], ShopLocation::inertia()['legal']);
    }
}
