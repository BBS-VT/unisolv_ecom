<?php


namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrdersItem;
use App\Models\Product;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $currentCompany = $user->currentCompany();

        // Date ranges
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $startOfYear = Carbon::now()->startOfYear();
        $endOfYear = Carbon::now()->endOfYear();

        // Filter by selected period
        $period = $request->input('period', 'month'); // Default to month

        switch ($period) {
            case 'today':
                $startDate = $today;
                $endDate = $today;
                $displayPeriod = 'Today';
                break;
            case 'week':
                $startDate = $startOfWeek;
                $endDate = $endOfWeek;
                $displayPeriod = 'This Week';
                break;
            case 'month':
                $startDate = $startOfMonth;
                $endDate = $endOfMonth;
                $displayPeriod = 'This Month';
                break;
            case 'year':
                $startDate = $startOfYear;
                $endDate = $endOfYear;
                $displayPeriod = 'This Year';
                break;
            default:
                $startDate = $startOfMonth;
                $endDate = $endOfMonth;
                $displayPeriod = 'This Month';
        }

        $ordersQuery = Order::where('company_id', $currentCompany->id)
            ->whereBetween('OrderDate', [$startDate, $endDate]);

        $revenueByMonth = Order::where('company_id', $currentCompany->id)
            ->whereYear('OrderDate', Carbon::now()->year)
            ->select(
                DB::raw('MONTH(OrderDate) as month'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy(DB::raw('MONTH(OrderDate)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month')
            ->map(function ($item) {
                return round($item->total, 2);
            });

        for ($i = 1; $i <= 12; $i++) {
            if (!isset($revenueByMonth[$i])) {
                $revenueByMonth[$i] = 0;
            }
        }

        $revenueByMonth = $revenueByMonth->sortKeys();

        // Weekly sales
        $weeklySales = Order::where('company_id', $currentCompany->id)
            ->whereBetween('OrderDate', [$startOfWeek, $endOfWeek])
            ->sum('total');

        // Orders placed
        $ordersPlaced = $ordersQuery->count();

        // Total revenue
        $totalRevenue = $ordersQuery->sum('total');

        // Revenue change percentage (compared to previous period)
        $previousPeriodStart = (clone $startDate)->subDays($endDate->diffInDays($startDate) + 1);
        $previousPeriodEnd = (clone $startDate)->subDay();

        $previousPeriodRevenue = Order::where('company_id', $currentCompany->id)
            ->whereBetween('OrderDate', [$previousPeriodStart, $previousPeriodEnd])
            ->sum('total');

        $revenueChangePercentage = 0;
        if ($previousPeriodRevenue > 0) {
            $revenueChangePercentage = round((($totalRevenue - $previousPeriodRevenue) / $previousPeriodRevenue) * 100, 1);
        }

        // Average Items per order
        $totalOrders = DB::table('orders')
            ->where('company_id', $currentCompany->id)
            ->whereBetween('OrderDate', [$startDate, $endDate])
            ->count();

        $totalUniqueItems = DB::table('orders_items')
            ->join('orders', 'orders_items.OrderID', '=', 'orders.id')
            ->where('orders.company_id', $currentCompany->id)
            ->whereBetween('orders.OrderDate', [$startDate, $endDate])
            ->distinct('StockItem')
            ->count('StockItem');

        $avgItemsPerOrder = $totalOrders > 0 ? round($totalUniqueItems / $totalOrders, 1) : 0;

        // Average order value
        $avgOrderValue = $ordersPlaced > 0 ? round(($totalRevenue / $ordersPlaced) / 100, 2) : 0;

        // Get daily earnings for the selected period (limited to last 7 days for display)
        $dailyEarnings = Order::where('company_id', $currentCompany->id)
            ->whereBetween('OrderDate', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(OrderDate) as date'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total) as total_earnings')
            )
            ->groupBy(DB::raw('DATE(OrderDate)'))
            ->orderBy('date', 'desc')
            ->limit(7)
            ->get();

        // Most popular products
        $popularProducts = OrdersItem::join('orders', 'orders_items.OrderID', '=', 'orders.id')
            ->join('products', 'orders_items.StockItem', '=', 'products.StockCode')
            ->where('orders.company_id', $currentCompany->id)
            ->whereBetween('orders.OrderDate', [$startDate, $endDate])
            ->select(
                'products.id',
                'products.StockCode',
                'products.StockItemName',
                'products.SellingPrice',
                'products.AverageCostPrice',
                DB::raw('SUM(orders_items.Quantity) as total_quantity')
            )
            ->groupBy('products.id', 'products.StockCode', 'products.StockItemName', 'products.SellingPrice', 'products.AverageCostPrice')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->get();

        // Sales by product category (for pie chart)
        $salesByCategory = OrdersItem::join('orders', 'orders_items.OrderID', '=', 'orders.id')
            ->join('products', 'orders_items.StockItem', '=', 'products.StockCode')
            ->join('product_product_category', 'products.id', '=', 'product_product_category.product_id')
            ->join('product_categories', 'product_product_category.product_category_id', '=', 'product_categories.id')
            ->where('orders.company_id', $currentCompany->id)
            ->whereBetween('orders.OrderDate', [$startDate, $endDate])
            ->select(
                'product_categories.StockGroupName',
                DB::raw('SUM(orders_items.Quantity * orders_items.UnitPrice) as total_sales')
            )
            ->groupBy('product_categories.StockGroupName')
            ->orderBy('total_sales', 'desc')
            ->get();

        // Revenue trend (last 6 months for sparkline chart)
        $revenueTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthlyRevenue = Order::where('company_id', $currentCompany->id)
                ->whereYear('OrderDate', $month->year)
                ->whereMonth('OrderDate', $month->month)
                ->sum('total');

            $revenueTrend[] = [
                'x' => $month->format('M'),
                'y' => round($monthlyRevenue, 2)
            ];
        }

        // Format dates for display
        $formattedStartDate = $startDate->format('d M Y');
        $formattedEndDate = $endDate->format('d M Y');
        $todayFormatted = $today->format('M d');

        $topCustomers = $this->getTopCustomers($currentCompany, $startDate, $endDate);
        $productReorderRates = $this->getProductReorderRates($currentCompany, $startDate, $endDate);

        return view('dashboard', compact(
            'revenueByMonth',
            'weeklySales',
            'ordersPlaced',
            'avgItemsPerOrder',
            'avgOrderValue',
            'totalRevenue',
            'revenueChangePercentage',
            'dailyEarnings',
            'popularProducts',
            'revenueTrend',
            'displayPeriod',
            'formattedStartDate',
            'formattedEndDate',
            'todayFormatted',
            'period',
            'topCustomers',
            'productReorderRates'
        ));
    }

    /**
     * Get chart data for AJAX requests
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChartData(Request $request)
    {
        $user = $request->user();
        $currentCompany = $user->currentCompany();
        $period = $request->input('period', 'month');

        // Determine date range based on period
        switch ($period) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today();
                break;
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            default:
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
        }

        // Get revenue data for the selected period
        if ($period === 'year') {
            // Monthly data for yearly view
            $revenueData = Order::where('company_id', $currentCompany->id)
                ->whereYear('OrderDate', Carbon::now()->year)
                ->select(
                    DB::raw('MONTH(OrderDate) as label'),
                    DB::raw('(SUM(total) / 100) as value')
                )
                ->groupBy(DB::raw('MONTH(OrderDate)'))
                ->orderBy('label')
                ->get()
                ->map(function ($item) {
                    $monthName = Carbon::create(null, $item->label, 1)->format('M');
                    return [
                        'label' => $monthName,
                        'value' => round($item->value, 2)
                    ];
                });
        } elseif ($period === 'month') {
            // Daily data for monthly view
            $revenueData = Order::where('company_id', $currentCompany->id)
                ->whereBetween('OrderDate', [$startDate, $endDate])
                ->select(
                    DB::raw('DAY(OrderDate) as label'),
                    DB::raw('(SUM(total) / 100) as value')
                )
                ->groupBy(DB::raw('DAY(OrderDate)'))
                ->orderBy('label')
                ->get()
                ->map(function ($item) {
                    return [
                        'label' => $item->label,
                        'value' => round($item->value, 2)
                    ];
                });
        } elseif ($period === 'week') {
            // Daily data for weekly view
            $revenueData = Order::where('company_id', $currentCompany->id)
                ->whereBetween('OrderDate', [$startDate, $endDate])
                ->select(
                    DB::raw('DAYOFWEEK(OrderDate) as day_of_week'),
                    DB::raw('DATE(OrderDate) as date'),
                    DB::raw('(SUM(total) / 100) as value')
                )
                ->groupBy('day_of_week', 'date')
                ->orderBy('date')
                ->get()
                ->map(function ($item) {
                    $dayName = Carbon::parse($item->date)->format('D');
                    return [
                        'label' => $dayName,
                        'value' => round($item->value, 2)
                    ];
                });
        } else {
            // Hourly data for daily view
            $revenueData = Order::where('company_id', $currentCompany->id)
                ->whereDate('OrderDate', $startDate)
                ->select(
                    DB::raw('HOUR(OrderDate) as label'),
                    DB::raw('(SUM(total) / 100) as value')
                )
                ->groupBy(DB::raw('HOUR(OrderDate)'))
                ->orderBy('label')
                ->get()
                ->map(function ($item) {
                    return [
                        'label' => $item->label . ':00',
                        'value' => round($item->value, 2)
                    ];
                });
        }

        // Sales by category data
        $categoryData = OrdersItem::join('orders', 'orders_items.OrderID', '=', 'orders.id')
            ->join('products', 'orders_items.StockItem', '=', 'products.StockCode')
            ->join('product_product_category as ppc', 'ppc.product_id', '=', 'products.id')
            ->join('product_categories as pc', 'pc.id', '=', 'ppc.product_category_id')
            ->where('orders.company_id', $currentCompany->id)
            ->whereBetween('orders.OrderDate', [$startDate, $endDate])
            ->select(
                'pc.StockGroupName as category',
                DB::raw('SUM(orders_items.Quantity * orders_items.UnitPrice) as total_sales')
            )
            ->groupBy('pc.StockGroupName')
            ->orderBy('total_sales', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->category,
                    'value' => round($item->total_sales, 2)
                ];
            });

        return response()->json([
            'revenueData' => $revenueData,
            'categoryData' => $categoryData
        ]);
    }

    /**
     * Get top customers by order value
     */
    private function getTopCustomers($currentCompany, $startDate, $endDate)
    {
        return DB::table('orders')
            ->join('customers', 'orders.CustomerID', '=', 'customers.acc_code')
            ->select(
                'customers.id',
                'customers.CustomerName',
                'customers.acc_main',
                DB::raw('COUNT(DISTINCT orders.id) as order_count'),
                DB::raw('(SUM(orders.total) / 100) as total_spent'),
                DB::raw('(SUM(orders.total) /100) / COUNT(DISTINCT orders.id) as avg_order_value')
            )
            ->where('orders.company_id', $currentCompany->id)
            ->whereBetween('orders.OrderDate', [$startDate, $endDate])
            ->groupBy('customers.id', 'customers.CustomerName', 'customers.acc_main')
            ->orderBy('total_spent', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get product reorder rates
     */
    private function getProductReorderRates($currentCompany, $startDate, $endDate)
    {
        $reorderRateQuery = "
            SELECT
                p.id,
                p.StockItemName,
                p.StockCode,
                COUNT(DISTINCT o.id) as order_count,
                COUNT(DISTINCT o.customer_id) as unique_customers,
                ROUND(COUNT(DISTINCT o.id) / COUNT(DISTINCT o.customer_id), 1) as reorder_ratio
            FROM
                products p
            JOIN
                orders_items oi ON p.id = oi.StockItem
            JOIN
                orders o ON oi.OrderID = o.id
            WHERE
                o.company_id = ? AND
                o.OrderDate BETWEEN ? AND ?
            GROUP BY
                p.id, p.StockItemName, p.StockCode
            HAVING
                COUNT(DISTINCT o.customer_id) > 1
            ORDER BY
                reorder_ratio DESC
            LIMIT 5
        ";
        $reorderRateQuery = Order::all();

        //return DB::select($reorderRateQuery, [
        //    $currentCompany->id,
        //    $startDate->format('Y-m-d H:i:s'),
        //    $endDate->format('Y-m-d H:i:s')
        //]);
    }


    public function sales()
    {
        return view('dashboards.salesrep');
    }


}
