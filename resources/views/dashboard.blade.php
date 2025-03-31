@extends('layouts.app')

@section('style')
    <style>
        .nav-tabs .nav-link.active {
            background-color: #f8f9fa;
            border-color: #dee2e6 #dee2e6 #fff;
        }
    </style>
@endsection

@section('content')

    <div class="container-fluid">
        <!-- Page-Title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="row">
                        <div class="col">
                            <h4 class="page-title">Sales</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item active"><a href="{{ route('home') }}">Home</a></li>
                            </ol>
                        </div>
                        <div class="col-auto align-self-center">
                            <div class="row">
                                <div class="dropdown">
                                    <a href="#" class="btn btn-sm btn-outline-primary dropdown-toggle"
                                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        {{ $displayPeriod }}<i class="las la-angle-down ml-1"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item {{ $period == 'today' ? 'active' : '' }}"
                                           href="{{ route('home', ['period' => 'today']) }}">Today</a>
                                        <a class="dropdown-item {{ $period == 'week' ? 'active' : '' }}"
                                           href="{{ route('home', ['period' => 'week']) }}">This Week</a>
                                        <a class="dropdown-item {{ $period == 'month' ? 'active' : '' }}"
                                           href="{{ route('home', ['period' => 'month']) }}">This Month</a>
                                        <a class="dropdown-item {{ $period == 'year' ? 'active' : '' }}"
                                           href="{{ route('home', ['period' => 'year']) }}">This Year</a>
                                    </div>
                                </div>
                                <a href="#" class="btn btn-sm btn-outline-primary ml-2" id="Dash_Date">
                                    <span class="day-name" id="Day_Name">Today:</span>&nbsp;
                                    <span class="" id="Select_date">{{ $todayFormatted }}</span>
                                    <i data-feather="calendar" class="align-self-center icon-xs ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">{{ __('Revenue Status') }}</h4>
                            </div>
                            <div class="col-auto">
                                <ul class="nav nav-tabs tab-nav-right" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#revenue-chart" role="tab">Chart</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#revenue-trend" role="tab">Trend</a>
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
                <div class="row">
                    <div class="col-12 col-lg-6 col-xl">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col text-center">
                                        <span class="h4">R {{ number_format(($weeklySales/100), 2) }}</span>
                                        <h6 class="text-uppercase text-muted mt-2 m-0">Weekly Sales</h6>
                                    </div><!--end col-->
                                </div> <!-- end row -->
                            </div><!--end card-body-->
                        </div> <!--end card-body-->
                    </div><!--end col-->
                    <div class="col-12 col-lg-6 col-xl">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col text-center">
                                        <span class="h4">{{ $ordersPlaced }}</span>
                                        <h6 class="text-uppercase text-muted mt-2 m-0">Orders Placed</h6>
                                    </div><!--end col-->
                                </div> <!-- end row -->
                            </div><!--end card-body-->
                        </div> <!--end card-body-->
                    </div><!--end col-->
                    <div class="col-12 col-lg-6 col-xl">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col text-center">
                                        <span class="h4">{{ $avgItemsPerOrder }}</span>
                                        <h6 class="text-uppercase text-muted mt-2 m-0">{{ __('Items per Order') }}</h6>
                                    </div><!--end col-->
                                </div> <!-- end row -->
                            </div><!--end card-body-->
                        </div> <!--end card-body-->
                    </div><!--end col-->
                    <div class="col-12 col-lg-6 col-xl">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col text-center">
                                        <span class="h4">R {{ number_format($avgOrderValue, 2) }}</span>
                                        <h6 class="text-uppercase text-muted mt-2 m-0">Avg. Order Value</h6>
                                    </div><!--end col-->
                                </div> <!-- end row -->
                            </div><!--end card-body-->
                        </div> <!--end card-->
                    </div><!--end col-->
                </div><!--end row-->
            </div><!-- end col-->

            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <div class="media">
                                    <img src="{{ asset('images/money-beg.png') }}" alt="" class="align-self-center" height="40">
                                    <div class="media-body align-self-center ml-3">
                                        <h6 class="m-0 font-20"> {{ number_format(($totalRevenue/100), 2) }}</h6>
                                        <p class="text-muted mb-0">Total Revenue</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto align-self-center">
                                <p class="mb-0">
                                    @if($revenueChangePercentage > 0)
                                        <span class="text-success"><i class="mdi mdi-trending-up"></i>{{ $revenueChangePercentage }}%</span>
                                    @elseif($revenueChangePercentage < 0)
                                        <span class="text-danger"><i class="mdi mdi-trending-down"></i>{{ abs($revenueChangePercentage) }}%</span>
                                    @else
                                        <span class="text-muted"><i class="mdi mdi-trending-neutral"></i>0.0%</span>
                                    @endif
                                    from previous period
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="apexchart-wrapper">
                                <div id="revenue_spark_chart" class="chart-gutters"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">Sales by Category</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <div id="sales_by_category" class="apex-charts"></div>
                            <h6 class="bg-light-alt py-3 px-2 mb-0">
                                <i data-feather="calendar" class="align-self-center icon-xs mr-1"></i>
                                {{ $formattedStartDate }} to {{ $formattedEndDate }}
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">Recent Orders</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table mb-0">
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
                                            <td>{{ \Carbon\Carbon::parse($earning->date)->format('d M') }}</td>
                                            <td>{{ $earning->order_count }}</td>
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
                                            <td>R {{ number_format(($earning->total_earnings)/100, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No orders in this period</td>
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
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">{{ __('Most Popular Products') }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="thead-light">
                                <tr>
                                    <th class="border-top-0">Product</th>
                                    <th class="border-top-0">Price</th>
                                    <th class="border-top-0">Quantity Sold</th>
                                    <th class="border-top-0">Status</th>
                                    <th class="border-top-0">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($popularProducts as $product)
                                    <tr>
                                        <td>
                                            <div class="media">
                                                <img src="{{ asset('images/products/01.png') }}" height="30" class="mr-3 align-self-center rounded" alt="...">
                                                <div class="media-body align-self-center">
                                                    <h6 class="m-0">{{ $product->StockItemName }}</h6>
                                                    <a href="#" class="font-12 text-primary">ID: {{ $product->StockCode }}</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>R{{ number_format($product->SellingPrice, 2) }}
                                            @if($product->AverageCostPrice > 0 && $product->AverageCostPrice < $product->SellingPrice)
                                                <del class="text-muted font-10">R{{ number_format($product->AverageCostPrice, 2) }}</del>
                                            @endif
                                        </td>
                                        <td>{{ $product->total_quantity }}</td>
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
                                            <a href="{{ route('products.edit', $product->id) }}" class="mr-2"><i class="las la-pen text-info font-18"></i></a>
                                            <a href="{{ route('products.show', $product->id) }}"><i class="las la-eye text-success font-18"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No products sold in this period</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">

            @include('dashboard.partials.top_customers')

            @include('dashboard.partials.product-reorder')
        </div>

    </div>

@endsection

@section('script')
    <script src="{{ asset('plugins/apex-charts/apexcharts.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Get context
            var currentPeriod = "{{ $period }}";

            // Revenue Chart
            function initRevenueChart() {
                var options = {
                    chart: {
                        height: 350,
                        type: 'bar',
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '30%',
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
                    colors: ['#2a76f4'],
                    series: [{
                        name: 'Revenue',
                        data: []
                    }],
                    xaxis: {
                        categories: [],
                    },
                    yaxis: {
                        title: {
                            text: 'Revenue (R)'
                        },
                        labels: {
                            formatter: function(val) {
                                return "R" + parseFloat(val).toFixed(0);
                            }
                        },
                    },
                    fill: {
                        opacity: 1
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return "R" + parseFloat(val).toFixed(2);
                            }
                        }
                    }
                };

                var revenueChart = new ApexCharts(
                    document.querySelector("#revenue_chart"),
                    options
                );
                revenueChart.render();

                // Now load the data via AJAX
                $.ajax({
                    url: "{{ route('dashboard.sales.chart-data') }}",
                    method: "GET",
                    data: { period: currentPeriod },
                    success: function(response) {
                        var categories = [];
                        var data = [];

                        $.each(response.revenueData, function(index, item) {
                            categories.push(item.label);
                            data.push(item.value);
                        });

                        revenueChart.updateOptions({
                            xaxis: {
                                categories: categories
                            },
                            series: [{
                                name: 'Revenue',
                                data: data
                            }]
                        });
                    }
                });

                return revenueChart;
            }

            // Revenue Trend Chart
            function initRevenueTrendChart() {
                var options = {
                    chart: {
                        height: 350,
                        type: 'line',
                        toolbar: {
                            show: false
                        },
                        zoom: {
                            enabled: false
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    colors: ['#2a76f4'],
                    series: [{
                        name: 'Revenue Trend',
                        data: {!! json_encode(array_column($revenueTrend, 'y')) !!}
                    }],
                    xaxis: {
                        categories: {!! json_encode(array_column($revenueTrend, 'x')) !!},
                    },
                    yaxis: {
                        labels: {
                            formatter: function(val) {
                                return "R" + parseFloat(val).toFixed(0);
                            }
                        },
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return "R" + parseFloat(val).toFixed(2);
                            }
                        }
                    }
                };

                var trendChart = new ApexCharts(
                    document.querySelector("#revenue_trend_chart"),
                    options
                );
                trendChart.render();
                return trendChart;
            }

            // Revenue Spark Chart
            function initRevenueSparkChart() {
                var options = {
                    chart: {
                        type: 'area',
                        height: 80,
                        sparkline: {
                            enabled: true
                        },
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 2
                    },
                    fill: {
                        opacity: 0.05
                    },
                    colors: ['#2a76f4'],
                    series: [{
                        name: 'Revenue',
                        data: {!! json_encode(array_column($revenueTrend, 'y')) !!}
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
                                return "R" + parseFloat(val).toFixed(2);
                            }
                        },
                        marker: {
                            show: false
                        }
                    }
                };

                var sparkChart = new ApexCharts(
                    document.querySelector("#revenue_spark_chart"),
                    options
                );
                sparkChart.render();
                return sparkChart;
            }

            // Sales By Category Chart
            function initSalesByCategoryChart() {
                var options = {
                    chart: {
                        height: 300,
                        type: 'donut',
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '65%'
                            }
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    colors: ['#2a76f4', '#0acf97', '#fa5c7c', '#ffbc00', '#5da5fa'],
                    series: [],
                    labels: [],
                    legend: {
                        show: true,
                        position: 'bottom',
                        horizontalAlign: 'center',
                        verticalAlign: 'middle',
                        floating: false,
                        fontSize: '14px',
                        offsetX: 0,
                        offsetY: 5
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return "R" + parseFloat(val).toFixed(2);
                            }
                        }
                    }
                };

                var categoryChart = new ApexCharts(
                    document.querySelector("#sales_by_category"),
                    options
                );
                categoryChart.render();

                // Load data via AJAX
                $.ajax({
                    url: "{{ route('dashboard.sales.chart-data') }}",
                    method: "GET",
                    data: { period: currentPeriod },
                    success: function(response) {
                        var labels = [];
                        var data = [];

                        $.each(response.categoryData, function(index, item) {
                            labels.push(item.label);
                            data.push(item.value);
                        });

                        categoryChart.updateOptions({
                            labels: labels,
                            series: data
                        });
                    }
                });

                return categoryChart;
            }

            // Initialize all charts
            var revenueChart = initRevenueChart();
            var trendChart = initRevenueTrendChart();
            var sparkChart = initRevenueSparkChart();
            var categoryChart = initSalesByCategoryChart();

            // Handle period change
            $('.dropdown-item').on('click', function() {
                // Already handled via href links
            });
        });
    </script>

@endsection
