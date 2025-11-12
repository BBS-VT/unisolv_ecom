@extends('layouts.master')

@push('style')
    <style>
        .nav-tabs .nav-link.active {
            background-color: #f8f9fa;
            border-color: #dee2e6 #dee2e6 #fff;
        }

        /* Ensure ApexCharts containers have proper height */
        .apex-charts {
            min-height: 350px;
        }

        .chart-gutters {
            min-height: 80px;
        }

        /* Fix badge styles for Bootstrap 5 */
        .badge-soft-success {
            background-color: rgba(10, 207, 151, 0.18);
            color: #0acf97;
            font-weight: 600;
        }

        .badge-soft-danger {
            background-color: rgba(250, 92, 124, 0.18);
            color: #fa5c7c;
            font-weight: 600;
        }

        /* Enhanced Page Title Area */
        .page-title-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.15);
        }

        .page-title-box h4 {
            color: #fff;
            font-weight: 600;
            margin-bottom: 0;
        }

        .page-title-box .btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #fff;
            font-weight: 500;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .page-title-box .btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .page-title-box .btn i {
            font-size: 1rem;
        }

        /* Enhanced Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        .card-header {
            background: linear-gradient(to right, #f8f9fa 0%, #ffffff 100%);
            border-bottom: 2px solid #e9ecef;
            padding: 1.25rem 1.5rem;
        }

        .card-title {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0;
        }

        /* Stats Cards Enhancement */
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
        }

        .stats-card .card-body {
            padding: 1.5rem;
        }

        .stats-card .h4 {
            font-size: 1.75rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.5rem;
        }

        .stats-card h6 {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.875rem;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        /* Alternate gradient colors for variety */
        .stats-card-1 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stats-card-2 {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .stats-card-3 {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .stats-card-4 {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        /* Revenue Info Card */
        .revenue-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .revenue-card .card-body {
            padding: 1.5rem;
        }

        .revenue-card img {
            filter: brightness(0) invert(1);
            opacity: 0.9;
        }

        .revenue-card h6 {
            color: white;
            font-weight: 600;
        }

        .revenue-card p {
            color: rgba(255, 255, 255, 0.9);
        }

        .revenue-card .font-20 {
            font-size: 1.5rem;
            font-weight: 700;
        }

        /* Tab Navigation Enhancement */
        .nav-tabs {
            border-bottom: 2px solid #e9ecef;
        }

        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link:hover {
            color: #667eea;
            border-color: transparent;
        }

        .nav-tabs .nav-link.active {
            color: #667eea;
            background-color: transparent;
            border-bottom: 3px solid #667eea;
            font-weight: 600;
        }

        /* Chart Card Specific */
        .chart-card {
            background: white;
        }

        .chart-card .card-body {
            padding: 1.5rem;
        }

        /* Category Chart Enhancement */
        .bg-light-alt {
            background-color: #f8f9fa;
            border-radius: 6px;
            font-size: 0.875rem;
            color: #6c757d;
        }

        /* Table Enhancement */
        .table thead th {
            font-weight: 600;
            font-size: 0.875rem;
            color: #495057;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody tr {
            transition: background-color 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Responsive adjustments */
        @media (max-width: 991px) {
            .page-title-box {
                padding: 1rem;
            }

            .page-title-box h4 {
                font-size: 1.25rem;
                margin-bottom: 1rem;
            }

            .page-title-right {
                margin-top: 1rem;
            }
        }

        @media (max-width: 575px) {
            .stats-card .h4 {
                font-size: 1.5rem;
            }

            .page-title-box .btn {
                font-size: 0.875rem;
                padding: 0.375rem 0.75rem;
            }
        }

        /* Loading state for charts */
        .apex-charts.loading {
            position: relative;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .apex-charts.loading::after {
            content: "Loading chart...";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #6c757d;
            font-size: 0.875rem;
        }

        /* Icon improvements */
        .media img {
            border-radius: 8px;
            padding: 0.5rem;
            background: rgba(255, 255, 255, 0.2);
        }

        /* Trending icons */
        .mdi {
            font-size: 1.1rem;
            margin-right: 0.25rem;
        }

        /* Enhance dropdown */
        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 0.5rem;
        }

        .dropdown-item {
            border-radius: 6px;
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
            color: #667eea;
        }

        .dropdown-item.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
    </style>
@endpush

@section('content')
    <!-- Enhanced Page Title Section -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-0">
                        <i class="mdi mdi-chart-line me-2"></i>{{ __('Sales Dashboard') }}
                    </h4>
                </div>

                <div class="page-title-right">
                    <div class="d-flex flex-wrap gap-2">
                        <!-- Period Dropdown -->
                        <div class="dropdown">
                            <button type="button" class="btn btn-sm dropdown-toggle"
                                    data-bs-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false">
                                <i class="mdi mdi-calendar-clock me-1"></i>
                                {{ $displayPeriod }}
                                <i class="mdi mdi-chevron-down ms-1"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item {{ $period == 'today' ? 'active' : '' }}"
                                   href="{{ route('home', ['period' => 'today']) }}">
                                    <i class="mdi mdi-calendar-today me-2"></i>Today
                                </a>
                                <a class="dropdown-item {{ $period == 'week' ? 'active' : '' }}"
                                   href="{{ route('home', ['period' => 'week']) }}">
                                    <i class="mdi mdi-calendar-week me-2"></i>This Week
                                </a>
                                <a class="dropdown-item {{ $period == 'month' ? 'active' : '' }}"
                                   href="{{ route('home', ['period' => 'month']) }}">
                                    <i class="mdi mdi-calendar-month me-2"></i>This Month
                                </a>
                                <a class="dropdown-item {{ $period == 'year' ? 'active' : '' }}"
                                   href="{{ route('home', ['period' => 'year']) }}">
                                    <i class="mdi mdi-calendar me-2"></i>This Year
                                </a>
                            </div>
                        </div>

                        <!-- Date Display -->
                        <button type="button" class="btn btn-sm" id="Dash_Date">
                            <i class="mdi mdi-calendar-range me-1"></i>
                            <span id="Select_date">{{ $todayFormatted }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Charts Row -->
    <div class="row">
        <!-- Main Revenue Chart -->
        <div class="col-xl-9">
            <div class="card chart-card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">
                                <i class="mdi mdi-chart-bar me-2 text-primary"></i>
                                {{ __('Revenue Status') }}
                            </h4>
                        </div>
                        <div class="col-auto">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#revenue-chart" role="tab">
                                        <i class="mdi mdi-chart-bar me-1"></i>Chart
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#revenue-trend" role="tab">
                                        <i class="mdi mdi-chart-line me-1"></i>Trend
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="revenue-chart">
                            <div id="revenue_chart" class="apex-charts"></div>
                        </div>
                        <div class="tab-pane" id="revenue-trend">
                            <div id="revenue_trend_chart" class="apex-charts"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards Row -->
            <div class="row g-3">
                <div class="col-12 col-lg-6 col-xl-3">
                    <div class="card stats-card stats-card-1">
                        <div class="card-body">
                            <div class="text-center">
                                <div class="mb-2">
                                    <i class="mdi mdi-currency-usd mdi-48px" style="opacity: 0.3;"></i>
                                </div>
                                <span class="h4 d-block">R {{ number_format(($weeklySales/100), 2) }}</span>
                                <h6 class="text-uppercase mt-2 mb-0">Weekly Sales</h6>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6 col-xl-3">
                    <div class="card stats-card stats-card-2">
                        <div class="card-body">
                            <div class="text-center">
                                <div class="mb-2">
                                    <i class="mdi mdi-cart-outline mdi-48px" style="opacity: 0.3;"></i>
                                </div>
                                <span class="h4 d-block">{{ $ordersPlaced }}</span>
                                <h6 class="text-uppercase mt-2 mb-0">Orders Placed</h6>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6 col-xl-3">
                    <div class="card stats-card stats-card-3">
                        <div class="card-body">
                            <div class="text-center">
                                <div class="mb-2">
                                    <i class="mdi mdi-package-variant mdi-48px" style="opacity: 0.3;"></i>
                                </div>
                                <span class="h4 d-block">{{ $avgItemsPerOrder }}</span>
                                <h6 class="text-uppercase mt-2 mb-0">{{ __('Items per Order') }}</h6>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6 col-xl-3">
                    <div class="card stats-card stats-card-4">
                        <div class="card-body">
                            <div class="text-center">
                                <div class="mb-2">
                                    <i class="mdi mdi-chart-line-variant mdi-48px" style="opacity: 0.3;"></i>
                                </div>
                                <span class="h4 d-block">R {{ number_format($avgOrderValue, 2) }}</span>
                                <h6 class="text-uppercase mt-2 mb-0">Avg. Order Value</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Cards -->
        <div class="col-xl-3">
            <!-- Total Revenue Card -->
            <div class="card revenue-card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="flex-grow-1">
                            <p class="text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px; opacity: 0.9;">
                                Total Revenue
                            </p>
                            <h3 class="mb-0 font-weight-bold">R {{ number_format(($totalRevenue/100), 2) }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <div class="avatar-title rounded" style="background: rgba(255,255,255,0.2);">
                                <i class="mdi mdi-cash-multiple mdi-24px"></i>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center">
                        @if($revenueChangePercentage > 0)
                            <span class="badge bg-white text-success me-2">
                                <i class="mdi mdi-trending-up"></i>{{ $revenueChangePercentage }}%
                            </span>
                        @elseif($revenueChangePercentage < 0)
                            <span class="badge bg-white text-danger me-2">
                                <i class="mdi mdi-trending-down"></i>{{ abs($revenueChangePercentage) }}%
                            </span>
                        @else
                            <span class="badge bg-white text-muted me-2">
                                <i class="mdi mdi-trending-neutral"></i>0.0%
                            </span>
                        @endif
                        <small style="opacity: 0.9;">vs previous period</small>
                    </div>
                </div>
                <div class="px-3 pb-3">
                    <div id="revenue_spark_chart" class="chart-gutters"></div>
                </div>
            </div>

            <!-- Sales by Category Card -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="mdi mdi-shape me-2 text-primary"></i>
                        Sales by Category
                    </h4>
                </div>
                <div class="card-body">
                    <div id="sales_by_category" class="apex-charts" style="min-height: 300px;"></div>
                    <div class="bg-light-alt py-3 px-3 mt-3 text-center">
                        <i class="mdi mdi-calendar-range me-1"></i>
                        <small>{{ $formattedStartDate }} to {{ $formattedEndDate }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="mdi mdi-receipt me-2 text-primary"></i>
                        {{ __('Recent Orders') }}
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle table-nowrap mb-0">
                            <thead class="thead-light">
                            <tr>
                                <th class="border-top-0">Date</th>
                                <th class="border-top-0">Item Count</th>
                                <th class="border-top-0">Discount</th>
                                <th class="border-top-0">Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($dailyEarnings as $earning)
                                <tr>
                                    <td><strong>{{ \Carbon\Carbon::parse($earning->date)->format('d M') }}</strong></td>
                                    <td>
                                        <span class="badge bg-primary">{{ $earning->order_count }}</span>
                                    </td>
                                    <td class="text-danger">
                                        @php
                                            $discount = 0; // TODO: - replace with actual discount calculation
                                        @endphp
                                        @if($discount > 0)
                                            -R{{ number_format($discount, 2) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td><strong>R {{ number_format(($earning->total_earnings)/100, 2) }}</strong></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="mdi mdi-information-outline mdi-24px d-block mb-2"></i>
                                        No orders in this period
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="mdi mdi-star-circle me-2 text-primary"></i>
                        {{ __('Most Popular Products') }}
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="thead-light">
                            <tr>
                                <th class="border-top-0">Product</th>
                                <th class="border-top-0">Price</th>
                                <th class="border-top-0">Qty Sold</th>
                                <th class="border-top-0">Status</th>
                                <th class="border-top-0">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($popularProducts as $product)
                                <tr>
                                    <td>
                                        <div>
                                            <h6 class="mb-1">{{ $product->StockItemName }}</h6>
                                            <small class="text-muted">ID: {{ $product->StockCode }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>R{{ number_format($product->SellingPrice, 2) }}</strong>
                                        @if($product->AverageCostPrice > 0 && $product->AverageCostPrice < $product->SellingPrice)
                                            <br><del class="text-muted small">R{{ number_format($product->AverageCostPrice, 2) }}</del>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $product->total_quantity }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $inStock = true; // TODO: Placeholder - check stock on hand
                                        @endphp
                                        @if($inStock)
                                            <span class="badge badge-soft-success px-2">In Stock</span>
                                        @else
                                            <span class="badge badge-soft-danger px-2">Out of Stock</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('products.edit', $product->id) }}"
                                               class="btn btn-sm btn-soft-primary"
                                               title="Edit">
                                                <i class="bx bx-pencil"></i>
                                            </a>
                                            <a href="{{ route('products.show', $product->id) }}"
                                               class="btn btn-sm btn-soft-success"
                                               title="View">
                                                <i class="bx bx-show"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="mdi mdi-information-outline mdi-24px d-block mb-2"></i>
                                        No products sold in this period
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Dashboard Sections -->
    <div class="row">
        @include('dashboard.partials.top_customers')
        @include('dashboard.partials.product-reorder')
    </div>

@endsection

@push('scripts')
    <!-- ApexCharts -->
    <script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            console.log('Dashboard JavaScript initializing...');

            // Get context
            var currentPeriod = "{{ $period }}";
            console.log('Current period:', currentPeriod);

            // Revenue Chart
            function initRevenueChart() {
                console.log('Initializing revenue chart...');

                var options = {
                    chart: {
                        height: 350,
                        type: 'bar',
                        toolbar: {
                            show: false
                        },
                        animations: {
                            enabled: true
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '50%',
                            borderRadius: 8,
                            distributed: false
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    colors: ['#667eea'],
                    series: [{
                        name: 'Revenue',
                        data: [0] // Start with placeholder
                    }],
                    xaxis: {
                        categories: ['Loading...'],
                        labels: {
                            style: {
                                colors: '#8e8da4'
                            }
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Revenue (R)',
                            style: {
                                color: '#8e8da4'
                            }
                        },
                        labels: {
                            formatter: function(val) {
                                return "R" + parseFloat(val || 0).toFixed(0);
                            },
                            style: {
                                colors: '#8e8da4'
                            }
                        },
                    },
                    fill: {
                        opacity: 1,
                        type: 'gradient',
                        gradient: {
                            shade: 'light',
                            type: "vertical",
                            shadeIntensity: 0.5,
                            inverseColors: false,
                            opacityFrom: 0.85,
                            opacityTo: 0.85,
                            stops: [0, 100]
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return "R" + parseFloat(val || 0).toFixed(2);
                            }
                        }
                    },
                    grid: {
                        borderColor: '#f1f1f1',
                        strokeDashArray: 4
                    },
                    noData: {
                        text: 'Loading...'
                    }
                };

                try {
                    var revenueChart = new ApexCharts(
                        document.querySelector("#revenue_chart"),
                        options
                    );
                    revenueChart.render();
                    console.log('Revenue chart rendered successfully');

                    // Now load the data via AJAX
                    $.ajax({
                        url: "{{ route('dashboard.sales.chart-data') }}",
                        method: "GET",
                        data: { period: currentPeriod },
                        success: function(response) {
                            console.log('Revenue data received:', response);

                            var categories = [];
                            var data = [];

                            if (response.revenueData && response.revenueData.length > 0) {
                                $.each(response.revenueData, function(index, item) {
                                    categories.push(item.label);
                                    data.push(parseFloat(item.value) || 0);
                                });
                            } else {
                                categories = ['No Data'];
                                data = [0];
                            }

                            revenueChart.updateOptions({
                                xaxis: {
                                    categories: categories
                                },
                                series: [{
                                    name: 'Revenue',
                                    data: data
                                }]
                            });

                            console.log('Revenue chart updated with data');
                        },
                        error: function(xhr, status, error) {
                            console.error('Error loading revenue data:', error);
                            console.error('Response:', xhr.responseText);
                        }
                    });

                    return revenueChart;
                } catch (error) {
                    console.error('Error initializing revenue chart:', error);
                    return null;
                }
            }

            // Revenue Trend Chart
            function initRevenueTrendChart() {
                console.log('Initializing revenue trend chart...');

                var trendData = {!! json_encode($revenueTrend ?? []) !!};
                console.log('Trend data:', trendData);

                var yData = [];
                var xData = [];

                if (trendData && trendData.length > 0) {
                    yData = trendData.map(item => parseFloat(item.y) || 0);
                    xData = trendData.map(item => item.x);
                } else {
                    yData = [0];
                    xData = ['No Data'];
                }

                var options = {
                    chart: {
                        height: 350,
                        type: 'area',
                        toolbar: {
                            show: false
                        },
                        zoom: {
                            enabled: false
                        },
                        animations: {
                            enabled: true
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    colors: ['#667eea'],
                    series: [{
                        name: 'Revenue Trend',
                        data: yData
                    }],
                    xaxis: {
                        categories: xData,
                        labels: {
                            style: {
                                colors: '#8e8da4'
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            formatter: function(val) {
                                return "R" + parseFloat(val || 0).toFixed(0);
                            },
                            style: {
                                colors: '#8e8da4'
                            }
                        },
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.7,
                            opacityTo: 0.2,
                            stops: [0, 90, 100]
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return "R" + parseFloat(val || 0).toFixed(2);
                            }
                        }
                    },
                    grid: {
                        borderColor: '#f1f1f1',
                        strokeDashArray: 4
                    }
                };

                try {
                    var trendChart = new ApexCharts(
                        document.querySelector("#revenue_trend_chart"),
                        options
                    );
                    trendChart.render();
                    console.log('Trend chart rendered successfully');
                    return trendChart;
                } catch (error) {
                    console.error('Error initializing trend chart:', error);
                    return null;
                }
            }

            // Revenue Spark Chart
            function initRevenueSparkChart() {
                console.log('Initializing spark chart...');

                var trendData = {!! json_encode($revenueTrend ?? []) !!};
                var sparkData = trendData && trendData.length > 0 ? trendData.map(item => parseFloat(item.y) || 0) : [0];

                var options = {
                    chart: {
                        type: 'area',
                        height: 80,
                        sparkline: {
                            enabled: true
                        },
                        animations: {
                            enabled: true
                        }
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 2
                    },
                    fill: {
                        opacity: 0.3,
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.7,
                            opacityTo: 0.2,
                            stops: [0, 90, 100]
                        }
                    },
                    colors: ['#ffffff'],
                    series: [{
                        name: 'Revenue',
                        data: sparkData
                    }],
                    tooltip: {
                        fixed: {
                            enabled: false
                        },
                        x: {
                            show: false
                        },
                        y: {
                            title: {
                                formatter: function(seriesName) {
                                    return '';
                                }
                            },
                            formatter: function(val) {
                                return "R" + parseFloat(val || 0).toFixed(2);
                            }
                        },
                        marker: {
                            show: false
                        }
                    }
                };

                try {
                    var sparkChart = new ApexCharts(
                        document.querySelector("#revenue_spark_chart"),
                        options
                    );
                    sparkChart.render();
                    console.log('Spark chart rendered successfully');
                    return sparkChart;
                } catch (error) {
                    console.error('Error initializing spark chart:', error);
                    return null;
                }
            }

            // Sales By Category Chart
            function initSalesByCategoryChart() {
                console.log('Initializing sales by category chart...');

                var options = {
                    chart: {
                        height: 300,
                        type: 'donut',
                        animations: {
                            enabled: true
                        }
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                                labels: {
                                    show: false
                                }
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val) {
                            return val.toFixed(1) + "%";
                        },
                        style: {
                            fontSize: '12px',
                            fontWeight: 'bold'
                        },
                        dropShadow: {
                            enabled: false
                        }
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['#fff']
                    },
                    colors: ['#667eea', '#764ba2', '#f093fb', '#4facfe', '#43e97b'],
                    series: [1], // Placeholder
                    labels: ['Loading...'],
                    legend: {
                        show: true,
                        position: 'bottom',
                        horizontalAlign: 'center',
                        floating: false,
                        fontSize: '13px',
                        offsetY: 5,
                        markers: {
                            width: 12,
                            height: 12,
                            radius: 4
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return "R" + parseFloat(val || 0).toFixed(2);
                            }
                        }
                    },
                    noData: {
                        text: 'Loading...'
                    }
                };

                try {
                    var categoryChart = new ApexCharts(
                        document.querySelector("#sales_by_category"),
                        options
                    );
                    categoryChart.render();
                    console.log('Category chart rendered successfully');

                    // Load data via AJAX
                    $.ajax({
                        url: "{{ route('dashboard.sales.chart-data') }}",
                        method: "GET",
                        data: { period: currentPeriod },
                        success: function(response) {
                            console.log('Category data received:', response);

                            var labels = [];
                            var data = [];

                            if (response.categoryData && response.categoryData.length > 0) {
                                $.each(response.categoryData, function(index, item) {
                                    labels.push(item.label);
                                    data.push(parseFloat(item.value) || 0);
                                });
                            } else {
                                labels = ['No Data'];
                                data = [1];
                            }

                            categoryChart.updateOptions({
                                labels: labels,
                                series: data
                            });

                            console.log('Category chart updated with data');
                        },
                        error: function(xhr, status, error) {
                            console.error('Error loading category data:', error);
                            console.error('Response:', xhr.responseText);
                        }
                    });

                    return categoryChart;
                } catch (error) {
                    console.error('Error initializing category chart:', error);
                    return null;
                }
            }

            // Initialize all charts with error handling
            try {
                var revenueChart = initRevenueChart();
                var trendChart = initRevenueTrendChart();
                var sparkChart = initRevenueSparkChart();
                var categoryChart = initSalesByCategoryChart();

                console.log('All charts initialized');

                // Handle tab switching to refresh charts
                $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                    window.dispatchEvent(new Event('resize'));
                });
            } catch (error) {
                console.error('Error during chart initialization:', error);
            }
        });
    </script>

@endpush
