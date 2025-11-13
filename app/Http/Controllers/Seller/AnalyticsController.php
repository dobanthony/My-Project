<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\Order;
use App\Models\Product;
use App\Models\ReceivedOrder;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $seller = Auth::user();
        $shop = $seller->shop;

        // Redirect if no shop
        if (!$shop) {
            return redirect()->route('dashboard')->with('error', 'You do not have a shop yet.');
        }

        $range = $request->input('range', 'week');
        $startDate = match ($range) {
            'month' => now()->subDays(30),
            'year' => now()->startOfYear(),
            default => now()->subDays(7),
        };

        $productIds = $shop->products->pluck('id');

        // Orders marked as received (completed)
        $deliveredOrders = ReceivedOrder::whereHas('order', fn ($query) =>
            $query->whereIn('product_id', $productIds)
        )->where('created_at', '>=', $startDate)
         ->with('order.product')
         ->get();

        $totalSales = $deliveredOrders->sum(fn ($r) => $r->order->quantity * $r->order->product->price);

        $totalOrders = Order::whereIn('product_id', $productIds)
            ->where('created_at', '>=', $startDate)
            ->count();

        $pendingOrders = Order::whereIn('product_id', $productIds)
            ->where('status', 'pending')
            ->where('created_at', '>=', $startDate)
            ->count();

        $cancelledOrders = Order::whereIn('product_id', $productIds)
            ->where('status', 'canceled')
            ->where('created_at', '>=', $startDate)
            ->count();

        $receivedOrdersCount = $deliveredOrders->count();

        $topSelling = Product::whereIn('id', $productIds)
            ->withCount(['orders as total_sold' => fn ($q) =>
                $q->where('created_at', '>=', $startDate)->select(DB::raw("SUM(quantity)"))
            ])
            ->orderByDesc('total_sold')
            ->take(5)
            ->get(['id', 'name']);

        //Bar Chart data for top-selling products
        $topSellingChartData = $topSelling->map(fn ($product) => [
            'label' => $product->name,
            'total' => (int) $product->total_sold,
        ]);

        $lowStock = Product::whereIn('id', $productIds)
            ->where('stock', '<=', 10)
            ->get(['id', 'name', 'stock']);

        $topCustomer = Order::join('users', 'orders.user_id', '=', 'users.id')
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->whereIn('orders.product_id', $productIds)
            ->where('orders.created_at', '>=', $startDate)
            ->select('users.first_name',
                    'users.last_name',
                    DB::raw('SUM(orders.quantity * products.price) as total_spent'))
            ->groupBy('users.first_name', 'users.last_name')
            ->orderByDesc('total_spent')
            ->first();

        $weeklyOrders = Order::whereIn('product_id', $productIds)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        // ✅ UPDATED RECENT ORDERS WITH first_name and last_name
        $recentOrders = Order::whereIn('product_id', $productIds)
            ->latest()
            ->take(5)
            ->with(['user', 'product'])
            ->get()
            ->map(fn ($order) => [
                'id' => $order->id,
                'first_name' => $order->user->first_name ?? '',
                'last_name' => $order->user->last_name ?? '',
                'total' => $order->quantity * $order->product->price,
                'status' => $order->status,
                'date' => $order->created_at->format('M d, Y'),
            ]);

        // Line Chart Data (Sales)
        $salesData = $deliveredOrders->groupBy(function ($item) use ($range) {
            $date = $item->created_at;
            return match ($range) {
                'year' => $date->format('m'),
                default => $date->format('Y-m-d'),
            };
        })->map(fn ($group) =>
            $group->sum(fn ($r) => $r->order->quantity * $r->order->product->price)
        );

        $salesLabels = [];
        $salesTotals = [];

        if ($range === 'year') {
            foreach (range(1, 12) as $month) {
                $label = \Carbon\Carbon::create()->month($month)->format('M');
                $key = str_pad($month, 2, '0', STR_PAD_LEFT);
                $salesLabels[] = $label;
                $salesTotals[] = (float) ($salesData->get($key) ?? 0);
            }
        } elseif ($range === 'month') {
            foreach (range(29, 0) as $i) {
                $date = now()->subDays($i);
                $key = $date->format('Y-m-d');
                $salesLabels[] = $date->format('d M');
                $salesTotals[] = (float) ($salesData->get($key) ?? 0);
            }
        } else {
            foreach (range(6, 0) as $i) {
                $date = now()->subDays($i);
                $key = $date->format('Y-m-d');
                $salesLabels[] = $date->format('D');
                $salesTotals[] = (float) ($salesData->get($key) ?? 0);
            }
        }

        return Inertia::render('Seller/Analytics', [
        'range' => $range,
        'shop' => $shop->only(['id', 'name', 'slug', 'shop_rating', 'shop_rating_count']),
            'stats' => [
                [
                    'key' => 'totalSales',
                    'label' => 'Total Sales',
                    'value' => round($totalSales, 2),
                    'icon' => 'bi bi-currency-dollar text-success',
                    'isCurrency' => true,
                    'bgClass' => 'border-success'
                ],
                [
                    'key' => 'totalOrders',
                    'label' => 'Total Orders',
                    'value' => $totalOrders,
                    'icon' => 'bi bi-card-checklist text-primary',
                    'isCurrency' => false,
                    'bgClass' => 'border-primary'
                ],
                [
                    'key' => 'pendingOrders',
                    'label' => 'Pending Orders',
                    'value' => $pendingOrders,
                    'icon' => 'bi bi-hourglass-split text-warning',
                    'isCurrency' => false,
                    'bgClass' => 'border-warning'
                ],
                [
                    'key' => 'cancelledOrders',
                    'label' => 'Cancelled Orders',
                    'value' => $cancelledOrders,
                    'icon' => 'bi bi-x-circle text-danger',
                    'isCurrency' => false,
                    'bgClass' => 'border-danger'
                ],
                [
                    'key' => 'receivedOrders',
                    'label' => 'Received Orders',
                    'value' => $receivedOrdersCount,
                    'icon' => 'bi bi-check2-circle text-secondary',
                    'isCurrency' => false,
                    'bgClass' => 'border-secondary'
                ],
            ],
            'topSelling' => $topSelling,
            'lowStock' => $lowStock,
            'customerStats' => [
                'first_name' => $topCustomer->first_name ?? 'N/A',
                'last_name'  => $topCustomer->last_name ?? '',
                'total_spent' => round($topCustomer->total_spent ?? 0, 2),
            ],
            'ratings' => [
                'average' => $shop->shop_rating,
                'count' => $shop->shop_rating_count,
            ],
            'weeklyOrders' => $weeklyOrders,
            'recentOrders' => $recentOrders,
            'reports' => [
                'receipts' => $receivedOrdersCount,
                'reportsFiled' => 5, // Placeholder
                'approved' => 4,     // Placeholder
            ],
            'salesChart' => [
                'labels' => $salesLabels,
                'datasets' => [[
                    'label' => '₱ Sales',
                    'data' => $salesTotals,
                    'borderColor' => '#0d6efd',
                    'backgroundColor' => 'rgba(13, 110, 253, 0.2)',
                    'fill' => true,
                    'tension' => 0.4,
                ]],
            ],
            'topSellingChart' => [
                'labels' => $topSellingChartData->pluck('label'),
                'datasets' => [[
                    'label' => 'Units Sold',
                    'data' => $topSellingChartData->pluck('total'),
                    'backgroundColor' => 'rgba(40, 167, 69, 0.6)',
                    'borderColor' => 'rgba(40, 167, 69, 1)',
                    'borderWidth' => 1,
                ]]
            ]
        ]);
    }
}
