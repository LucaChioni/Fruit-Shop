<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Feature\Concerns\CreatesShopModels;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use CreatesShopModels;
    use RefreshDatabase;

    public function test_admin_dashboard_shows_stats_and_latest_orders(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $pendingOrder = $this->createOrder(null, [
            'customer_name' => 'Cliente Pending',
            'status' => 'pending',
        ]);
        $completedOrder = $this->createOrder(null, [
            'customer_name' => 'Cliente Completed',
            'status' => 'completed',
            'created_at' => now()->subDay(),
        ]);

        $completedOrder->forceFill(['created_at' => now()->subDay()])->save();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Dashboard')
                ->where('stats.pending_orders', 1)
                ->where('stats.today_orders', 1)
                ->where('stats.total_orders', 2)
                ->has('latestOrders', 2)
                ->where('latestOrders.0.id', $pendingOrder->id)
                ->where('latestOrders.1.id', $completedOrder->id));
    }

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }
}
