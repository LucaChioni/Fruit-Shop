<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Feature\Concerns\CreatesShopModels;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use CreatesShopModels;
    use RefreshDatabase;

    public function test_checkout_create_redirects_when_cart_is_empty(): void
    {
        $response = $this
            ->withCookie(CartService::GUEST_CART_COOKIE, 'guest-token')
            ->get(route('checkout.create'));

        $response
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('error', 'Il carrello è vuoto.');
    }

    public function test_checkout_create_shows_customer_name_for_authenticated_user(): void
    {
        $user = User::factory()->create(['name' => 'Giulia Verdi']);
        $cart = $this->createCart(['user_id' => $user->id]);
        $this->createCartItem($cart, $this->createProduct(), 1);

        $response = $this->actingAs($user)->get(route('checkout.create'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Checkout/Create')
                ->where('customerName', 'Giulia Verdi'));
    }

    public function test_checkout_store_creates_order_items_and_clears_cart(): void
    {
        $user = User::factory()->create();
        $cart = $this->createCart(['user_id' => $user->id]);
        $product = $this->createProduct(['name' => 'Pere', 'price' => 3.20, 'unit_type' => 'kg']);
        $this->createCartItem($cart, $product, 2);

        $response = $this
            ->actingAs($user)
            ->post(route('checkout.store'), [
                'customer_name' => 'Cliente Test',
                'notes' => 'Consegna pomeriggio',
            ]);

        $order = Order::firstOrFail();

        $response
            ->assertSessionHasNoErrors()
            ->assertSessionHas('last_order_id', $order->id)
            ->assertRedirect(route('orders.show', $order));

        $this->assertSame($user->id, $order->user_id);
        $this->assertSame('Cliente Test', $order->customer_name);
        $this->assertNotNull($order->order_number);
        $this->assertSame('pending', $order->status);
        $this->assertSame('6.40', $order->total_amount);
        $this->assertSame(1, $order->items()->count());
        $this->assertSame(0, $cart->items()->count());

        $orderItem = $order->items()->firstOrFail();
        $this->assertSame('Pere', $orderItem->product_name);
        $this->assertSame('6.40', $orderItem->line_total);
    }

    public function test_guest_checkout_store_creates_guest_order(): void
    {
        $cart = $this->createCart(['guest_token' => 'guest-token']);
        $product = $this->createProduct(['name' => 'Carote', 'price' => 1.40, 'unit_type' => 'kg']);
        $this->createCartItem($cart, $product, 3);

        $response = $this
            ->withCookie(CartService::GUEST_CART_COOKIE, 'guest-token')
            ->post(route('checkout.store'), [
                'customer_name' => 'Cliente Guest',
            ]);

        $order = Order::firstOrFail();

        $response
            ->assertSessionHasNoErrors()
            ->assertSessionHas('last_order_id', $order->id)
            ->assertRedirect(route('orders.show', $order));

        $this->assertNull($order->user_id);
        $this->assertNotNull($order->order_number);
        $this->assertSame('Cliente Guest', $order->customer_name);
        $this->assertSame('4.20', $order->total_amount);
        $this->assertSame(1, $order->items()->count());
        $this->assertSame(0, $cart->items()->count());
    }

    public function test_checkout_store_redirects_when_cart_is_empty(): void
    {
        $response = $this
            ->withCookie(CartService::GUEST_CART_COOKIE, 'guest-token')
            ->post(route('checkout.store'), [
                'customer_name' => 'Cliente Test',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('error', 'Il carrello è vuoto.');

        $this->assertSame(0, Order::count());
    }

    public function test_checkout_customer_name_validation_uses_italian_message(): void
    {
        $cart = $this->createCart(['guest_token' => 'guest-token']);
        $this->createCartItem($cart, $this->createProduct(), 1);

        $response = $this
            ->withCookie(CartService::GUEST_CART_COOKIE, 'guest-token')
            ->post(route('checkout.store'), [
                'customer_name' => '',
            ]);

        $response->assertSessionHasErrors([
            'customer_name' => 'Inserisci il nome per il ritiro.',
        ]);
    }
}
