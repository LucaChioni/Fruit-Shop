<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Feature\Concerns\CreatesShopModels;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use CreatesShopModels;
    use RefreshDatabase;

    public function test_orders_index_requires_authentication(): void
    {
        $this->get(route('orders.index'))->assertRedirect(route('login'));
    }

    public function test_orders_index_shows_only_authenticated_user_orders(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->createOrder($otherUser, ['customer_name' => 'Altro Cliente']);
        $order = $this->createOrder($user, ['customer_name' => 'Cliente Utente']);

        $response = $this->actingAs($user)->get(route('orders.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Orders/Index')
                ->has('orders', 1)
                ->where('orders.0.id', $order->id)
                ->where('orders.0.order_number', $order->order_number)
                ->where('orders.0.customer_name', 'Cliente Utente')
                ->where('filters.status', 'all')
                ->where('filters.sort', 'newest'));
    }

    public function test_orders_index_can_be_filtered_and_sorted(): void
    {
        $user = User::factory()->create();
        $this->createOrder($user, ['customer_name' => 'Pending', 'status' => 'pending', 'total_amount' => 20]);
        $lowerTotal = $this->createOrder($user, ['customer_name' => 'Completed Low', 'status' => 'completed', 'total_amount' => 10]);
        $higherTotal = $this->createOrder($user, ['customer_name' => 'Completed High', 'status' => 'completed', 'total_amount' => 30]);

        $response = $this->actingAs($user)->get(route('orders.index', [
            'status' => 'completed',
            'sort' => 'total_desc',
        ]));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Orders/Index')
                ->has('orders', 2)
                ->where('orders.0.id', $higherTotal->id)
                ->where('orders.1.id', $lowerTotal->id)
                ->where('filters.status', 'completed')
                ->where('filters.sort', 'total_desc'));
    }

    public function test_order_show_allows_owner(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrder($user, ['customer_name' => 'Cliente Utente']);
        $this->createOrderItem($order);

        $response = $this->actingAs($user)->get(route('orders.show', $order));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Orders/Show')
                ->where('order.id', $order->id)
                ->where('order.order_number', $order->order_number)
                ->where('order.customer_name', 'Cliente Utente')
                ->has('order.items', 1));
    }

    public function test_order_show_forbids_other_authenticated_users(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $order = $this->createOrder($otherUser);

        $this->actingAs($user)
            ->get(route('orders.show', $order))
            ->assertForbidden();
    }

    public function test_guest_order_show_requires_matching_last_order_session(): void
    {
        $order = $this->createOrder();
        $otherOrder = $this->createOrder();

        $this->get(route('orders.show', $order))->assertForbidden();

        $this
            ->withSession(['last_order_id' => $otherOrder->id])
            ->get(route('orders.show', $order))
            ->assertForbidden();

        $this
            ->withSession(['last_order_id' => $order->id])
            ->get(route('orders.show', $order))
            ->assertOk();
    }
}
