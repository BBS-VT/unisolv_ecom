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

        // Conversion rate (placeholder - depends on how you calculate this)
        // Typically this would be orders / quotes or visits
        $conversionRate = 0;
        $quotes = 0; // This would come from your quotes model

        if ($quotes > 0) {
            $conversionRate = round(($ordersPlaced / $quotes) * 100, 1);
        } else {
            // Placeholder value if you don't track quotes
            $conversionRate = 82.8;
        }

        // Average order value
        $avgOrderValue = $ordersPlaced > 0 ? round($totalRevenue / $ordersPlaced, 2) : 0;

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
        /*$salesByCategory = OrdersItem::join('orders', 'orders_items.OrderID', '=', 'orders.id')
            ->join('products', 'orders_items.StockItem', '=', 'products.StockCode')
            ->join('product_categories', 'products.category_id', '=', 'product_categories.id')
            ->where('orders.company_id', $currentCompany->id)
            ->whereBetween('orders.OrderDate', [$startDate, $endDate])
            ->select(
                'product_categories.StockGroupName',
                DB::raw('SUM(orders_items.Quantity * orders_items.UnitPrice) as total_sales')
            )
            ->groupBy('product_categories.StockGroupName')
            ->orderBy('total_sales', 'desc')
            ->get();*/

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

        return view('dashboard', compact(
            'revenueByMonth',
            'weeklySales',
            'ordersPlaced',
            'conversionRate',
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
            'period'
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
                    DB::raw('SUM(OrderTotal) as value')
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
                    DB::raw('SUM(OrderTotal) as value')
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
                    DB::raw('SUM(OrderTotal) as value')
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
                    DB::raw('SUM(OrderTotal) as value')
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
        $categoryData = OrdersItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('product_categories', 'products.category_id', '=', 'product_categories.id')
            ->where('orders.company_id', $currentCompany->id)
            ->whereBetween('orders.OrderDate', [$startDate, $endDate])
            ->select(
                'product_categories.name',
                DB::raw('SUM(order_items.quantity * order_items.price) as total_sales')
            )
            ->groupBy('product_categories.name')
            ->orderBy('total_sales', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->name,
                    'value' => round($item->total_sales, 2)
                ];
            });

        return response()->json([
            'revenueData' => $revenueData,
            'categoryData' => $categoryData
        ]);
    }
    /*public function index(Request $request, User $user)
    {

        $user = $request->user();
        $company = $user->currentCompany();

        // Dashboard Stats
        $customersCount = Customer::findByCompany($company->id)->count();
        $invoicesCount = Order::findByCompany($company->id)->count();

        // Due/Open Orders Invoices and Estimates
        $dueOrders = Order::findByCompany($company->id)->active()->where('OrderStatusID', '=', 1)->take(5)->latest()->get();

        // Financial Year Starts-Ends
        $financialYearStarts = $company->getSetting('financial_month_starts');
        $financialYearEnds = $company->getSetting('financial_month_ends');

        // Create Carbon Instances from Financial Year
        $dateStarts = Carbon::now()->month($financialYearStarts)->startOfMonth();
        $dateEnds = Carbon::now()->month($financialYearEnds)->endOfMonth();

        // if the date ends is smaller than date start, add one year to date ends
        if($dateEnds->lt($dateStarts)){
            $dateEnds->addYear(1)->endOfMonth();
        }

        // Create Period from given dates
        $period = CarbonPeriod::since($dateStarts)->months(1)->until($dateEnds);

        return view('dashboard', [
            'customersCount' => $customersCount,
            'ordersCount'    => $invoicesCount,
            'dueOrders'      => $dueOrders,
            'currency_code'  => $company->currency->code,
        ]);
    }*/

    public function sales()
    {
        return view('dashboards.salesrep');
    }


}
