<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Announcement;
use Carbon\Carbon;
use DB;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $filter = $request->input('filter', 'month');
        $now = Carbon::now()->timezone('Asia/Manila');
        $stats = collect();

        // 📊 Dynamic Chart Data
        if ($filter === 'week') {
            $stats = collect(range(0, 6))->map(function ($daysAgo) use ($now) {
                $date = $now->copy()->subDays($daysAgo)->startOfDay();

                $orders = Order::whereDate('created_at', $date)->get();
                $sales = $orders->sum(fn($order) => optional($order->product)->price * $order->quantity);

                return [
                    'month' => $date->format('D'),
                    'orders' => $orders->count(),
                    'sales' => round($sales, 2),
                ];
            })->reverse();
        } elseif ($filter === 'year') {
            $stats = collect(range(1, 12))->map(function ($month) use ($now) {
                $orders = Order::whereMonth('created_at', $month)
                    ->whereYear('created_at', $now->year)
                    ->get();

                $sales = $orders->sum(fn($order) => optional($order->product)->price * $order->quantity);

                return [
                    'month' => Carbon::create()->month($month)->format('M'),
                    'orders' => $orders->count(),
                    'sales' => round($sales, 2),
                ];
            });
        } else {
            // Default: current month by day
            $daysInMonth = $now->daysInMonth;

            $stats = collect(range(1, $daysInMonth))->map(function ($day) use ($now) {
                $date = Carbon::createFromDate($now->year, $now->month, $day)->startOfDay();
                $orders = Order::whereDate('created_at', $date)->get();

                $sales = $orders->sum(fn($order) => optional($order->product)->price * $order->quantity);

                return [
                    'month' => $date->format('d'),
                    'orders' => $orders->count(),
                    'sales' => round($sales, 2),
                ];
            });
        }

        // 🔝 Top Shop (most orders from seller's products)
        $topShop = User::select('users.name')
            ->join('shops', 'shops.user_id', '=', 'users.id')
            ->join('products', 'products.shop_id', '=', 'shops.id')
            ->join('orders', 'orders.product_id', '=', 'products.id')
            ->where('users.role', 'seller')
            ->groupBy('users.id', 'users.name')
            ->orderByRaw('COUNT(orders.id) DESC')
            ->value('users.name');

        // 🧍 Top Customer (most orders)
        $topCustomer = User::select('users.name')
            ->join('orders', 'orders.user_id', '=', 'users.id')
            ->where('users.role', 'user')
            ->groupBy('users.id', 'users.name')
            ->orderByRaw('COUNT(orders.id) DESC')
            ->value('users.name');

        // 🛍️ Top Product (by quantity sold)
        $topProduct = Product::select('products.name')
            ->join('orders', 'orders.product_id', '=', 'products.id')
            ->groupBy('products.id', 'products.name')
            ->orderByRaw('SUM(orders.quantity) DESC')
            ->value('products.name');

        return Inertia::render('Admin/AdminDash', [
            'metrics' => [
                'totalOrders' => Order::count(),
                'totalUsers' => User::where('role', 'user')->count(),
                'totalSellers' => User::where('role', 'seller')->count(),
                'totalAnnouncements' => Announcement::count(),
                'totalProducts' => Product::count(),
                'pendingSellerApplications' => User::where('seller_status', 'pending')->count(),
            ],
            'monthlyStats' => $stats->values()->all(),
            'topMetrics' => [
                'topShop' => $topShop ?? 'N/A',
                'topCustomer' => $topCustomer ?? 'N/A',
                'topProduct' => $topProduct ?? 'N/A',
            ],
        ]);
    }
}
