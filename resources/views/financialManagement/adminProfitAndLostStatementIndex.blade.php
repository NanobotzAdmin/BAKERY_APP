@php
    // Keep your original logic
    $privilageId = \DB::table('pm_interfaces')
        ->select('pm_interfaces.id AS pageId','pm_interface_topic.id AS grupId')
        ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
        ->where('pm_interfaces.path','adminProfitAndLostStatement')
        ->first();

    // Mock Data for UI Visualization (In a real app, pass these from Controller)
    $revenue = 150000;
    $cogs = 45000;
    $grossProfit = $revenue - $cogs;
    $expenses = 35000;
    $netProfit = $grossProfit - $expenses;
@endphp

@extends('layout', ['pageId' => $privilageId->pageId ?? 0, 'grupId' => $privilageId->grupId ?? 0])

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

<style>
    :root {
        --primary-color: #4f46e5; /* Indigo 600 */
        --primary-hover: #4338ca; /* Indigo 700 */
        --bg-color: #f3f4f6;
        --card-bg: #ffffff;
        --text-main: #111827;
        --text-muted: #6b7280;
        --success-color: #10b981;
        --danger-color: #ef4444;
        --warning-color: #f59e0b;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --radius-lg: 16px;
        --radius-xl: 24px;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: var(--bg-color);
        color: var(--text-main);
    }

    .page-header {
        margin-bottom: 2rem;
    }

    .page-title {
        font-weight: 700;
        color: var(--text-main);
        font-size: 1.75rem;
        margin-bottom: 0.5rem;
    }

    .page-subtitle {
        color: var(--text-muted);
        font-size: 0.95rem;
    }

    .btn-action {
        border-radius: 10px;
        padding: 8px 16px;
        font-weight: 500;
        transition: all 0.2s;
        border: 1px solid #e5e7eb;
        background: white;
        color: var(--text-main);
    }
    .btn-action:hover {
        background-color: #f9fafb;
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }

    .card-modern {
        background: var(--card-bg);
        border: 1px solid rgba(229, 231, 235, 0.5);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
        overflow: hidden;
    }
    .card-modern:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .stat-card {
        padding: 1.5rem;
        height: 100%;
        display: flex;
        align-items: center;
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-right: 1.25rem;
        flex-shrink: 0;
    }

    .stat-label {
        color: var(--text-muted);
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-main);
        line-height: 1.2;
    }

    .stat-trend {
        font-size: 0.75rem;
        font-weight: 600;
        margin-top: 0.25rem;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    /* Filter Section */
    .filter-container {
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    .form-label-modern {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
        text-transform: uppercase;
    }
    .form-control-modern {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 0.6rem 1rem;
        font-size: 0.95rem;
        transition: all 0.2s;
    }
    .form-control-modern:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        outline: none;
    }
    .btn-primary-modern {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
        border-radius: 10px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-primary-modern:hover {
        background-color: var(--primary-hover);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }

    /* Table Styling */
    .table-modern {
        width: 100%;
        margin-bottom: 0;
    }
    .table-modern th {
        background-color: #f9fafb;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }
    .table-modern td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f3f4f6;
        color: var(--text-main);
        font-size: 0.95rem;
    }
    .table-modern tr:last-child td {
        border-bottom: none;
    }
    .table-modern tr:hover td {
        background-color: #f9fafb;
    }
    
    .pl-header-row td {
        background-color: #f3f4f6;
        font-weight: 700;
        color: var(--text-main);
        font-size: 0.9rem;
    }
    .pl-sub-row td:first-child {
        padding-left: 3rem;
        position: relative;
    }
    .pl-sub-row td:first-child::before {
        content: '';
        position: absolute;
        left: 1.5rem;
        top: 50%;
        width: 6px;
        height: 6px;
        background-color: #d1d5db;
        border-radius: 50%;
        transform: translateY(-50%);
    }
    .pl-total-row td {
        background-color: #fff;
        border-top: 2px solid #e5e7eb;
        font-weight: 700;
        font-size: 1.1rem;
        color: var(--text-main);
    }
    
    .btn-icon-modern {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-color);
        background-color: rgba(79, 70, 229, 0.1);
        border: none;
        transition: all 0.2s;
    }
    .btn-icon-modern:hover {
        background-color: var(--primary-color);
        color: white;
    }

    /* Modal Styling */
    .modal-content-modern {
        border: none;
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-lg);
        overflow: hidden;
    }
    .modal-header-modern {
        background-color: var(--primary-color);
        color: white;
        padding: 1.5rem;
        border-bottom: none;
    }
    .modal-title-modern {
        font-weight: 700;
        font-size: 1.25rem;
    }
    .btn-close-white {
        filter: brightness(0) invert(1);
    }
</style>

<div class="container-fluid py-4">
    
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-end page-header">
        <div>
            <h1 class="page-title">Profit & Loss Statement</h1>
            <p class="page-subtitle m-0">Detailed financial performance report and analysis</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-action"><i class="fa fa-print me-2"></i> Print</button>
            <button class="btn btn-action"><i class="fa fa-file-excel me-2"></i> Export Excel</button>
            <button class="btn btn-action"><i class="fa fa-file-pdf me-2"></i> Export PDF</button>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card card-modern filter-container">
        <form class="row g-4 align-items-end">
            <div class="col-md-3">
                <label class="form-label-modern">From Date</label>
                <input type="date" class="form-control form-control-modern" value="{{ date('Y-m-01') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label-modern">To Date</label>
                <input type="date" class="form-control form-control-modern" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label-modern">Branch / Location</label>
                <select class="form-select form-control-modern">
                    <option>All Branches</option>
                    <option>Main Office</option>
                    <option>Warehouse A</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary-modern w-100"><i class="fa fa-filter me-2"></i> Generate Report</button>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4 g-4">
        <div class="col-xl-3 col-md-6">
            <div class="card card-modern stat-card">
                <div class="stat-icon" style="background-color: rgba(79, 70, 229, 0.1); color: var(--primary-color);">
                    <i class="fa fa-chart-line"></i>
                </div>
                <div>
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-value">RS. {{ number_format($revenue, 2) }}</div>
                    <div class="stat-trend text-success"><i class="fa fa-arrow-up"></i> 12% vs last month</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-modern stat-card">
                <div class="stat-icon" style="background-color: rgba(245, 158, 11, 0.1); color: var(--warning-color);">
                    <i class="fa fa-shopping-bag"></i>
                </div>
                <div>
                    <div class="stat-label">Cost of Goods</div>
                    <div class="stat-value">RS. {{ number_format($cogs, 2) }}</div>
                    <div class="stat-trend text-muted">30% of Revenue</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-modern stat-card">
                <div class="stat-icon" style="background-color: rgba(239, 68, 68, 0.1); color: var(--danger-color);">
                    <i class="fa fa-wallet"></i>
                </div>
                <div>
                    <div class="stat-label">Expenses</div>
                    <div class="stat-value">RS. {{ number_format($expenses, 2) }}</div>
                    <div class="stat-trend text-danger"><i class="fa fa-arrow-down"></i> 2% vs last month</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-modern stat-card" style="background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%); border-color: var(--success-color);">
                <div class="stat-icon" style="background-color: rgba(16, 185, 129, 0.1); color: var(--success-color);">
                    <i class="fa fa-coins"></i>
                </div>
                <div>
                    <div class="stat-label">Net Profit</div>
                    <div class="stat-value text-success">RS. {{ number_format($netProfit, 2) }}</div>
                    <div class="stat-trend text-success fw-bold">Healthy Margin</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Table -->
        <div class="col-lg-8">
            <div class="card card-modern h-100">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h5 class="card-title mb-0 fw-bold text-dark">Statement Details</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 50%">Category</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-center" style="width: 100px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Revenue -->
                                <tr class="pl-header-row">
                                    <td colspan="3">Income / Revenue</td>
                                </tr>
                                <tr class="pl-sub-row">
                                    <td>Product Sales</td>
                                    <td class="text-end fw-bold">RS. 16,476.00</td>
                                    <td class="text-center"><button class="btn-icon-modern" onclick="openDetails('Product Sales')"><i class="fa fa-eye"></i></button></td>
                                </tr>
                                <tr class="pl-sub-row">
                                    <td>Service Income</td>
                                    <td class="text-end fw-bold">RS. 30,000.00</td>
                                    <td class="text-center"><button class="btn-icon-modern" onclick="openDetails('Service Income')"><i class="fa fa-eye"></i></button></td>
                                </tr>
                                <tr class="pl-total-row" style="color: var(--primary-color);">
                                    <td>Total Revenue</td>
                                    <td class="text-end">RS. {{ number_format($revenue, 2) }}</td>
                                    <td></td>
                                </tr>

                                <!-- COGS -->
                                <tr class="pl-header-row mt-2">
                                    <td colspan="3">Cost of Goods Sold (COGS)</td>
                                </tr>
                                <tr class="pl-sub-row">
                                    <td>Raw Materials</td>
                                    <td class="text-end text-danger">(RS. 25,000.00)</td>
                                    <td class="text-center"><button class="btn-icon-modern" onclick="openDetails('Raw Materials')"><i class="fa fa-eye"></i></button></td>
                                </tr>
                                <tr class="pl-sub-row">
                                    <td>Direct Labor</td>
                                    <td class="text-end text-danger">(RS. 20,000.00)</td>
                                    <td class="text-center"><button class="btn-icon-modern" onclick="openDetails('Direct Labor')"><i class="fa fa-eye"></i></button></td>
                                </tr>
                                <tr class="pl-total-row">
                                    <td>Gross Profit</td>
                                    <td class="text-end">RS. {{ number_format($grossProfit, 2) }}</td>
                                    <td></td>
                                </tr>

                                <!-- Expenses -->
                                <tr class="pl-header-row">
                                    <td colspan="3">Operating Expenses</td>
                                </tr>
                                <tr class="pl-sub-row">
                                    <td>Rent & Utilities</td>
                                    <td class="text-end text-danger">(RS. 10,000.00)</td>
                                    <td class="text-center"><button class="btn-icon-modern" onclick="openDetails('Rent & Utilities')"><i class="fa fa-eye"></i></button></td>
                                </tr>
                                <tr class="pl-sub-row">
                                    <td>Salaries & Wages</td>
                                    <td class="text-end text-danger">(RS. 15,000.00)</td>
                                    <td class="text-center"><button class="btn-icon-modern" onclick="openDetails('Salaries & Wages')"><i class="fa fa-eye"></i></button></td>
                                </tr>
                                <tr class="pl-sub-row">
                                    <td>Marketing</td>
                                    <td class="text-end text-danger">(RS. 10,000.00)</td>
                                    <td class="text-center"><button class="btn-icon-modern" onclick="openDetails('Marketing')"><i class="fa fa-eye"></i></button></td>
                                </tr>

                                <!-- Net Profit -->
                                <tr class="pl-total-row" style="background-color: #f0fdf4; color: var(--success-color); font-size: 1.2rem;">
                                    <td>Net Profit / (Loss)</td>
                                    <td class="text-end">RS. {{ number_format($netProfit, 2) }}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts & Insights -->
        <div class="col-lg-4">
            <div class="card card-modern h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark">Performance Trend</h5>
                </div>
                <div class="card-body">
                    <div class="position-relative mb-4" style="height: 300px;">
                        <canvas id="profitChart"></canvas>
                    </div>
                    
                    <div>
                        <h6 class="fw-bold text-uppercase text-muted small mb-3">Key Insights</h6>
                        <div class="d-flex align-items-center justify-content-between p-3 rounded-3 mb-2" style="background-color: #fef2f2;">
                            <div class="d-flex align-items-center">
                                <i class="fa fa-exclamation-circle text-danger me-3 fs-5"></i>
                                <div>
                                    <div class="small text-muted fw-bold">Highest Expense</div>
                                    <div class="fw-bold text-dark">Salaries</div>
                                </div>
                            </div>
                            <span class="badge bg-danger rounded-pill">Critical</span>
                        </div>

                        <div class="d-flex align-items-center justify-content-between p-3 rounded-3 mb-2" style="background-color: #f0fdf4;">
                            <div class="d-flex align-items-center">
                                <i class="fa fa-calendar-check text-success me-3 fs-5"></i>
                                <div>
                                    <div class="small text-muted fw-bold">Best Month</div>
                                    <div class="fw-bold text-dark">October</div>
                                </div>
                            </div>
                            <span class="badge bg-success rounded-pill">Top</span>
                        </div>

                        <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="background-color: #eff6ff;">
                            <div class="d-flex align-items-center">
                                <i class="fa fa-percent text-primary me-3 fs-5"></i>
                                <div>
                                    <div class="small text-muted fw-bold">Profit Margin</div>
                                    <div class="fw-bold text-dark">32%</div>
                                </div>
                            </div>
                            <span class="badge bg-primary rounded-pill">Good</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-modern">
            <div class="modal-header modal-header-modern">
                <h5 class="modal-title modal-title-modern" id="modalTitle">Category Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex justify-content-between mb-4">
                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">Date Range: This Month</span>
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3"><i class="fa fa-download me-1"></i> Export CSV</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-modern">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Description / Ref</th>
                                <th>Vendor/Client</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="modalContent">
                            </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('footer')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // 1. Chart Initialization
    const ctx = document.getElementById('profitChart').getContext('2d');
    const profitChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Expenses', 'COGS', 'Net Profit'],
            datasets: [{
                data: [35000, 45000, 70000],
                backgroundColor: ['#dc3545', '#ffc107', '#198754'],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // 2. Modal Logic
    function openDetails(categoryName) {
        const modalTitle = document.getElementById('modalTitle');
        const modalBody = document.getElementById('modalContent');
        const myModal = new bootstrap.Modal(document.getElementById('detailModal'));

        // Set Title
        modalTitle.innerText = categoryName + " - Breakdown";

        // Mock Data Generation (Replace with AJAX call in real app)
        let html = '';
        for(let i=1; i<=5; i++) {
            let amount = (Math.random() * 1000).toFixed(2);
            html += `
                <tr>
                    <td>2023-10-0${i}</td>
                    <td>Transaction REF-${1000+i}</td>
                    <td>Vendor Name ${i}</td>
                    <td class="text-end fw-bold">RS. ${amount}</td>
                </tr>
            `;
        }
        
        modalBody.innerHTML = html;
        myModal.show();
    }
</script>

@endsection