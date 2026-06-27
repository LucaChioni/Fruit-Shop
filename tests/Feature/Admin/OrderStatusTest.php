<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_order_status(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $order = Order::create([
            'customer_name' => 'Mario Rossi',
            'status' => 'pending',
            'total_amount' => 12.50,
        ]);

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
        $order = Order::create([
            'customer_name' => 'Mario Rossi',
            'status' => 'pending',
            'total_amount' => 12.50,
        ]);

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
        $order = Order::create([
            'customer_name' => 'Mario Rossi',
            'status' => 'pending',
            'total_amount' => 12.50,
        ]);

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
