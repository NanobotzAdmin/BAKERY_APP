@php
    $pageMeta = isset($privilageId) ? $privilageId : (object) ['pageId' => 'dashboard', 'grupId' => 'mainGroup'];

    // --- DUMMY DATA FOR DASHBOARD ---
    
    // 1. Branch Performance Data
    $branchSales = [
        ['name' => 'Colombo Main', 'sales' => 150000, 'target' => 120000, 'orders' => 45],
        ['name' => 'Kandy Outlet', 'sales' => 85000, 'target' => 90000, 'orders' => 22], // Missed target
        ['name' => 'Negombo Outlet', 'sales' => 95000, 'target' => 80000, 'orders' => 30],
        ['name' => 'Galle Outlet', 'sales' => 60000, 'target' => 75000, 'orders' => 18],
    ];

    // 2. Recent Orders (Mini Table)
    $recentOrders = [
        ['id' => 'ORD-1001', 'branch' => 'Colombo', 'amount' => '4,500', 'status' => 'pending', 'time' => '10 mins ago'],
        ['id' => 'ORD-1002', 'branch' => 'Kandy', 'amount' => '1,200', 'status' => 'baking', 'time' => '25 mins ago'],
        ['id' => 'ORD-1003', 'branch' => 'Walk-in', 'amount' => '850', 'status' => 'ready', 'time' => '40 mins ago'],
        ['id' => 'ORD-1004', 'branch' => 'Negombo', 'amount' => '12,000', 'status' => 'pending', 'time' => '1 hour ago'],
    ];

@endphp

@extends('layout', ['pageId' => $pageMeta->pageId, 'grupId' => $pageMeta->grupId])

@section('content')

    {{-- Chart.js Library --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* Modern Cards */
        .ibox { box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-radius: 8px; border: none; margin-bottom: 25px; }
        .ibox-title { border-top-left-radius: 8px; border-top-right-radius: 8px; border-bottom: 1px solid #f0f0f0; }
        .ibox-content { border-bottom-left-radius: 8px; border-bottom-right-radius: 8px; }
        
        /* Stats Widgets */
        .stat-card { color: #fff; border-radius: 8px; padding: 20px; position: relative; overflow: hidden; height: 100%; transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .bg-gradient-primary { background: linear-gradient(45deg, #1ab394, #1bc3a2); }
        .bg-gradient-info { background: linear-gradient(45deg, #23c6c8, #3dd5d7); }
        .bg-gradient-warning { background: linear-gradient(45deg, #f8ac59, #fbc07d); }
        .bg-gradient-danger { background: linear-gradient(45deg, #ed5565, #ef6876); }
        
        .stat-icon { position: absolute; right: 15px; top: 20px; font-size: 3rem; opacity: 0.2; }
        .stat-value { font-size: 28px; font-weight: 700; margin: 10px 0 5px; }
        .stat-label { font-size: 14px; opacity: 0.9; text-transform: uppercase; letter-spacing: 1px; }

        /* Status Badges */
        .badge-status { padding: 4px 8px; font-size: 10px; border-radius: 4px; text-transform: uppercase; font-weight: 600; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-baking { background: #cce5ff; color: #004085; }
        .status-ready { background: #d4edda; color: #155724; }
        
        /* Progress Bars */
        .progress-bar-success { background-color: #1ab394; }
        .progress-bar-warning { background-color: #f8ac59; }
    </style>

    {{-- HEADER WITH DATE FILTER --}}
    <div class="row wrapper border-bottom white-bg page-heading align-items-center">
        <div class="col-sm-8">
            <h2><b>Admin Dashboard</b></h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item active"><strong>Overview</strong></li>
            </ol>
        </div>
        <div class="col-sm-4 text-right">
            <div class="btn-group mt-3">
                <button class="btn btn-white active" type="button">Today</button>
                <button class="btn btn-white" type="button">This Week</button>
                <button class="btn btn-white" type="button">This Month</button>
            </div>
        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">
        
        {{-- ROW 1: TOP STATS CARDS --}}
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="stat-card bg-gradient-primary">
                    <div class="stat-label">Total Revenue (Today)</div>
                    <div class="stat-value">Rs 390,000</div>
                    <small><i class="fa fa-arrow-up"></i> 12% vs yesterday</small>
                    <i class="fa fa-line-chart stat-icon"></i>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card bg-gradient-info">
                    <div class="stat-label">Total Orders</div>
                    <div class="stat-value">115</div>
                    <small>45 Online / 70 Walk-in</small>
                    <i class="fa fa-shopping-basket stat-icon"></i>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card bg-gradient-warning">
                    <div class="stat-label">Pending Production</div>
                    <div class="stat-value">42 Items</div>
                    <small>Kitchen Load: High</small>
                    <i class="fa fa-birthday-cake stat-icon"></i>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card bg-gradient-danger">
                    <div class="stat-label">Low Stock Alerts</div>
                    <div class="stat-value">3 Items</div>
                    <small>Attention Required</small>
                    <i class="fa fa-exclamation-triangle stat-icon"></i>
                </div>
            </div>
        </div>

        {{-- ROW 2: CHARTS --}}
        <div class="row">
            {{-- Branch Sales Comparison (Bar Chart) --}}
            <div class="col-lg-8">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Branch-wise Sales Performance</h5>
                        <div class="ibox-tools">
                            <span class="label label-primary pull-right">Live Data</span>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div>
                            <canvas id="branchSalesChart" height="120"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Product Category Mix (Doughnut Chart) --}}
            <div class="col-lg-4">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Sales by Category</h5>
                    </div>
                    <div class="ibox-content">
                        <canvas id="categoryChart" height="200"></canvas>
                        <div class="text-center mt-3">
                            <small class="text-muted">Top Seller: <strong>Butter Cake</strong></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ROW 3: TABLES --}}
        <div class="row">
            
            {{-- Branch Targets Table --}}
            <div class="col-lg-6">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Daily Target vs Achievement</h5>
                    </div>
                    <div class="ibox-content">
                        <table class="table table-hover no-margins">
                            <thead>
                                <tr>
                                    <th>Branch</th>
                                    <th class="text-right">Target</th>
                                    <th class="text-right">Actual</th>
                                    <th class="text-center">Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($branchSales as $b)
                                    @php
                                        $percent = ($b['sales'] / $b['target']) * 100;
                                        $color = $percent >= 100 ? 'progress-bar-success' : 'progress-bar-warning';
                                        $textClass = $percent >= 100 ? 'text-navy' : 'text-warning';
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $b['name'] }}</strong></td>
                                        <td class="text-right text-muted">{{ number_format($b['target']) }}</td>
                                        <td class="text-right font-bold">{{ number_format($b['sales']) }}</td>
                                        <td style="width: 30%;">
                                            <small class="pull-right {{ $textClass }} font-bold">{{ number_format($percent, 0) }}%</small>
                                            <div class="progress progress-mini">
                                                <div style="width: {{ $percent }}%;" class="progress-bar {{ $color }}"></div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Recent Activity Feed --}}
            <div class="col-lg-6">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Recent Orders</h5>
                        <div class="ibox-tools">
                            <a class="btn btn-xs btn-white" href="/orders">View All</a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Branch</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                        <tr>
                                            <td><a href="#" class="text-navy font-bold">#{{ $order['id'] }}</a></td>
                                            <td>{{ $order['branch'] }}</td>
                                            <td>Rs {{ $order['amount'] }}</td>
                                            <td><span class="badge-status status-{{ $order['status'] }}">{{ ucfirst($order['status']) }}</span></td>
                                            <td class="text-muted"><small><i class="fa fa-clock-o"></i> {{ $order['time'] }}</small></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- CHART JS INITIALIZATION --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            
            // 1. BRANCH SALES CHART (BAR)
            const ctxBranch = document.getElementById('branchSalesChart').getContext('2d');
            
            // Extracting data from PHP
            const branchNames = {!! json_encode(array_column($branchSales, 'name')) !!};
            const branchValues = {!! json_encode(array_column($branchSales, 'sales')) !!};

            new Chart(ctxBranch, {
                type: 'bar',
                data: {
                    labels: branchNames,
                    datasets: [{
                        label: 'Revenue (Rs)',
                        data: branchValues,
                        backgroundColor: '#1ab394',
                        borderColor: '#1ab394',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f0f0f0' } },
                        x: { grid: { display: false } }
                    },
                    plugins: { legend: { display: false } }
                }
            });

            // 2. CATEGORY CHART (DOUGHNUT)
            const ctxCat = document.getElementById('categoryChart').getContext('2d');
            
            new Chart(ctxCat, {
                type: 'doughnut',
                data: {
                    labels: ['Cakes & Gateaux', 'Short Eats', 'Breads/Buns', 'Beverages'],
                    datasets: [{
                        data: [45, 30, 15, 10], // Dummy percentage data
                        backgroundColor: [
                            '#1ab394', // Green (Primary)
                            '#f8ac59', // Orange
                            '#23c6c8', // Teal
                            '#ed5565'  // Red
                        ],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    cutout: '70%', // Makes it a thin ring
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
                    }
                }
            });
        });
    </script>

@endsection