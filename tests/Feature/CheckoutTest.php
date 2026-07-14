<?php

namespace Tests\Feature;

use App\Mail\OrderPlaced;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Feature\Concerns\CreatesShopModels;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use CreatesShopModels;
    use RefreshDatabase;

    public function test_checkout_create_requires_authentication(): void
    {
        $this->get(route('checkout.create'))->assertRedirect(route('login'));
    }

    public function test_checkout_create_redirects_when_cart_is_empty(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('checkout.create'));

        $response
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('error', 'Il carrello è vuoto.');
    }

    public function test_checkout_create_does_not_expose_editable_customer_name(): void
    {
        $user = User::factory()->create(['name' => 'Giulia Verdi']);
        $cart = $this->createCart(['user_id' => $user->id]);
        $this->createCartItem($cart, $this->createProduct(), 1);

        $response = $this->actingAs($user)->get(route('checkout.create'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Checkout/Create')
                ->missing('customerName'));
    }

    public function test_checkout_create_skips_closed_dates_for_default_pickup(): void
    {
        $this->travelTo(Carbon::parse('2026-12-24 23:00:00'));

        $user = User::factory()->create();
        $cart = $this->createCart(['user_id' => $user->id]);
        $this->createCartItem($cart, $this->createProduct(), 1);

        $response = $this->actingAs($user)->get(route('checkout.create'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Checkout/Create')
                ->where('pickupAtDefault', '2026-12-28T11:00')
                ->where('closedPickupDates.0', '2026-12-25')
                ->where('closedPickupDates.1', '2026-12-26')
                ->where('closedPickupDates.2', '2026-12-27'));
    }

    public function test_checkout_store_requires_authentication(): void
    {
        $this->post(route('checkout.store'), [
            'customer_name' => 'Cliente Test',
            'pickup_at' => '2026-06-29T12:00',
        ])->assertRedirect(route('login'));
    }

    public function test_checkout_store_creates_order_items_and_clears_cart(): void
    {
        $this->travelTo(Carbon::parse('2026-06-28 09:00:00'));
        Mail::fake();
        config(['mail.order_notifications.address' => 'admin@example.com']);

        $user = User::factory()->create(['name' => 'Cliente Account', 'email' => 'customer@example.com']);
        $cart = $this->createCart(['user_id' => $user->id]);
        $product = $this->createProduct(['name' => 'Pere', 'price' => 3.20, 'unit_type' => 'kg']);
        $this->createCartItem($cart, $product, 2);

        $response = $this
            ->actingAs($user)
            ->post(route('checkout.store'), [
                'customer_name' => 'Cliente Test',
                'pickup_at' => '2026-06-29T12:00',
                'notes' => 'Consegna pomeriggio',
            ]);

        $order = Order::firstOrFail();

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('orders.show', $order));

        $this->assertSame($user->id, $order->user_id);
        $this->assertSame('Cliente Account', $order->customer_name);
        $this->assertSame('2026-06-29 12:00:00', $order->pickup_at->format('Y-m-d H:i:s'));
        $this->assertNotNull($order->order_number);
        $this->assertSame('pending', $order->status);
        $this->assertSame('6.40', $order->total_amount);
        $this->assertSame(1, $order->items()->count());
        $this->assertSame(0, $cart->items()->count());

        $orderItem = $order->items()->firstOrFail();
        $this->assertSame('Pere', $orderItem->product_name);
        $this->assertSame('6.40', $orderItem->line_total);

        Mail::assertSent(OrderPlaced::class, 2);
        Mail::assertSent(OrderPlaced::class, fn (OrderPlaced $mail) => $mail->order->is($order) && $mail->hasTo('admin@example.com'));
        Mail::assertSent(OrderPlaced::class, fn (OrderPlaced $mail) => $mail->order->is($order) && $mail->hasTo('customer@example.com'));
    }

    public function test_checkout_store_does_not_duplicate_email_when_admin_is_customer(): void
    {
        $this->travelTo(Carbon::parse('2026-06-28 09:00:00'));
        Mail::fake();
        config(['mail.order_notifications.address' => 'customer@example.com']);

        $user = User::factory()->create(['email' => 'customer@example.com']);
        $cart = $this->createCart(['user_id' => $user->id]);
        $this->createCartItem($cart, $this->createProduct(), 1);

        $this
            ->actingAs($user)
            ->post(route('checkout.store'), [
                'customer_name' => 'Cliente Test',
                'pickup_at' => '2026-06-29T12:00',
            ]);

        $order = Order::firstOrFail();

        Mail::assertSent(OrderPlaced::class, 1);
        Mail::assertSent(OrderPlaced::class, fn (OrderPlaced $mail) => $mail->order->is($order) && $mail->hasTo('customer@example.com'));
    }

    public function test_checkout_store_redirects_when_cart_is_empty(): void
    {
        Mail::fake();
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('checkout.store'), [
                'customer_name' => 'Cliente Test',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('error', 'Il carrello è vuoto.');

        $this->assertSame(0, Order::count());
        Mail::assertNothingSent();
    }

    public function test_checkout_pickup_time_must_be_at_least_two_hours_after_order(): void
    {
        $this->travelTo(Carbon::parse('2026-06-28 09:00:00'));

        $user = User::factory()->create();
        $cart = $this->createCart(['user_id' => $user->id]);
        $this->createCartItem($cart, $this->createProduct(), 1);

        $response = $this
            ->actingAs($user)
            ->post(route('checkout.store'), [
                'customer_name' => 'Cliente Test',
                'pickup_at' => '2026-06-28T10:30',
            ]);

        $response->assertSessionHasErrors([
            'pickup_at' => 'Il ritiro deve essere almeno 2 ore dopo l\'ordine.',
        ]);
    }

    public function test_checkout_pickup_time_must_be_in_opening_hours(): void
    {
        $this->travelTo(Carbon::parse('2026-06-28 09:00:00'));

        $user = User::factory()->create();
        $cart = $this->createCart(['user_id' => $user->id]);
        $this->createCartItem($cart, $this->createProduct(), 1);

        $response = $this
            ->actingAs($user)
            ->post(route('checkout.store'), [
                'customer_name' => 'Cliente Test',
                'pickup_at' => '2026-06-29T14:30',
            ]);

        $response->assertSessionHasErrors([
            'pickup_at' => 'Scegli un orario di ritiro tra 11:00-13:00 o 16:00-19:30.',
        ]);
    }

    public function test_checkout_pickup_date_cannot_be_sunday(): void
    {
        $this->travelTo(Carbon::parse('2026-06-26 09:00:00'));

        $user = User::factory()->create();
        $cart = $this->createCart(['user_id' => $user->id]);
        $this->createCartItem($cart, $this->createProduct(), 1);

        $response = $this
            ->actingAs($user)
            ->post(route('checkout.store'), [
                'customer_name' => 'Cliente Test',
                'pickup_at' => '2026-06-28T12:00',
            ]);

        $response->assertSessionHasErrors([
            'pickup_at' => 'Il ritiro non è disponibile la domenica o nei giorni festivi.',
        ]);
    }

    public function test_checkout_pickup_date_cannot_be_holiday(): void
    {
        $this->travelTo(Carbon::parse('2026-12-24 09:00:00'));

        $user = User::factory()->create();
        $cart = $this->createCart(['user_id' => $user->id]);
        $this->createCartItem($cart, $this->createProduct(), 1);

        $response = $this
            ->actingAs($user)
            ->post(route('checkout.store'), [
                'customer_name' => 'Cliente Test',
                'pickup_at' => '2026-12-25T12:00',
            ]);

        $response->assertSessionHasErrors([
            'pickup_at' => 'Il ritiro non è disponibile la domenica o nei giorni festivi.',
        ]);
    }
}
