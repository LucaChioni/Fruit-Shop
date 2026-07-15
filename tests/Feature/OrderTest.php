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
                ->where('filters.search', '')
                ->where('filters.status', 'all')
                ->where('filters.sort', 'created_at')
                ->where('filters.sort_direction', 'desc'));
    }

    public function test_orders_index_can_be_filtered_and_sorted(): void
    {
        $user = User::factory()->create();
        $this->createOrder($user, ['customer_name' => 'Pending', 'status' => 'pending', 'total_amount' => 20]);
        $lowerTotal = $this->createOrder($user, ['customer_name' => 'Completed Low', 'status' => 'completed', 'total_amount' => 10]);
        $higherTotal = $this->createOrder($user, ['customer_name' => 'Completed High', 'status' => 'completed', 'total_amount' => 30]);

        $response = $this->actingAs($user)->get(route('orders.index', [
            'status' => 'completed',
            'sort' => 'total_amount',
            'sort_direction' => 'desc',
        ]));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Orders/Index')
                ->has('orders', 2)
                ->where('orders.0.id', $higherTotal->id)
                ->where('orders.1.id', $lowerTotal->id)
                ->where('filters.search', '')
                ->where('filters.status', 'completed')
                ->where('filters.sort', 'total_amount')
                ->where('filters.sort_direction', 'desc'));
    }

    public function test_orders_index_can_be_searched_by_customer_name_or_order_number(): void
    {
        $user = User::factory()->create(['name' => 'Cliente Ricercato']);
        $otherUser = User::factory()->create();
        $this->createOrder($user, [
            'order_number' => 'FS-NAME-001',
        ]);
        $codeMatch = $this->createOrder($user, [
            'order_number' => 'FS-CODE-123',
        ]);
        $this->createOrder($otherUser, [
            'order_number' => 'FS-CODE-999',
        ]);

        $this->actingAs($user)
            ->get(route('orders.index', ['search' => 'Ricercato']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Orders/Index')
                ->has('orders', 2)
                ->where('filters.search', 'Ricercato'));

        $this->actingAs($user)
            ->get(route('orders.index', ['search' => 'CODE-123']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Orders/Index')
                ->has('orders', 1)
                ->where('orders.0.id', $codeMatch->id)
                ->where('filters.search', 'CODE-123'));
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

    public function test_order_show_uses_the_current_customer_name(): void
    {
        $user = User::factory()->create(['name' => 'Nome precedente']);
        $order = $this->createOrder($user);
        $user->update(['name' => 'Nome aggiornato']);

        $this->actingAs($user)
            ->get(route('orders.show', $order))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('order.customer_name', 'Nome aggiornato'));
    }

    public function test_order_show_translates_legacy_order_items_from_the_product(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrder($user);
        $product = $this->createProduct(['name' => 'Mele Golden', 'name_en' => 'Golden apples']);
        $this->createOrderItem($order, $product, ['product_name_en' => null]);

        $this->withSession(['locale' => 'en'])
            ->actingAs($user)
            ->get(route('orders.show', $order))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('order.items.0.product_name', 'Golden apples'));
    }

    public function test_order_show_uses_singular_and_plural_tray_units(): void
    {
        app()->setLocale('it');
        $user = User::factory()->create();
        $order = $this->createOrder($user);
        $product = $this->createProduct(['unit_type' => 'vaschetta']);
        $gramProduct = $this->createProduct(['unit_type' => 'g']);
        $this->createOrderItem($order, $product, ['quantity' => 1]);
        $this->createOrderItem($order, $product, ['quantity' => 2]);
        $this->createOrderItem($order, $gramProduct, ['quantity' => 2]);

        $this->actingAs($user)
            ->get(route('orders.show', $order))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('order.items', 3)
                ->where('order.items.0.unit_type', 'vaschetta')
                ->where('order.items.0.quantity', '1')
                ->where('order.items.1.unit_type', 'vaschette')
                ->where('order.items.1.quantity', '2')
                ->where('order.items.2.unit_type', 'g')
                ->where('order.items.2.quantity', '2'));
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

    public function test_order_show_requires_authentication(): void
    {
        $order = $this->createOrder();

        $this->get(route('orders.show', $order))->assertRedirect(route('login'));
    }
}
