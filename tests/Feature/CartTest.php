<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Feature\Concerns\CreatesShopModels;
use Tests\TestCase;

class CartTest extends TestCase
{
    use CreatesShopModels;
    use RefreshDatabase;

    public function test_guest_cart_index_shows_items_and_total(): void
    {
        $cart = $this->createCart(['guest_token' => 'guest-token']);
        $product = $this->createProduct(['name' => 'Mele', 'price' => 2.50, 'unit_type' => 'kg']);
        $cartItem = $this->createCartItem($cart, $product, 2);

        $response = $this
            ->withCookie(CartService::GUEST_CART_COOKIE, 'guest-token')
            ->get(route('cart.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Cart/Index')
                ->where('cartId', $cart->id)
                ->where('total', '5.00')
                ->has('items', 1)
                ->where('items.0.id', $cartItem->id)
                ->where('items.0.product_name', 'Mele')
                ->where('items.0.unit_price', '2.50')
                ->where('items.0.quantity_step', 0.1)
                ->where('items.0.line_total', '5.00'));
    }

    public function test_piece_products_use_integer_quantity_step_in_cart(): void
    {
        $cart = $this->createCart(['guest_token' => 'guest-token']);
        $product = $this->createProduct(['name' => 'Lattuga', 'unit_type' => 'pz']);
        $this->createCartItem($cart, $product, 1);

        $response = $this
            ->withCookie(CartService::GUEST_CART_COOKIE, 'guest-token')
            ->get(route('cart.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('items.0.product_name', 'Lattuga')
                ->where('items.0.quantity_step', 1));
    }

    public function test_authenticated_cart_index_uses_user_cart(): void
    {
        $user = User::factory()->create();
        $cart = $this->createCart(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('cart.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Cart/Index')
                ->where('cartId', $cart->id)
                ->has('items', 0)
                ->where('total', '0.00'));
    }

    public function test_cart_update_changes_only_items_in_current_cart(): void
    {
        $user = User::factory()->create();
        $cart = $this->createCart(['user_id' => $user->id]);
        $otherCart = $this->createCart(['guest_token' => 'other-token']);
        $product = $this->createProduct();
        $cartItem = $this->createCartItem($cart, $product, 1);
        $otherCartItem = $this->createCartItem($otherCart, $product, 4);

        $response = $this
            ->actingAs($user)
            ->patch(route('cart.update'), [
                'quantities' => [
                    $cartItem->id => 3,
                    $otherCartItem->id => 9,
                ],
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('cart.index'));

        $this->assertSame('3.00', $cartItem->refresh()->quantity);
        $this->assertSame('4.00', $otherCartItem->refresh()->quantity);
    }

    public function test_cart_item_store_creates_guest_cart_item(): void
    {
        $product = $this->createProduct();

        $response = $this
            ->withCookie(CartService::GUEST_CART_COOKIE, 'guest-token')
            ->post(route('cart.items.store'), [
                'product_id' => $product->id,
                'quantity' => 1.5,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('products.index'));

        $cart = Cart::where('guest_token', 'guest-token')->firstOrFail();
        $cartItem = $cart->items()->where('product_id', $product->id)->firstOrFail();

        $this->assertSame('1.50', $cartItem->quantity);
    }

    public function test_cart_item_store_increments_existing_item(): void
    {
        $user = User::factory()->create();
        $cart = $this->createCart(['user_id' => $user->id]);
        $product = $this->createProduct();
        $cartItem = $this->createCartItem($cart, $product, 1);

        $response = $this
            ->actingAs($user)
            ->post(route('cart.items.store'), [
                'product_id' => $product->id,
                'quantity' => 2.25,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('products.index'));

        $this->assertSame('3.25', $cartItem->refresh()->quantity);
        $this->assertSame(1, $cart->items()->count());
    }

    public function test_cart_item_destroy_deletes_only_items_from_current_cart(): void
    {
        $user = User::factory()->create();
        $cart = $this->createCart(['user_id' => $user->id]);
        $otherCart = $this->createCart(['guest_token' => 'other-token']);
        $product = $this->createProduct();
        $cartItem = $this->createCartItem($cart, $product, 1);
        $otherCartItem = $this->createCartItem($otherCart, $product, 1);

        $forbiddenResponse = $this
            ->actingAs($user)
            ->delete(route('cart.items.destroy', $otherCartItem));

        $forbiddenResponse->assertForbidden();
        $this->assertNotNull($otherCartItem->fresh());

        $response = $this
            ->actingAs($user)
            ->delete(route('cart.items.destroy', $cartItem));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('cart.index'));

        $this->assertNull($cartItem->fresh());
    }

    public function test_cart_item_quantity_validation_uses_italian_message(): void
    {
        $product = $this->createProduct();

        $response = $this
            ->withCookie(CartService::GUEST_CART_COOKIE, 'guest-token')
            ->post(route('cart.items.store'), [
                'product_id' => $product->id,
                'quantity' => 0,
            ]);

        $response->assertSessionHasErrors([
            'quantity' => 'La quantità minima è 0,01.',
        ]);
    }
}
