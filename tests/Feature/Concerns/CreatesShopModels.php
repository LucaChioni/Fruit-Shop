<?php

namespace Tests\Feature\Concerns;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;

trait CreatesShopModels
{
    protected function createProduct(array $attributes = []): Product
    {
        return Product::create(array_merge([
            'name' => 'Mele Golden',
            'description' => 'Mele dolci e croccanti.',
            'image_url' => null,
            'price' => 2.50,
            'unit_type' => 'kg',
            'is_active' => true,
        ], $attributes));
    }

    protected function createCart(array $attributes = []): Cart
    {
        $attributes['user_id'] ??= User::factory()->create()->id;

        return Cart::create($attributes);
    }

    protected function createCartItem(Cart $cart, Product $product, float $quantity): CartItem
    {
        return CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
        ]);
    }

    protected function createOrder(?User $user = null, array $attributes = []): Order
    {
        $user ??= User::factory()->create();

        if (array_key_exists('customer_name', $attributes)) {
            $user->update(['name' => $attributes['customer_name']]);
            unset($attributes['customer_name']);
        }

        return Order::create(array_merge([
            'user_id' => $user->id,
            'status' => 'pending',
            'total_amount' => 10.00,
        ], $attributes));
    }

    protected function createOrderItem(Order $order, ?Product $product = null, array $attributes = []): OrderItem
    {
        $product ??= $this->createProduct();

        return OrderItem::create(array_merge([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_type' => $product->unit_type,
            'unit_price' => $product->price,
            'quantity' => 2,
            'line_total' => 5.00,
        ], $attributes));
    }
}
