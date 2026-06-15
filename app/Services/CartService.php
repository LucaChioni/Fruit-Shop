<?php

namespace App\Services;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class CartService
{
    public const GUEST_CART_COOKIE = 'guest_cart_token';

    public function getCurrentCart(Request $request): Cart
    {
        if ($request->user()) {
            return Cart::firstOrCreate([
                'user_id' => $request->user()->id,
            ]);
        }

        $guestToken = $request->cookie(self::GUEST_CART_COOKIE);

        if (! $guestToken) {
            $guestToken = (string) Str::uuid();

            Cookie::queue(
                self::GUEST_CART_COOKIE,
                $guestToken,
                60 * 24 * 30
            );
        }

        return Cart::firstOrCreate([
            'guest_token' => $guestToken,
        ]);
    }
}
