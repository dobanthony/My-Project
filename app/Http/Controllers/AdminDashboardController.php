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
use Rap2hpoutre\FastExcel\FastExcel;

class AdminDashboardController extends Controller
{
    /**
     * -----------------------------------------------------
     *  SHARED FUNCTION: Stats for charts + Excel export
     * -----------------------------------------------------
     */
    private function getStats($filter, $now)
    {
        // WEEKLY (7 days)
        if ($filter === 'week') {
            return collect(range(0, 6))->map(function ($daysAgo) use ($now) {
                $date = $now->copy()->subDays($daysAgo)->startOfDay();

                $orders = Order::whereDate('created_at', $date)->get();
                $sales = $orders->sum(fn($o) => optional($o->product)->price * $o->quantity);

                return [
                    'month' => $date->format('D'),
                    'orders' => $orders->count(),
                    'sales' => round($sales, 2),
                ];
            })->reverse()->values();
        }

        // YEARLY (12 months)
        if ($filter === 'year') {
            return collect(range(1, 12))->map(function ($month) use ($now) {
                $orders = Order::whereMonth('created_at', $month)
                    ->whereYear('created_at', $now->year)
                    ->get();

                $sales = $orders->sum(fn($o) => optional($o->product)->price * $o->quantity);

                return [
                    'month' => Carbon::create()->month($month)->format('M'),
                    'orders' => $orders->count(),
                    'sales' => round($sales, 2),
                ];
            })->values();
        }

        // MONTHLY (by day)
        return collect(range(1, $now->daysInMonth))->map(function ($day) use ($now) {
            $date = Carbon::createFromDate($now->year, $now->month, $day)->startOfDay();

            $orders = Order::whereDate('created_at', $date)->get();
            $sales = $orders->sum(fn($o) => optional($o->product)->price * $o->quantity);

            return [
                'month' => $date->format('d'),
                'orders' => $orders->count(),
                'sales' => round($sales, 2),
            ];
        })->values();
    }

    /**
     * -----------------------------------------------------
     *  DASHBOARD INDEX PAGE
     * -----------------------------------------------------
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $filter = $request->input('filter', 'week');
        $now = Carbon::now()->timezone('Asia/Manila');

        // ❤️ SINGLE SOURCE OF TRUTH
        $stats = $this->getStats($filter, $now);

        // TOP METRICS
        $topShop = User::select('users.first_name')
            ->join('shops', 'shops.user_id', '=', 'users.id')
            ->join('products', 'products.shop_id', '=', 'shops.id')
            ->join('orders', 'orders.product_id', '=', 'products.id')
            ->where('users.role', 'seller')
            ->groupBy('users.id', 'users.first_name')
            ->orderByRaw('COUNT(orders.id) DESC')
            ->value('users.first_name');

        $topCustomer = User::select('users.first_name')
            ->join('orders', 'orders.user_id', '=', 'users.id')
            ->where('users.role', 'user')
            ->groupBy('users.id', 'users.first_name')
            ->orderByRaw('COUNT(orders.id) DESC')
            ->value('users.first_name');

        $topProduct = Product::select('products.name')
            ->join('orders', 'orders.product_id', '=', 'products.id')
            ->groupBy('products.id', 'products.name')
            ->orderByRaw('SUM(orders.quantity) DESC')
            ->value('products.name');

        return Inertia::render('Admin/AdminDash', [
            'filter' => $filter,
            'monthlyStats' => $stats,
            'metrics' => [
                'totalOrders' => Order::count(),
                'totalUsers' => User::count(),
                'totalSellers' => User::where('role', 'seller')->count(),
                'totalAnnouncements' => Announcement::count(),
                'totalProducts' => Product::count(),
                'pendingSellerApplications' => User::where('seller_status', 'pending')->count(),
            ],
            'topMetrics' => [
                'topShop' => $topShop ?? 'N/A',
                'topCustomer' => $topCustomer ?? 'N/A',
                'topProduct' => $topProduct ?? 'N/A',
            ],
        ]);
    }

    /**
     * -----------------------------------------------------
     *  EXCEL EXPORT (FILTERED)
     * -----------------------------------------------------
     */
    public function export(Request $request)
{
    $filter = $request->input('filter', 'week');
    $now = Carbon::now()->timezone('Asia/Manila');

    // Reuse stats
    $stats = $this->getStats($filter, $now);

    // METRICS
    $metrics = [
        ['Metric' => 'Total Orders', 'Value' => Order::count()],
        ['Metric' => 'Total Users', 'Value' => User::count()],
        ['Metric' => 'Total Sellers', 'Value' => User::where('role', 'seller')->count()],
        ['Metric' => 'Total Products', 'Value' => Product::count()],
        ['Metric' => 'Announcements', 'Value' => Announcement::count()],
        ['Metric' => 'Pending Seller Applications', 'Value' => User::where('seller_status', 'pending')->count()],
    ];

    // SALES SHEET
    $salesSheet = collect($stats)->map(fn($s) => [
        'Label' => $s['month'],
        'Sales (₱)' => $s['sales'],
    ]);

    // ORDERS SHEET
    $ordersSheet = collect($stats)->map(fn($s) => [
        'Label' => $s['month'],
        'Orders' => $s['orders'],
    ]);

    // ----------- EXPORT USING FASTEXCEL (3 FILES) ------------------
    $exportPath = storage_path("app/public/dashboard_export/");
    if (!is_dir($exportPath)) mkdir($exportPath, 0777, true);

    $metricsFile = $exportPath . "Metrics.xlsx";
    $salesFile   = $exportPath . "Sales.xlsx";
    $ordersFile  = $exportPath . "Orders.xlsx";

    (new \Rap2hpoutre\FastExcel\FastExcel($metrics))->export($metricsFile);
    (new \Rap2hpoutre\FastExcel\FastExcel($salesSheet))->export($salesFile);
    (new \Rap2hpoutre\FastExcel\FastExcel($ordersSheet))->export($ordersFile);

    // ----------- ZIP THE FILES ------------------------------------
    $zipFile = storage_path("app/public/AdminDashboardExport.zip");
    $zip = new \ZipArchive;

    if ($zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE)) {
        $zip->addFile($metricsFile, "Metrics.xlsx");
        $zip->addFile($salesFile, "Sales.xlsx");
        $zip->addFile($ordersFile, "Orders.xlsx");
        $zip->close();
    }

    return response()->download($zipFile)->deleteFileAfterSend(true);
}

}