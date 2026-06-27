<?php

namespace App\Http\Controllers\Admin;

use App\Data\OrderData;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $latestOrders = Order::query()
            ->latest()
            ->limit(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'pending_orders' => Order::where('status', 'pending')->count(),
                'today_orders' => Order::whereDate('created_at', today())->count(),
                'total_orders' => Order::count(),
            ],
            'latestOrders' => OrderData::collection($latestOrders),
        ]);
    }
}
