@php
    $privilageId = \DB::table('pm_interfaces')
        ->select('pm_interfaces.id AS pageId','pm_interface_topic.id AS grupId')
        ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
        ->where('pm_interfaces.path','adminTrialBalance')
        ->first();
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
        flex-direction: column;
        justify-content: center;
    }

    .stat-label {
        color: var(--text-muted);
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-main);
        line-height: 1.2;
    }

    .status-indicator {
        height: 100%;
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        color: white;
        display: flex;
        flex-direction: column;
        justify-content: center;
        box-shadow: var(--shadow-md);
    }
    .status-balanced {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }
    .status-unbalanced {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
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
    
    .group-header td {
        background-color: #fffbeb;
        color: #b45309;
        font-weight: 700;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .account-code {
        font-family: 'Courier New', Courier, monospace;
        font-weight: 600;
        color: var(--primary-color);
        background: rgba(79, 70, 229, 0.1);
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.9em;
    }

    .total-row td {
        background-color: #f8fafc;
        font-weight: 800;
        border-top: 2px solid #e5e7eb;
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
    
    /* Filter Section */
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
</style>

<div class="container-fluid py-4">

    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-end page-header">
        <div>
            <h1 class="page-title">Trial Balance</h1>
            <p class="page-subtitle m-0">Report Period: <span class="fw-bold text-dark">Jan 1, 2024 - Dec 31, 2024</span></p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-action" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fa fa-filter me-2 text-muted"></i> Filter
            </button>
            <button class="btn btn-action" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="fa fa-download me-2 text-muted"></i> Export
            </button>
            <button class="btn btn-action" onclick="window.print()">
                <i class="fa fa-print me-2 text-muted"></i> Print
            </button>
        </div>
    </div>

    {{-- Summary Cards & Status --}}
    <div class="row mb-4 g-4">
        {{-- Status Card --}}
        <div class="col-md-3">
            <div class="status-indicator status-balanced">
                <div class="d-flex align-items-center mb-2">
                    <i class="fa fa-check-circle fa-2x me-3"></i>
                    <h5 class="mb-0 fw-bold">Books Balanced</h5>
                </div>
                <small class="opacity-75">Total Debits match Total Credits.</small>
            </div>
        </div>

        {{-- Debit Summary --}}
        <div class="col-md-3">
            <div class="card card-modern stat-card">
                <span class="stat-label">Total Debits</span>
                <h3 class="stat-value">RS. 850,250.00</h3>
            </div>
        </div>

        {{-- Credit Summary --}}
        <div class="col-md-3">
            <div class="card card-modern stat-card">
                <span class="stat-label">Total Credits</span>
                <h3 class="stat-value">RS. 850,250.00</h3>
            </div>
        </div>

        {{-- Variance Summary --}}
        <div class="col-md-3">
            <div class="card card-modern stat-card" style="border-left: 4px solid var(--success-color);">
                <span class="stat-label">Variance</span>
                <h3 class="stat-value text-success">RS. 0.00</h3>
                <small class="text-muted fw-bold">Difference</small>
            </div>
        </div>
    </div>

    {{-- Main Table Section --}}
    <div class="row">
        <div class="col-12">
            <div class="card card-modern">
                {{-- Search Bar inside Table Container --}}
                <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                    <div class="input-group" style="max-width: 300px;">
                        <span class="input-group-text bg-light border-end-0 border-0"><i class="fa fa-search text-muted"></i></span>
                        <input type="text" class="form-control form-control-modern border-0 bg-light" placeholder="Search accounts...">
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="hideZeroAccounts">
                        <label class="form-check-label small text-muted fw-bold" for="hideZeroAccounts">Hide zero balance accounts</label>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th style="width: 15%;">Code</th>
                                <th style="width: 45%;">Account Name</th>
                                <th style="width: 15%; text-align: left;">Debit</th>
                                <th style="width: 15%; text-align: left;">Credit</th>
                                <th style="width: 10%; text-align: center;">View</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="group-header"><td colspan="5">ASSETS</td></tr>
                            <tr>
                                <td><span class="account-code">1010</span></td>
                                <td>Cash in Hand</td>
                                <td class="text-end fw-bold">25,000.00</td>
                                <td class="text-end text-muted">-</td>
                                <td class="text-center">
                                    <button class="btn-icon-modern mx-auto" onclick="openLedger('1010', 'Cash in Hand')"><i class="fa fa-eye small"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="account-code">1020</span></td>
                                <td>Bank - Checking</td>
                                <td class="text-end fw-bold">150,000.00</td>
                                <td class="text-end text-muted">-</td>
                                <td class="text-center">
                                    <button class="btn-icon-modern mx-auto" onclick="openLedger('1020', 'Bank - Checking')"><i class="fa fa-eye small"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="account-code">1200</span></td>
                                <td>Accounts Receivable</td>
                                <td class="text-end fw-bold">45,500.00</td>
                                <td class="text-end text-muted">-</td>
                                <td class="text-center">
                                    <button class="btn-icon-modern mx-auto" onclick="openLedger('1200', 'Accounts Receivable')"><i class="fa fa-eye small"></i></button>
                                </td>
                            </tr>

                            <tr class="group-header"><td colspan="5">LIABILITIES</td></tr>
                            <tr>
                                <td><span class="account-code">2010</span></td>
                                <td>Accounts Payable</td>
                                <td class="text-end text-muted">-</td>
                                <td class="text-end fw-bold">35,000.00</td>
                                <td class="text-center">
                                    <button class="btn-icon-modern mx-auto" onclick="openLedger('2010', 'Accounts Payable')"><i class="fa fa-eye small"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="account-code">2050</span></td>
                                <td>Sales Tax Payable</td>
                                <td class="text-end text-muted">-</td>
                                <td class="text-end fw-bold">5,250.00</td>
                                <td class="text-center">
                                    <button class="btn-icon-modern mx-auto" onclick="openLedger('2050', 'Sales Tax Payable')"><i class="fa fa-eye small"></i></button>
                                </td>
                            </tr>

                            <tr class="group-header"><td colspan="5">EQUITY</td></tr>
                            <tr>
                                <td><span class="account-code">3000</span></td>
                                <td>Owner's Capital</td>
                                <td class="text-end text-muted">-</td>
                                <td class="text-end fw-bold">500,000.00</td>
                                <td class="text-center">
                                    <button class="btn-icon-modern mx-auto" onclick="openLedger('3000', 'Owner\'s Capital')"><i class="fa fa-eye small"></i></button>
                                </td>
                            </tr>

                            <tr class="group-header"><td colspan="5">REVENUE</td></tr>
                            <tr>
                                <td><span class="account-code">4000</span></td>
                                <td>Sales Income</td>
                                <td class="text-end text-muted">-</td>
                                <td class="text-end fw-bold">310,000.00</td>
                                <td class="text-center">
                                    <button class="btn-icon-modern mx-auto" onclick="openLedger('4000', 'Sales Income')"><i class="fa fa-eye small"></i></button>
                                </td>
                            </tr>

                            <tr class="group-header"><td colspan="5">EXPENSES</td></tr>
                            <tr>
                                <td><span class="account-code">5010</span></td>
                                <td>Rent Expense</td>
                                <td class="text-end fw-bold">12,000.00</td>
                                <td class="text-end text-muted">-</td>
                                <td class="text-center">
                                    <button class="btn-icon-modern mx-auto" onclick="openLedger('5010', 'Rent Expense')"><i class="fa fa-eye small"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="account-code">5020</span></td>
                                <td>Salaries & Wages</td>
                                <td class="text-end fw-bold">617,750.00</td>
                                <td class="text-end text-muted">-</td>
                                <td class="text-center">
                                    <button class="btn-icon-modern mx-auto" onclick="openLedger('5020', 'Salaries & Wages')"><i class="fa fa-eye small"></i></button>
                                </td>
                            </tr>

                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="2" class="text-end">TOTAL BALANCE</td>
                                <td class="text-end text-success">RS. 850,250.00</td>
                                <td class="text-end text-success">RS. 850,250.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- MODAL: Filter --}}
<div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-modern">
            <div class="modal-header modal-header-modern">
                <h5 class="modal-title modal-title-modern">Filter Options</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label-modern">Start Date</label>
                            <input type="date" class="form-control form-control-modern" value="2024-01-01">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label-modern">End Date</label>
                            <input type="date" class="form-control form-control-modern" value="2024-12-31">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-modern">Account Types</label>
                        <select class="form-select form-control-modern" multiple>
                            <option selected>Assets</option>
                            <option selected>Liabilities</option>
                            <option selected>Equity</option>
                            <option selected>Revenue</option>
                            <option selected>Expenses</option>
                        </select>
                        <div class="form-text small">Hold Ctrl to select multiple</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary-modern rounded-pill px-4">Apply Filters</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: Export --}}
<div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content modal-content-modern text-center">
            <div class="modal-body p-4">
                <div class="mb-3">
                    <i class="fa fa-cloud-download-alt fa-3x text-primary opacity-50"></i>
                </div>
                <h5 class="fw-bold mb-3">Export Data</h5>
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-danger rounded-pill"><i class="fa fa-file-pdf me-2"></i> PDF Document</button>
                    <button class="btn btn-outline-success rounded-pill"><i class="fa fa-file-excel me-2"></i> Excel Spreadsheet</button>
                    <button class="btn btn-outline-secondary rounded-pill"><i class="fa fa-file-code me-2"></i> CSV Format</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: Ledger Drilldown --}}
<div class="modal fade" id="ledgerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content modal-content-modern">
            <div class="modal-header modal-header-modern">
                <div>
                    <h5 class="modal-title modal-title-modern">Account Ledger</h5>
                    <p class="mb-0 small text-white-50" id="ledgerModalSubtitle">Code: 1020 | Bank - Checking</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                
                {{-- Date Range Selection Section --}}
                <div class="p-4 bg-light border-bottom">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label-modern small">Start Date</label>
                            <input type="date" id="ledgerStartDate" class="form-control form-control-modern bg-white" value="{{ date('Y-m-01') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-modern small">End Date</label>
                            <input type="date" id="ledgerEndDate" class="form-control form-control-modern bg-white" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-primary-modern w-100" onclick="loadLedgerData()">
                                <i class="fa fa-sync-alt me-2"></i> Load Ledger
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Ledger Table Container (Initially Hidden) --}}
                <div id="ledgerTableContainer" style="display: none;">
                    <table class="table table-striped table-hover mb-0 small table-modern">
                        <thead class="bg-white sticky-top shadow-sm">
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Reference</th>
                                <th class="text-end">Debit</th>
                                <th class="text-end">Credit</th>
                                <th class="text-end">Running Bal.</th>
                            </tr>
                        </thead>
                        <tbody id="ledgerTableBody">
                            {{-- Content will be loaded here --}}
                        </tbody>
                    </table>
                </div>

                {{-- Loading State --}}
                <div id="ledgerLoading" class="text-center p-5" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted small">Fetching ledger data...</p>
                </div>

                {{-- Empty State / Prompt --}}
                <div id="ledgerPrompt" class="text-center p-5">
                    <i class="fa fa-calendar-alt fa-3x text-muted opacity-25 mb-3"></i>
                    <p class="text-muted">Select a date range and click <strong>Load Ledger</strong> to view transactions.</p>
                </div>

            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-sm btn-primary-modern rounded-pill px-4" id="btnFullReport" disabled>Full Ledger Report</button>
            </div>
        </div>
    </div>
</div>

@endsection


@section('footer')
<script>
    let ledgerModal;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Modal
        const modalEl = document.getElementById('ledgerModal');
        if (modalEl) {
            ledgerModal = new bootstrap.Modal(modalEl);
        }
    });

    function openLedger(code, name) {
        // 1. Set Title
        document.getElementById('ledgerModalSubtitle').innerText = `Code: ${code} | ${name}`;

        // 2. Reset State
        document.getElementById('ledgerPrompt').style.display = 'block';
        document.getElementById('ledgerTableContainer').style.display = 'none';
        document.getElementById('ledgerLoading').style.display = 'none';
        document.getElementById('btnFullReport').disabled = true;

        // 3. Show Modal
        if (ledgerModal) {
            ledgerModal.show();
        } else {
            // Fallback if DOMContentLoaded didn't catch it
            const modalEl = document.getElementById('ledgerModal');
            ledgerModal = new bootstrap.Modal(modalEl);
            ledgerModal.show();
        }
    }

    function loadLedgerData() {
        // 1. Show Loading, Hide Prompt & Table
        document.getElementById('ledgerPrompt').style.display = 'none';
        document.getElementById('ledgerTableContainer').style.display = 'none';
        document.getElementById('ledgerLoading').style.display = 'block';
        document.getElementById('btnFullReport').disabled = true;

        // 2. Simulate API Call / Data Fetching (Timeout for demo)
        setTimeout(() => {
            // Mock Data
            const startDate = document.getElementById('ledgerStartDate').value;
            const endDate = document.getElementById('ledgerEndDate').value;
            
            let html = `
                <tr>
                    <td>${startDate}</td>
                    <td>Opening Balance</td>
                    <td>-</td>
                    <td class="text-end">100,000.00</td>
                    <td class="text-end">-</td>
                    <td class="text-end fw-bold">100,000.00</td>
                </tr>
                <tr>
                    <td>${startDate}</td>
                    <td>Customer Payment - Inv #440</td>
                    <td>REC-001</td>
                    <td class="text-end">50,000.00</td>
                    <td class="text-end">-</td>
                    <td class="text-end fw-bold">150,000.00</td>
                </tr>
                <tr>
                    <td>${endDate}</td>
                    <td>Supplier Payment - Bill #992</td>
                    <td>PAY-005</td>
                    <td class="text-end">-</td>
                    <td class="text-end">20,000.00</td>
                    <td class="text-end fw-bold">130,000.00</td>
                </tr>
            `;

            // 3. Update DOM
            document.getElementById('ledgerTableBody').innerHTML = html;
            
            // 4. Show Table, Hide Loading
            document.getElementById('ledgerLoading').style.display = 'none';
            document.getElementById('ledgerTableContainer').style.display = 'block';
            document.getElementById('btnFullReport').disabled = false;

        }, 800); // 800ms delay to simulate loading
    }
</script>
@endsection