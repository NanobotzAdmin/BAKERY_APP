@php
    // Ideally, this logic belongs in the Controller, but kept here as per your snippet.
    $privilageId = \DB::table('pm_interfaces')
        ->select('pm_interfaces.id AS pageId','pm_interface_topic.id AS grupId')
        ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
        ->where('pm_interfaces.path','adminChartOfAccount')
        ->first();
@endphp

@extends('layout', ['pageId' => $privilageId->pageId ?? 0, 'grupId' => $privilageId->grupId ?? 0])

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<style>
    :root {
        --primary: #4f46e5;
        --primary-hover: #4338ca;
        --primary-light: #eef2ff;
        --success: #10b981;
        --success-light: #d1fae5;
        --danger: #ef4444;
        --danger-light: #fee2e2;
        --warning: #f59e0b;
        --warning-light: #fef3c7;
        --info: #3b82f6;
        --info-light: #dbeafe;
        --dark: #1f2937;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --gray-900: #111827;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        --radius: 12px;
        --radius-lg: 16px;
        --radius-xl: 20px;
    }

    * {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    body {
        background: var(--gray-50);
    }

    .coa-container {
        padding: 24px;
        max-width: 100%;
    }

    /* Header Section */
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: var(--radius-lg);
        padding: 32px;
        color: white;
        margin-bottom: 32px;
        box-shadow: var(--shadow-lg);
    }

    .page-header h1 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .page-header p {
        opacity: 0.9;
        font-size: 0.95rem;
        margin: 0;
    }

    /* Stats Cards */
    .stat-card {
        background: white;
        border-radius: var(--radius);
        padding: 24px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--gray-200);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--primary);
        transform: scaleY(0);
        transition: transform 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--primary);
    }

    .stat-card:hover::before {
        transform: scaleY(1);
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 16px;
        transition: transform 0.3s;
    }

    .stat-card:hover .stat-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--gray-900);
        margin: 8px 0 4px 0;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--gray-500);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-change {
        font-size: 0.75rem;
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .stat-change.positive { color: var(--success); }
    .stat-change.negative { color: var(--danger); }

    /* Filter Section */
    .filter-section {
        background: white;
        border-radius: var(--radius);
        padding: 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--gray-200);
        margin-bottom: 24px;
    }

    .search-input-wrapper {
        position: relative;
    }

    .search-input-wrapper i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-400);
    }

    .search-input-wrapper input {
        padding-left: 44px;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        transition: all 0.2s;
    }

    .search-input-wrapper input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    /* Table Section */
    .table-wrapper {
        background: white;
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--gray-200);
        overflow: hidden;
    }

    .table-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .table-header h3 {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0;
    }

    .modern-table {
        margin: 0;
        width: 100%;
    }

    .modern-table thead {
        background: var(--gray-50);
    }

    .modern-table thead th {
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--gray-600);
        padding: 16px 24px;
        border: none;
        border-bottom: 2px solid var(--gray-200);
    }

    .modern-table tbody tr {
        transition: all 0.2s;
        border-bottom: 1px solid var(--gray-100);
    }

    .modern-table tbody tr:hover {
        background: var(--gray-50);
    }

    .modern-table tbody td {
        padding: 16px 24px;
        vertical-align: middle;
        font-size: 0.875rem;
        color: var(--gray-700);
    }

    .parent-row {
        background: var(--gray-50) !important;
        font-weight: 600;
    }

    .parent-row:hover {
        background: var(--gray-100) !important;
    }

    .parent-row td {
        font-weight: 600;
        color: var(--gray-900);
    }

    .child-row td:first-child {
        padding-left: 48px;
        position: relative;
    }

    .child-row td:first-child::before {
        content: '└─';
        position: absolute;
        left: 24px;
        color: var(--gray-400);
    }

    .sub-child-row td:first-child {
        padding-left: 72px;
        position: relative;
    }

    .sub-child-row td:first-child::before {
        content: '└─';
        position: absolute;
        left: 48px;
        color: var(--gray-400);
    }

    /* Badges */
    .badge-modern {
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .badge-asset {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-liability {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-equity {
        background: #f3e8ff;
        color: #6b21a8;
    }

    .badge-income {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-expense {
        background: #fef3c7;
        color: #92400e;
    }

    /* Action Buttons */
    .action-group {
        display: flex;
        gap: 8px;
        align-items: center;
        justify-content: center;
    }

    .action-btn {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.875rem;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-view {
        background: var(--info-light);
        color: var(--info);
    }

    .btn-view:hover {
        background: var(--info);
        color: white;
    }

    .btn-edit {
        background: var(--primary-light);
        color: var(--primary);
    }

    .btn-edit:hover {
        background: var(--primary);
        color: white;
    }

    .btn-delete {
        background: var(--danger-light);
        color: var(--danger);
    }

    .btn-delete:hover {
        background: var(--danger);
        color: white;
    }

    .btn-add {
        background: var(--success-light);
        color: var(--success);
    }

    .btn-add:hover {
        background: var(--success);
        color: white;
    }

    /* Buttons */
    .btn-modern {
        border-radius: var(--radius);
        padding: 10px 20px;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-primary-modern {
        background: var(--primary);
        color: white;
    }

    .btn-primary-modern:hover {
        background: var(--primary-hover);
        color: white;
    }

    .btn-secondary-modern {
        background: white;
        color: var(--gray-700);
        border: 1px solid var(--gray-300);
    }

    .btn-secondary-modern:hover {
        background: var(--gray-50);
        border-color: var(--gray-400);
    }

    /* Modal Styles */
    .modal-content {
        border: none;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-xl);
    }

    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: var(--radius-lg) var(--radius-lg) 0 0;
        padding: 24px 32px;
        border: none;
    }

    .modal-header .modal-title {
        font-weight: 600;
        font-size: 1.25rem;
    }

    .modal-header .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.8;
    }

    .modal-header .btn-close:hover {
        opacity: 1;
    }

    .modal-body {
        padding: 32px;
    }

    .form-label {
        font-weight: 500;
        color: var(--gray-700);
        font-size: 0.875rem;
        margin-bottom: 8px;
    }

    .form-control, .form-select {
        border: 1px solid var(--gray-300);
        border-radius: var(--radius);
        padding: 10px 16px;
        transition: all 0.2s;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    .modal-footer {
        border-top: 1px solid var(--gray-200);
        padding: 20px 32px;
        background: var(--gray-50);
        border-radius: 0 0 var(--radius-lg) var(--radius-lg);
    }

    /* Detail View Styles */
    .detail-section {
        background: var(--gray-50);
        border-radius: var(--radius);
        padding: 20px;
        margin-bottom: 20px;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid var(--gray-200);
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 500;
        color: var(--gray-600);
        font-size: 0.875rem;
    }

    .detail-value {
        font-weight: 600;
        color: var(--gray-900);
        font-size: 0.875rem;
    }

    /* Expand/Collapse */
    .expand-icon {
        cursor: pointer;
        transition: transform 0.3s;
        display: inline-block;
    }

    .expand-icon.expanded {
        transform: rotate(90deg);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .coa-container {
            padding: 16px;
        }

        .page-header {
            padding: 24px;
        }

        .stat-card {
            margin-bottom: 16px;
        }
    }

    /* Loading Animation */
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .loading {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>

<div class="coa-container">
    
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1><i class="fa fa-chart-line me-2"></i>Chart of Accounts</h1>
                <p>Manage your financial structure and ledger accounts with ease</p>
            </div>
            <div>
                <button class="btn btn-light btn-modern me-2" onclick="exportAccounts()">
                    <i class="fa fa-download"></i> Export
                </button>
                <button class="btn btn-primary-modern btn-modern" data-toggle="modal" data-target="#newAccountModal">
                    <i class="fa fa-plus"></i> New Account
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-light-primary" style="background: #dbeafe; color: #1e40af;">
                    <i class="fa fa-briefcase"></i>
                </div>
                <div class="stat-label">Total Assets</div>
                <div class="stat-value">RS. 124,500.00</div>
                <div class="stat-change positive">
                    <i class="fa fa-arrow-up"></i> 12.5% from last month
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: #fee2e2; color: #991b1b;">
                    <i class="fa fa-credit-card"></i>
                </div>
                <div class="stat-label">Total Liabilities</div>
                <div class="stat-value">RS. 45,200.00</div>
                <div class="stat-change negative">
                    <i class="fa fa-arrow-down"></i> 3.2% from last month
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: #d1fae5; color: #065f46;">
                    <i class="fa fa-money"></i>
                </div>
                <div class="stat-label">Net Income</div>
                <div class="stat-value">RS. 89,300.00</div>
                <div class="stat-change positive">
                    <i class="fa fa-arrow-up"></i> 18.7% from last month
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: #fef3c7; color: #92400e;">
                    <i class="fa fa-folder-open"></i>
                </div>
                <div class="stat-label">Total Accounts</div>
                <div class="stat-value">142</div>
                <div class="stat-change positive">
                    <i class="fa fa-arrow-up"></i> 5 new this month
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="row align-items-end">
            <div class="col-md-5 mb-3 mb-md-0">
                <label class="form-label">Search Accounts</label>
                <div class="search-input-wrapper">
                    <i class="fa fa-search"></i>
                    <input type="text" class="form-control" id="searchInput" placeholder="Search by code, name, or type...">
                </div>
            </div>
            <div class="col-md-3 mb-3 mb-md-0">
                <label class="form-label">Account Type</label>
                <select class="form-select" id="typeFilter">
                    <option value="">All Types</option>
                    <option value="Asset">Assets</option>
                    <option value="Liability">Liabilities</option>
                    <option value="Equity">Equity</option>
                    <option value="Income">Income</option>
                    <option value="Expense">Expenses</option>
                </select>
            </div>
            <div class="col-md-2 mb-3 mb-md-0">
                <label class="form-label">Status</label>
                <select class="form-select" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-secondary-modern btn-modern w-100" onclick="resetFilters()">
                    <i class="fa fa-refresh"></i> Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="table-wrapper">
        <div class="table-header">
            <h3><i class="fa fa-list me-2"></i>Account List</h3>
            <span class="text-muted small">Showing <strong id="accountCount">142</strong> accounts</span>
        </div>
        <div class="table-responsive">
            <table class="table modern-table">
                <thead>
                    <tr>
                        <th style="width: 12%;">Code</th>
                        <th style="width: 35%;">Account Name</th>
                        <th style="width: 15%;">Type</th>
                        <th style="width: 15%;">Detail Type</th>
                        <th style="width: 13%; text-align: right;">Balance</th>
                        <th style="width: 10%; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody id="accountsTable">
                    <!-- Parent Row: Assets -->
                    <tr class="parent-row" data-id="1000">
                        <td><strong>1000</strong></td>
                        <td>
                            <span class="expand-icon" onclick="toggleRow(this)"><i class="fa fa-caret-right me-2"></i></span>
                            <strong>Assets</strong>
                        </td>
                        <td><span class="badge badge-modern badge-asset"><i class="fa fa-briefcase"></i> Asset</span></td>
                        <td>Heading</td>
                        <td class="text-end"><strong>RS. 124,500.00</strong></td>
                        <td class="text-center">
                            <div class="action-group">
                                <button class="action-btn btn-add" onclick="openAddModal('1000')" title="Add Sub-Account">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Child Row: Current Assets -->
                    <tr class="child-row parent-child" data-parent="1000" data-id="1100">
                        <td>1100</td>
                        <td>
                            <span class="expand-icon" onclick="toggleRow(this)"><i class="fa fa-caret-right me-2"></i></span>
                            Current Assets
                        </td>
                        <td><span class="badge badge-modern badge-asset"><i class="fa fa-briefcase"></i> Asset</span></td>
                        <td>Sub-Heading</td>
                        <td class="text-end">RS. 45,000.00</td>
                        <td class="text-center">
                            <div class="action-group">
                                <button class="action-btn btn-edit" onclick="openEditModal('1100')" title="Edit">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <button class="action-btn btn-view" onclick="openViewModal('1100')" title="View Details">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <button class="action-btn btn-add" onclick="openAddModal('1100')" title="Add Sub-Account">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Sub-Child Row: Cash in Hand -->
                    <tr class="sub-child-row" data-parent="1100" data-id="1101">
                        <td>1101</td>
                        <td>Cash in Hand</td>
                        <td><span class="badge badge-modern badge-asset"><i class="fa fa-briefcase"></i> Asset</span></td>
                        <td>Cash</td>
                        <td class="text-end">RS. 5,000.00</td>
                        <td class="text-center">
                            <div class="action-group">
                                <button class="action-btn btn-edit" onclick="openEditModal('1101')" title="Edit">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <button class="action-btn btn-view" onclick="openViewModal('1101')" title="View Details">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <button class="action-btn btn-delete" onclick="openDeleteModal('1101', 'Cash in Hand')" title="Delete">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Sub-Child Row: Petty Cash -->
                    <tr class="sub-child-row" data-parent="1100" data-id="1102">
                        <td>1102</td>
                        <td>Petty Cash</td>
                        <td><span class="badge badge-modern badge-asset"><i class="fa fa-briefcase"></i> Asset</span></td>
                        <td>Cash</td>
                        <td class="text-end">RS. 500.00</td>
                        <td class="text-center">
                            <div class="action-group">
                                <button class="action-btn btn-edit" onclick="openEditModal('1102')" title="Edit">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <button class="action-btn btn-view" onclick="openViewModal('1102')" title="View Details">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <button class="action-btn btn-delete" onclick="openDeleteModal('1102', 'Petty Cash')" title="Delete">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Parent Row: Liabilities -->
                    <tr class="parent-row" data-id="2000">
                        <td><strong>2000</strong></td>
                        <td>
                            <span class="expand-icon" onclick="toggleRow(this)"><i class="fa fa-caret-right me-2"></i></span>
                            <strong>Liabilities</strong>
                        </td>
                        <td><span class="badge badge-modern badge-liability"><i class="fa fa-credit-card"></i> Liability</span></td>
                        <td>Heading</td>
                        <td class="text-end"><strong>RS. 45,200.00</strong></td>
                        <td class="text-center">
                            <div class="action-group">
                                <button class="action-btn btn-add" onclick="openAddModal('2000')" title="Add Sub-Account">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Child Row: Accounts Payable -->
                    <tr class="child-row" data-parent="2000" data-id="2100">
                        <td>2100</td>
                        <td>Accounts Payable</td>
                        <td><span class="badge badge-modern badge-liability"><i class="fa fa-credit-card"></i> Liability</span></td>
                        <td>Creditors</td>
                        <td class="text-end">RS. 12,000.00</td>
                        <td class="text-center">
                            <div class="action-group">
                                <button class="action-btn btn-edit" onclick="openEditModal('2100')" title="Edit">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <button class="action-btn btn-view" onclick="openViewModal('2100')" title="View Details">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <button class="action-btn btn-delete" onclick="openDeleteModal('2100', 'Accounts Payable')" title="Delete">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add New Account Modal -->
<div class="modal fade" id="newAccountModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-plus-circle me-2"></i>Add New Account</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addAccountForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Parent Account <span class="text-danger">*</span></label>
                            <select class="form-select" id="parentAccount" required>
                                <option value="">Select Parent Account</option>
                                <option value="1000">1000 - Assets</option>
                                <option value="2000">2000 - Liabilities</option>
                                <option value="3000">3000 - Equity</option>
                                <option value="4000">4000 - Income</option>
                                <option value="5000">5000 - Expenses</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Account Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="accountCode" placeholder="e.g. 1105" required>
                            <small class="text-muted">Unique code for this account</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Account Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="accountName" placeholder="e.g. Bank of America" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Account Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="accountType" required>
                                <option value="">Select Type</option>
                                <option value="Asset">Asset</option>
                                <option value="Liability">Liability</option>
                                <option value="Equity">Equity</option>
                                <option value="Income">Income</option>
                                <option value="Expense">Expense</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Detail Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="detailType" required>
                                <option value="">Select Detail Type</option>
                                <option value="Cash">Cash</option>
                                <option value="Bank">Bank</option>
                                <option value="Accounts Receivable">Accounts Receivable</option>
                                <option value="Inventory">Inventory</option>
                                <option value="Fixed Asset">Fixed Asset</option>
                                <option value="Accounts Payable">Accounts Payable</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Opening Balance</label>
                            <div class="input-group">
                                <span class="input-group-text">RS.</span>
                                <input type="number" class="form-control" id="openingBalance" placeholder="0.00" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="accountStatus">
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="accountDescription" rows="3" placeholder="Enter account description..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary-modern btn-modern" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary-modern btn-modern" onclick="saveAccount()">
                    <i class="fa fa-save"></i> Save Account
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Account Modal -->
<div class="modal fade" id="editAccountModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-edit me-2"></i>Edit Account</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editAccountForm">
                    <input type="hidden" id="editAccountId">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Parent Account</label>
                            <select class="form-select" id="editParentAccount">
                                <option value="">Select Parent Account</option>
                                <option value="1000">1000 - Assets</option>
                                <option value="2000">2000 - Liabilities</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Account Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editAccountCode" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Account Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editAccountName" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Account Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="editAccountType" required>
                                <option value="Asset">Asset</option>
                                <option value="Liability">Liability</option>
                                <option value="Equity">Equity</option>
                                <option value="Income">Income</option>
                                <option value="Expense">Expense</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Detail Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="editDetailType" required>
                                <option value="Cash">Cash</option>
                                <option value="Bank">Bank</option>
                                <option value="Accounts Receivable">Accounts Receivable</option>
                                <option value="Inventory">Inventory</option>
                                <option value="Fixed Asset">Fixed Asset</option>
                                <option value="Accounts Payable">Accounts Payable</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Current Balance</label>
                            <div class="input-group">
                                <span class="input-group-text">RS.</span>
                                <input type="text" class="form-control" id="editCurrentBalance" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="editAccountStatus">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="editAccountDescription" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary-modern btn-modern" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary-modern btn-modern" onclick="updateAccount()">
                    <i class="fa fa-save"></i> Update Account
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Account Details Modal -->
<div class="modal fade" id="viewAccountModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-eye me-2"></i>Account Details</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="detail-section">
                    <div class="detail-row">
                        <span class="detail-label"><i class="fa fa-hashtag me-2"></i>Account Code</span>
                        <span class="detail-value" id="viewCode">1101</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="fa fa-tag me-2"></i>Account Name</span>
                        <span class="detail-value" id="viewName">Cash in Hand</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="fa fa-folder me-2"></i>Parent Account</span>
                        <span class="detail-value" id="viewParent">1100 - Current Assets</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="fa fa-briefcase me-2"></i>Account Type</span>
                        <span class="detail-value" id="viewType"><span class="badge badge-modern badge-asset">Asset</span></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="fa fa-list me-2"></i>Detail Type</span>
                        <span class="detail-value" id="viewDetailType">Cash</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="fa fa-dollar-sign me-2"></i>Current Balance</span>
                        <span class="detail-value" id="viewBalance" style="color: var(--success); font-size: 1.1rem;">RS. 5,000.00</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="fa fa-info-circle me-2"></i>Status</span>
                        <span class="detail-value" id="viewStatus"><span class="badge badge-modern" style="background: var(--success-light); color: var(--success);">Active</span></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="fa fa-calendar me-2"></i>Created Date</span>
                        <span class="detail-value" id="viewCreated">January 15, 2024</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="fa fa-file-text me-2"></i>Description</span>
                        <span class="detail-value" id="viewDescription">Main cash account for daily operations</span>
                    </div>
                </div>
                <div class="mt-4">
                    <h6 class="mb-3"><i class="fa fa-history me-2"></i>Recent Transactions</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>2024-01-20</td>
                                    <td>Cash Sale</td>
                                    <td>RS. 500.00</td>
                                    <td>-</td>
                                    <td>RS. 5,000.00</td>
                                </tr>
                                <tr>
                                    <td>2024-01-18</td>
                                    <td>Cash Withdrawal</td>
                                    <td>-</td>
                                    <td>RS. 200.00</td>
                                    <td>RS. 4,500.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary-modern btn-modern" data-dismiss="modal">
                    <i class="fa fa-times"></i> Close
                </button>
                <button type="button" class="btn btn-primary-modern btn-modern" onclick="editFromView()">
                    <i class="fa fa-edit"></i> Edit Account
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                <h5 class="modal-title"><i class="fa fa-exclamation-triangle me-2"></i>Delete Account</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-4">
                    <i class="fa fa-trash fa-4x text-danger mb-3"></i>
                    <h5>Are you sure?</h5>
                    <p class="text-muted">You are about to delete the account:</p>
                    <h6 class="text-dark" id="deleteAccountName">Cash in Hand</h6>
                    <p class="text-danger small mt-3"><strong>Warning:</strong> This action cannot be undone. All associated transactions will be affected.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary-modern btn-modern" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-modern" style="background: var(--danger); color: white;" onclick="confirmDelete()">
                    <i class="fa fa-trash"></i> Delete Account
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('footer')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Toggle row expand/collapse
    function toggleRow(element) {
        const row = element.closest('tr');
        const rowId = row.getAttribute('data-id');
        const icon = element.querySelector('i');
        
        if (icon.classList.contains('fa-caret-right')) {
            icon.classList.remove('fa-caret-right');
            icon.classList.add('fa-caret-down');
            element.classList.add('expanded');
            // Show child rows
            document.querySelectorAll(`tr[data-parent="${rowId}"]`).forEach(child => {
                child.style.display = '';
            });
        } else {
            icon.classList.remove('fa-caret-down');
            icon.classList.add('fa-caret-right');
            element.classList.remove('expanded');
            // Hide child rows recursively
            hideChildren(rowId);
        }
    }

    function hideChildren(parentId) {
        document.querySelectorAll(`tr[data-parent="${parentId}"]`).forEach(child => {
            child.style.display = 'none';
            const childId = child.getAttribute('data-id');
            if (childId) {
                hideChildren(childId);
                // Reset icon
                const icon = child.querySelector('.expand-icon i');
                if (icon) {
                    icon.classList.remove('fa-caret-down');
                    icon.classList.add('fa-caret-right');
                }
            }
        });
    }

    // Initially hide all child rows
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.child-row, .sub-child-row').forEach(row => {
            row.style.display = 'none';
        });
    });

    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#accountsTable tr');
        let visibleCount = 0;

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('accountCount').textContent = visibleCount;
    });

    // Filter functionality
    document.getElementById('typeFilter').addEventListener('change', applyFilters);
    document.getElementById('statusFilter').addEventListener('change', applyFilters);

    function applyFilters() {
        const typeFilter = document.getElementById('typeFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#accountsTable tr');
        let visibleCount = 0;

        rows.forEach(row => {
            const type = row.querySelector('.badge')?.textContent.trim() || '';
            const text = row.textContent.toLowerCase();
            
            let show = true;
            
            if (typeFilter && !type.toLowerCase().includes(typeFilter.toLowerCase())) {
                show = false;
            }
            
            if (searchTerm && !text.includes(searchTerm)) {
                show = false;
            }

            if (show) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('accountCount').textContent = visibleCount;
    }

    function resetFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('typeFilter').value = '';
        document.getElementById('statusFilter').value = '';
        applyFilters();
    }

    // Modal Functions
    function openAddModal(parentId = '') {
        if (parentId) {
            document.getElementById('parentAccount').value = parentId;
        }
        $('#newAccountModal').modal('show');
    }

    function openEditModal(accountId) {
        // In real app, fetch account data from server
        document.getElementById('editAccountId').value = accountId;
        document.getElementById('editAccountCode').value = accountId;
        document.getElementById('editAccountName').value = 'Cash in Hand';
        document.getElementById('editAccountType').value = 'Asset';
        document.getElementById('editDetailType').value = 'Cash';
        document.getElementById('editCurrentBalance').value = 'RS. 5,000.00';
        document.getElementById('editAccountStatus').value = 'active';
        document.getElementById('editAccountDescription').value = 'Main cash account for daily operations';
        $('#editAccountModal').modal('show');
    }

    function openViewModal(accountId) {
        // In real app, fetch account data from server
        document.getElementById('viewCode').textContent = accountId;
        document.getElementById('viewName').textContent = 'Cash in Hand';
        document.getElementById('viewParent').textContent = '1100 - Current Assets';
        document.getElementById('viewType').innerHTML = '<span class="badge badge-modern badge-asset"><i class="fa fa-briefcase"></i> Asset</span>';
        document.getElementById('viewDetailType').textContent = 'Cash';
        document.getElementById('viewBalance').textContent = 'RS. 5,000.00';
        document.getElementById('viewStatus').innerHTML = '<span class="badge badge-modern" style="background: var(--success-light); color: var(--success);">Active</span>';
        document.getElementById('viewCreated').textContent = 'January 15, 2024';
        document.getElementById('viewDescription').textContent = 'Main cash account for daily operations';
        $('#viewAccountModal').modal('show');
    }

    function openDeleteModal(accountId, accountName) {
        document.getElementById('deleteAccountName').textContent = accountName;
        document.getElementById('deleteAccountModal').setAttribute('data-account-id', accountId);
        $('#deleteAccountModal').modal('show');
    }

    function editFromView() {
        $('#viewAccountModal').modal('hide');
        setTimeout(() => {
            const accountId = document.getElementById('viewCode').textContent;
            openEditModal(accountId);
        }, 300);
    }

    // Save/Update/Delete Functions
    function saveAccount() {
        const form = document.getElementById('addAccountForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        Swal.fire({
            title: 'Success!',
            text: 'Account has been created successfully.',
            icon: 'success',
            confirmButtonColor: '#4f46e5',
            confirmButtonText: 'OK',
            timer: 2000
        }).then(() => {
            $('#newAccountModal').modal('hide');
            form.reset();
        });
    }

    function updateAccount() {
        const form = document.getElementById('editAccountForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        Swal.fire({
            title: 'Success!',
            text: 'Account has been updated successfully.',
            icon: 'success',
            confirmButtonColor: '#4f46e5',
            confirmButtonText: 'OK',
            timer: 2000
        }).then(() => {
            $('#editAccountModal').modal('hide');
        });
    }

    function confirmDelete() {
        const accountId = document.getElementById('deleteAccountModal').getAttribute('data-account-id');
        
        Swal.fire({
            title: 'Deleted!',
            text: 'Account has been deleted successfully.',
            icon: 'success',
            confirmButtonColor: '#4f46e5',
            confirmButtonText: 'OK',
            timer: 2000
        }).then(() => {
            $('#deleteAccountModal').modal('hide');
        });
    }

    function exportAccounts() {
        Swal.fire({
            title: 'Exporting...',
            text: 'Preparing your export file...',
            icon: 'info',
            timer: 2000,
            showConfirmButton: false
        });
    }
</script>
@endsection
