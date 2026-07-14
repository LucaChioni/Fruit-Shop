<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Feature\Concerns\CreatesShopModels;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use CreatesShopModels;
    use RefreshDatabase;

    public function test_admin_orders_index_shows_all_orders(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $customer = User::factory()->create(['email' => 'cliente@example.com']);
        $secondCustomer = User::factory()->create(['email' => 'secondo@example.com']);
        $firstOrder = $this->createOrder($customer, ['customer_name' => 'Primo Cliente']);
        $secondOrder = $this->createOrder($secondCustomer, ['customer_name' => 'Secondo Cliente']);

        $firstOrder->forceFill(['created_at' => now()->subMinute()])->save();
        $secondOrder->forceFill(['created_at' => now()])->save();

        $response = $this->actingAs($admin)->get(route('admin.orders.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Orders/Index')
                ->has('orders', 2)
                ->where('orders.0.id', $secondOrder->id)
                ->where('orders.0.order_number', $secondOrder->order_number)
                ->where('orders.0.customer_name', 'Secondo Cliente')
                ->where('orders.0.customer_email', 'secondo@example.com')
                ->where('orders.1.id', $firstOrder->id)
                ->where('orders.1.order_number', $firstOrder->order_number)
                ->where('orders.1.customer_name', 'Primo Cliente')
                ->where('orders.1.customer_email', 'cliente@example.com')
                ->where('filters.search', '')
                ->where('filters.status', 'all')
                ->where('filters.sort', 'created_at')
                ->where('filters.sort_direction', 'desc'));
    }

    public function test_admin_orders_index_can_be_filtered_and_sorted(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $customer = User::factory()->create();
        $lowerTotal = $this->createOrder($customer, ['customer_name' => 'Registrato Low', 'status' => 'completed', 'total_amount' => 10]);
        $higherTotal = $this->createOrder($customer, ['customer_name' => 'Registrato High', 'status' => 'completed', 'total_amount' => 30]);

        $response = $this->actingAs($admin)->get(route('admin.orders.index', [
            'status' => 'completed',
            'sort' => 'total_amount',
            'sort_direction' => 'desc',
        ]));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Orders/Index')
                ->has('orders', 2)
                ->where('orders.0.id', $higherTotal->id)
                ->where('orders.1.id', $lowerTotal->id)
                ->where('filters.search', '')
                ->where('filters.status', 'completed')
                ->where('filters.sort', 'total_amount')
                ->where('filters.sort_direction', 'desc'));
    }

    public function test_admin_orders_index_can_be_searched_by_customer_name_or_order_number(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $nameMatch = $this->createOrder(null, [
            'order_number' => 'FS-NAME-001',
            'customer_name' => 'Cliente Ricercato',
        ]);
        $codeMatch = $this->createOrder(null, [
            'order_number' => 'FS-CODE-123',
            'customer_name' => 'Cliente Codice',
        ]);
        $this->createOrder(null, [
            'order_number' => 'FS-OTHER-001',
            'customer_name' => 'Cliente Escluso',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.orders.index', ['search' => 'Ricercato']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Orders/Index')
                ->has('orders', 1)
                ->where('orders.0.id', $nameMatch->id)
                ->where('filters.search', 'Ricercato'));

        $this->actingAs($admin)
            ->get(route('admin.orders.index', ['search' => 'CODE-123']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Orders/Index')
                ->has('orders', 1)
                ->where('orders.0.id', $codeMatch->id)
                ->where('filters.search', 'CODE-123'));
    }

    public function test_non_admin_cannot_access_admin_orders_index(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.orders.index'))
            ->assertForbidden();
    }

    public function test_admin_order_show_uses_shared_detail_page_with_admin_props(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $order = $this->createOrder(null, ['customer_name' => 'Cliente Admin']);
        $this->createOrderItem($order);

        $response = $this->actingAs($admin)->get(route('admin.orders.show', $order));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Orders/Show')
                ->where('isAdminView', true)
                ->where('order.id', $order->id)
                ->where('order.order_number', $order->order_number)
                ->where('order.customer_name', 'Cliente Admin')
                ->has('order.items', 1)
                ->where('orderStatuses', [
                    'pending',
                    'ready',
                    'completed',
                    'cancelled',
                ]));
    }

    public function test_non_admin_cannot_access_admin_order_show(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrder();

        $this->actingAs($user)
            ->get(route('admin.orders.show', $order))
            ->assertForbidden();
    }

    public function test_admin_can_update_order_status(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $order = $this->createOrder();

        $response = $this
            ->actingAs($admin)
            ->patch(route('admin.orders.status.update', $order), [
                'status' => 'completed',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.orders.show', $order));

        $this->assertSame('completed', $order->refresh()->status);
    }

    public function test_non_admin_cannot_update_order_status(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrder();

        $response = $this
            ->actingAs($user)
            ->patch(route('admin.orders.status.update', $order), [
                'status' => 'completed',
            ]);

        $response->assertForbidden();

        $this->assertSame('pending', $order->refresh()->status);
    }

    public function test_order_status_must_be_valid(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $order = $this->createOrder();

        $response = $this
            ->actingAs($admin)
            ->from(route('admin.orders.show', $order))
            ->patch(route('admin.orders.status.update', $order), [
                'status' => 'invalid-status',
            ]);

        $response
            ->assertSessionHasErrors([
                'status' => 'Lo stato selezionato non è valido.',
            ])
            ->assertRedirect(route('admin.orders.show', $order));

        $this->assertSame('pending', $order->refresh()->status);
    }
}
