<?php

namespace App\Services;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartService
{
    public function findCurrentCart(Request $request): ?Cart
    {
        if (! $request->user()) {
            return null;
        }

        return Cart::query()
            ->where('user_id', $request->user()->id)
            ->first();
    }

    public function getCurrentCart(Request $request): Cart
    {
        if (! $request->user()) {
            abort(403);
        }

        return Cart::firstOrCreate([
            'user_id' => $request->user()->id,
        ]);
    }
}
