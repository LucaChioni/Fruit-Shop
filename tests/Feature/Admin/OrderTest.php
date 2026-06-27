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
        $firstOrder = $this->createOrder(null, ['customer_name' => 'Primo Cliente']);
        $secondOrder = $this->createOrder(null, ['customer_name' => 'Secondo Cliente']);

        $firstOrder->forceFill(['created_at' => now()->subMinute()])->save();
        $secondOrder->forceFill(['created_at' => now()])->save();

        $response = $this->actingAs($admin)->get(route('admin.orders.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Orders/Index')
                ->has('orders', 2)
                ->where('orders.0.id', $secondOrder->id)
                ->where('orders.0.customer_name', 'Secondo Cliente')
                ->where('orders.1.id', $firstOrder->id)
                ->where('orders.1.customer_name', 'Primo Cliente'));
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
                ->where('order.customer_name', 'Cliente Admin')
                ->has('order.items', 1)
                ->where('orderStatuses', [
                    'pending',
                    'confirmed',
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
                'status' => 'confirmed',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.orders.show', $order));

        $this->assertSame('confirmed', $order->refresh()->status);
    }

    public function test_non_admin_cannot_update_order_status(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrder();

        $response = $this
            ->actingAs($user)
            ->patch(route('admin.orders.status.update', $order), [
                'status' => 'confirmed',
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
            ->assertSessionHasErrors('status')
            ->assertRedirect(route('admin.orders.show', $order));

        $this->assertSame('pending', $order->refresh()->status);
    }
}
