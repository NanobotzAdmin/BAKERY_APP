@php
    $privilageId = \DB::table('pm_interfaces')
        ->select('pm_interfaces.id AS pageId','pm_interface_topic.id AS grupId')
        ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
        ->where('pm_interfaces.path','adminBalanceSheet')
        ->first();
@endphp

@extends('layout', ['pageId' => $privilageId->pageId ?? 0, 'grupId' => $privilageId->grupId ?? 0])

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

<style>
    :root {
        --primary-color: #4f46e5;
        --primary-hover: #4338ca;
        --bg-color: #f3f4f6;
        --card-bg: #ffffff;
        --text-main: #111827;
        --text-muted: #6b7280;
        --success-color: #10b981;
        --danger-color: #ef4444;
        --warning-color: #f59e0b;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --radius-lg: 16px;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: var(--bg-color);
        color: var(--text-main);
    }

    .page-header { margin-bottom: 2rem; }
    .page-title { font-weight: 700; color: var(--text-main); font-size: 1.75rem; margin-bottom: 0.5rem; }
    .page-subtitle { color: var(--text-muted); font-size: 0.95rem; }

    .card-modern {
        background: var(--card-bg);
        border: 1px solid rgba(229, 231, 235, 0.5);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
    }
    .card-modern:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }

    .stat-card { padding: 1.5rem; height: 100%; display: flex; flex-direction: column; justify-content: center; }
    .stat-label { color: var(--text-muted); font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em; }
    .stat-value { font-size: 1.75rem; font-weight: 700; color: var(--text-main); line-height: 1.2; }

    .table-modern { width: 100%; margin-bottom: 0; }
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
    .table-modern tr:hover td { background-color: #f9fafb; }
    
    .group-header td {
        background-color: #fffbeb;
        color: #b45309;
        font-weight: 700;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .sub-group-header td {
        padding-left: 2.5rem !important;
        font-weight: 600;
        color: var(--text-main);
        background-color: #f8fafc;
    }

    .account-row td:first-child { padding-left: 4rem !important; }

    .total-row td {
        background-color: #f0fdf4;
        font-weight: 800;
        border-top: 2px solid #16a34a;
        font-size: 1.05rem;
        color: #166534;
    }

    .btn-icon-modern {
        width: 32px; height: 32px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        color: var(--primary-color); background-color: rgba(79, 70, 229, 0.1);
        border: none; transition: all 0.2s;
    }
    .btn-icon-modern:hover { background-color: var(--primary-color); color: white; }

    /* Date Picker Customization */
    .date-picker-wrapper {
        background: white;
        padding: 5px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        box-shadow: var(--shadow-sm);
    }
    .date-input {
        border: none;
        padding: 8px 12px;
        font-size: 0.9rem;
        color: var(--text-main);
        outline: none;
        background: transparent;
    }
    .date-separator { color: var(--text-muted); margin: 0 5px; }
    .btn-filter {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 8px;
        font-weight: 500;
        margin-left: 10px;
        transition: all 0.2s;
    }
    .btn-filter:hover { background: var(--primary-hover); }

    /* Modal Styling */
    .modal-content-modern { border: none; border-radius: var(--radius-lg); overflow: hidden; }
    .modal-header-modern { background-color: var(--primary-color); color: white; padding: 1.5rem; border-bottom: none; }
    .btn-close-white { filter: brightness(0) invert(1); }
</style>

<div class="container-fluid py-4">

    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-end page-header">
        <div>
            <h1 class="page-title">Balance Sheet</h1>
            <p class="page-subtitle m-0">Financial Position</p>
        </div>
        
        {{-- Date Range Picker --}}
        <div class="d-flex align-items-center">
            <div class="date-picker-wrapper">
                <input type="date" id="startDate" class="date-input" value="{{ date('Y-01-01') }}">
                <span class="date-separator">to</span>
                <input type="date" id="endDate" class="date-input" value="{{ date('Y-m-d') }}">
                <button class="btn-filter" onclick="loadBalanceSheetData()">
                    <i class="fa fa-filter me-2"></i> Load
                </button>
            </div>
            <div class="ms-2">
                <button class="btn btn-white border shadow-sm p-2 rounded-3" onclick="window.print()" title="Print">
                    <i class="fa fa-print text-muted"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row mb-4 g-4">
        <div class="col-md-4">
            <div class="card card-modern stat-card border-start border-4 border-success">
                <span class="stat-label">Total Assets</span>
                <h3 class="stat-value" id="totalAssetsDisplay">RS. 0.00</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-modern stat-card border-start border-4 border-danger">
                <span class="stat-label">Total Liabilities</span>
                <h3 class="stat-value" id="totalLiabilitiesDisplay">RS. 0.00</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-modern stat-card border-start border-4 border-primary">
                <span class="stat-label">Total Equity</span>
                <h3 class="stat-value" id="totalEquityDisplay">RS. 0.00</h3>
            </div>
        </div>
    </div>

    {{-- Main Report Area --}}
    <div class="row">
        <div class="col-12">
            <div class="card card-modern">
                <div class="table-responsive">
                    <table class="table table-modern mb-0" id="balanceSheetTable">
                        <thead>
                            <tr>
                                <th style="width: 60%;">Account</th>
                                <th style="width: 30%; text-align: right;">Balance</th>
                                <th style="width: 10%; text-align: center;">View</th>
                            </tr>
                        </thead>
                        <tbody id="reportContent">
                            {{-- Content will be loaded via JS --}}
                            <tr><td colspan="3" class="text-center p-5 text-muted">Select a date range and click Load to view the report.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- MODAL: Account Details --}}
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content modal-content-modern">
            <div class="modal-header modal-header-modern">
                <div>
                    <h5 class="modal-title fw-bold">Account Ledger</h5>
                    <p class="mb-0 small opacity-75" id="modalAccountName">Account Name</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <table class="table table-striped table-hover mb-0 small">
                    <thead class="bg-light sticky-top">
                        <tr>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Description</th>
                            <th class="px-4 py-3 text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody id="modalContent">
                        {{-- Ledger rows --}}
                    </tbody>
                </table>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('footer')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        loadBalanceSheetData();
    });

    function loadBalanceSheetData() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        
        // Show loading state
        document.getElementById('reportContent').innerHTML = '<tr><td colspan="3" class="text-center p-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Generating Report...</p></td></tr>';

        // Fetch data (Simulated for now, replace with actual AJAX call)
        fetch(`{{ route('financial.balance-sheet-data') }}?start_date=${startDate}&end_date=${endDate}`)
            .then(response => response.json())
            .then(data => {
                renderReport(data);
                updateSummary(data);
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('reportContent').innerHTML = '<tr><td colspan="3" class="text-center p-5 text-danger">Error loading data. Please try again.</td></tr>';
            });
    }

    function renderReport(data) {
        let html = '';

        // Helper to format currency
        const formatMoney = (amount) => {
            return 'RS. ' + new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(amount);
        };

        // ASSETS
        html += `<tr class="group-header"><td colspan="3">ASSETS</td></tr>`;
        
        // Current Assets
        html += `<tr class="sub-group-header"><td colspan="3">Current Assets</td></tr>`;
        data.assets.current.forEach(acc => {
            html += `
                <tr class="account-row">
                    <td>${acc.name}</td>
                    <td class="text-end fw-bold">${formatMoney(acc.balance)}</td>
                    <td class="text-center"><button class="btn-icon-modern mx-auto" onclick="openDetailModal('${acc.name}', '${acc.id}')"><i class="fa fa-eye small"></i></button></td>
                </tr>`;
        });
        html += `<tr class="total-row"><td>Total Current Assets</td><td class="text-end">${formatMoney(data.assets.total_current)}</td><td></td></tr>`;

        // Non-Current Assets
        html += `<tr class="sub-group-header"><td colspan="3">Non-Current Assets</td></tr>`;
        data.assets.non_current.forEach(acc => {
            html += `
                <tr class="account-row">
                    <td>${acc.name}</td>
                    <td class="text-end fw-bold">${formatMoney(acc.balance)}</td>
                    <td class="text-center"><button class="btn-icon-modern mx-auto" onclick="openDetailModal('${acc.name}', '${acc.id}')"><i class="fa fa-eye small"></i></button></td>
                </tr>`;
        });
        html += `<tr class="total-row"><td>Total Non-Current Assets</td><td class="text-end">${formatMoney(data.assets.total_non_current)}</td><td></td></tr>`;
        
        // Total Assets
        html += `<tr class="group-header" style="background-color: #dcfce7 !important;"><td colspan="1" class="text-success">TOTAL ASSETS</td><td class="text-end text-success fs-5">${formatMoney(data.assets.total)}</td><td></td></tr>`;

        // LIABILITIES
        html += `<tr class="group-header mt-4"><td colspan="3">LIABILITIES</td></tr>`;
        
        // Current Liabilities
        html += `<tr class="sub-group-header"><td colspan="3">Current Liabilities</td></tr>`;
        data.liabilities.current.forEach(acc => {
            html += `
                <tr class="account-row">
                    <td>${acc.name}</td>
                    <td class="text-end fw-bold">${formatMoney(acc.balance)}</td>
                    <td class="text-center"><button class="btn-icon-modern mx-auto" onclick="openDetailModal('${acc.name}', '${acc.id}')"><i class="fa fa-eye small"></i></button></td>
                </tr>`;
        });
        html += `<tr class="total-row"><td>Total Current Liabilities</td><td class="text-end">${formatMoney(data.liabilities.total_current)}</td><td></td></tr>`;

        // Non-Current Liabilities
        html += `<tr class="sub-group-header"><td colspan="3">Non-Current Liabilities</td></tr>`;
        data.liabilities.non_current.forEach(acc => {
            html += `
                <tr class="account-row">
                    <td>${acc.name}</td>
                    <td class="text-end fw-bold">${formatMoney(acc.balance)}</td>
                    <td class="text-center"><button class="btn-icon-modern mx-auto" onclick="openDetailModal('${acc.name}', '${acc.id}')"><i class="fa fa-eye small"></i></button></td>
                </tr>`;
        });
        html += `<tr class="total-row"><td>Total Non-Current Liabilities</td><td class="text-end">${formatMoney(data.liabilities.total_non_current)}</td><td></td></tr>`;

        // EQUITY
        html += `<tr class="group-header mt-4"><td colspan="3">EQUITY</td></tr>`;
        data.equity.items.forEach(acc => {
            html += `
                <tr class="account-row">
                    <td>${acc.name}</td>
                    <td class="text-end fw-bold">${formatMoney(acc.balance)}</td>
                    <td class="text-center"><button class="btn-icon-modern mx-auto" onclick="openDetailModal('${acc.name}', '${acc.id}')"><i class="fa fa-eye small"></i></button></td>
                </tr>`;
        });
        html += `<tr class="total-row"><td>Total Equity</td><td class="text-end">${formatMoney(data.equity.total)}</td><td></td></tr>`;

        // Total Liabilities & Equity
        html += `<tr class="group-header" style="background-color: #dbeafe !important;"><td colspan="1" class="text-primary">TOTAL LIABILITIES & EQUITY</td><td class="text-end text-primary fs-5">${formatMoney(data.liabilities.total + data.equity.total)}</td><td></td></tr>`;

        document.getElementById('reportContent').innerHTML = html;
    }

    function updateSummary(data) {
        const formatMoney = (amount) => 'RS. ' + new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(amount);
        document.getElementById('totalAssetsDisplay').innerText = formatMoney(data.assets.total);
        document.getElementById('totalLiabilitiesDisplay').innerText = formatMoney(data.liabilities.total);
        document.getElementById('totalEquityDisplay').innerText = formatMoney(data.equity.total);
    }

    function openDetailModal(name, id) {
        document.getElementById('modalAccountName').innerText = name;
        
        // Simulate fetching ledger details
        let html = '';
        const formatMoney = (amount) => 'RS. ' + new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(amount);
        
        // Generate random transactions
        for(let i=0; i<5; i++) {
            let amount = Math.floor(Math.random() * 10000);
            html += `
                <tr>
                    <td class="px-4 py-3">2024-12-0${i+1}</td>
                    <td class="px-4 py-3">Transaction Ref #${1000+i} - ${name}</td>
                    <td class="px-4 py-3 text-end">${formatMoney(amount)}</td>
                </tr>
            `;
        }
        
        document.getElementById('modalContent').innerHTML = html;
        
        var myModal = new bootstrap.Modal(document.getElementById('detailModal'));
        myModal.show();
    }
</script>
@endsection