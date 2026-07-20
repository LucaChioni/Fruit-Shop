<?php

return [
    'name' => env('SHOP_NAME', 'Il Giardino della Frutta'),
    'address' => env('SHOP_ADDRESS', ''),
    'maps_url' => env('SHOP_MAPS_URL', ''),
    'legal' => [
        'brand' => env('SHOP_LEGAL_BRAND', 'Fruit Shop'),
        'company_name' => env('SHOP_COMPANY_NAME', 'Fruit Shop S.r.l.'),
        'registered_office' => env('SHOP_REGISTERED_OFFICE', 'Via Placeholder 1, 00100 Roma (RM)'),
        'vat_number' => env('SHOP_VAT_NUMBER', 'IT00000000000'),
        'tax_code' => env('SHOP_TAX_CODE', '00000000000'),
        'rea' => env('SHOP_REA', 'RM-0000000'),
        'share_capital' => env('SHOP_SHARE_CAPITAL', 'Euro 10.000 i.v.'),
        'email' => env('SHOP_LEGAL_EMAIL', 'info@example.com'),
        'pec' => env('SHOP_PEC', 'fruitshop@pec.example.com'),
    ],
];
