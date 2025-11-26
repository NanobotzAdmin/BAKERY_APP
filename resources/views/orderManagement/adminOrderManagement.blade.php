@php
    $orderPageMeta = isset($privilageId) ? $privilageId : (object) ['pageId' => 'demo', 'grupId' => 'demo'];
    
    // --- RICH DUMMY DATA ---
    $orders = [
        [
            'id' => 'ORD-1001', 'type' => 'manual', 'customer' => 'John Doe', 'contact' => '077-1234567',
            'date' => '2023-11-25 10:00', 'status' => 'pending', 'branch' => 'N/A',
            // Chocolate Cake: Has 'Eggs' (Insufficient) & Butter (Low) -> Will show Reorder Buttons
            'items' => [['name' => 'Chocolate Cake (1kg)', 'qty' => 1, 'note' => 'Happy Birthday', 'productId' => 'PROD-001']]
        ],
        [
            'id' => 'BR-550', 'type' => 'branch', 'customer' => 'Colombo Main', 'contact' => 'Manager: Sunil',
            'date' => '2023-11-26 06:00', 'status' => 'baking', 'branch' => 'Colombo Main',
            // Fish Buns: Low Stock -> Will show Reorder Button
            'items' => [
                ['name' => 'Fish Buns', 'qty' => 50, 'note' => '', 'productId' => 'PROD-003'], 
                ['name' => 'Chicken Rolls', 'qty' => 30, 'note' => 'Spicy', 'productId' => 'PROD-004']
            ]
        ],
        [
            'id' => 'AUTO-99', 'type' => 'auto', 'customer' => 'System Generated', 'contact' => 'Daily Cycle',
            'date' => '2023-11-26 05:00', 'status' => 'ready', 'branch' => 'Main Kitchen',
            'items' => [['name' => 'Sandwich Bread', 'qty' => 200, 'note' => 'Standard', 'productId' => 'PROD-006'], ['name' => 'Kade Paan', 'qty' => 150, 'note' => '', 'productId' => 'PROD-007']]
        ],
        [
            'id' => 'ORD-1045', 'type' => 'manual', 'customer' => 'Sarah Perera', 'contact' => '071-9988776',
            'date' => '2023-12-01 15:00', 'status' => 'pending', 'branch' => 'N/A',
            'items' => [['name' => 'Wedding Structure', 'qty' => 1, 'note' => 'Gold/White Theme', 'productId' => 'PROD-008'], ['name' => 'Love Cake', 'qty' => 100, 'note' => 'Wrapped', 'productId' => 'PROD-009']]
        ],
         [
            'id' => 'BR-555', 'type' => 'branch', 'customer' => 'Negombo Outlet', 'contact' => 'Manager: Amal',
            'date' => '2023-11-28 07:00', 'status' => 'baking', 'branch' => 'Negombo',
            'items' => [['name' => 'Sponge Cake', 'qty' => 10, 'note' => '', 'productId' => 'PROD-011'], ['name' => 'Muffins', 'qty' => 50, 'note' => 'Choco Chip', 'productId' => 'PROD-012']]
        ]
    ];
@endphp

@extends('layout', ['pageId' => $orderPageMeta->pageId, 'grupId' => $orderPageMeta->grupId])

@section('content')

    {{-- LIBRARIES --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <style>
        /* --- TABLE STYLES --- */
        .table-hover tbody tr:hover { background-color: #faf6ec; transition: background-color 0.2s; }
        
        /* Status Badges */
        .badge-status { padding: 5px 10px; border-radius: 4px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
        .status-pending { background-color: #ffeeba; color: #856404; }
        .status-baking { background-color: #cce5ff; color: #004085; }
        .status-ready { background-color: #d4edda; color: #155724; }
        .status-delivered { background-color: #d6d8d9; color: #1b1e21; }

        /* Order Type Badges */
        .type-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; padding: 3px 8px; border-radius: 3px; font-weight: 700; border: 1px solid transparent; }
        .type-manual { background: #f4fdf6; border-color: #28a745; color: #28a745; }
        .type-branch { background: #f0f7fd; border-color: #007bff; color: #007bff; }
        .type-auto { background: #f9f5ff; border-color: #6f42c1; color: #6f42c1; }

        /* --- ALERT ROW STYLES --- */
        tr.row-stock-critical { background-color: #fff5f5 !important; }
        tr.row-stock-critical td { border-color: #f5c6cb; }
        
        tr.row-stock-warning { background-color: #fffbf2 !important; }
        tr.row-stock-warning td { border-color: #ffeeba; }

        .stock-alert-icon { font-size: 16px; margin-right: 5px; vertical-align: middle; }
        .text-critical { color: #dc3545; }
        .text-warning { color: #ffca2c; }

        /* --- MODAL STYLES --- */
        .modal-header-custom { border-bottom: none; padding-bottom: 0; }
        .modal-order-id { font-size: 1.5rem; font-weight: 800; color: #333; }
        .modal-section-title { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; color: #888; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 15px; margin-top: 15px; font-weight: 600; }
        .info-box { background: #f9f9f9; padding: 15px; border-radius: 8px; border: 1px solid #eee; }
        
        .select2-container { z-index: 99999; }
        .select2-dropdown { z-index: 10001; }

        /* --- MODERN STOCK MODAL STYLES --- */
        .stock-card {
            border: 1px solid #eee; border-radius: 8px; padding: 15px; margin-bottom: 10px;
            background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.02); transition: transform 0.2s;
        }
        .stock-card:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .stock-info-row { display: flex; justify-content: space-between; margin-bottom: 8px; align-items: center; }
        .stock-name { font-weight: 700; font-size: 1rem; color: #333; }
        .progress-custom { height: 8px; border-radius: 4px; background-color: #e9ecef; overflow: hidden; margin-top: 5px; }
        .progress-bar-custom { height: 100%; border-radius: 4px; transition: width 0.6s ease; }
        .stock-meta { font-size: 0.85rem; color: #666; margin-top: 8px; display: flex; justify-content: space-between; }
        .stock-badge { padding: 4px 10px; border-radius: 12px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-sufficient { background: #d4edda; color: #155724; }
        .badge-low { background: #fff3cd; color: #856404; }
        .badge-insufficient { background: #f8d7da; color: #721c24; }
        
        /* Reorder Button Style */
        .btn-reorder-group { border-top: 1px solid #f0f0f0; margin-top: 10px; padding-top: 10px; text-align: right; }
    </style>

    {{-- Page Header --}}
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-8">
            <h2><b>Order Management</b></h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admindashboard">Home</a></li>
                <li class="breadcrumb-item active"><strong>All Orders</strong></li>
            </ol>
        </div>
        <div class="col-lg-4 text-right" style="padding-top: 20px;">
            <button type="button" class="btn btn-primary btn-lg shadow-sm" data-toggle="modal" data-target="#createOrderModal">
                <i class="fa fa-plus-circle"></i> New Manual Order
            </button>
        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">

        {{-- Filter Section --}}
        <div class="ibox mb-3">
            <div class="ibox-content" style="padding: 15px 20px;">
                <form method="GET" action="">
                    <div class="row">
                        <div class="col-md-2">
                            <label class="font-normal"><strong>From Date</strong></label>
                            <input type="date" class="form-control" name="start_date">
                        </div>
                        <div class="col-md-2">
                            <label class="font-normal"><strong>To Date</strong></label>
                            <input type="date" class="form-control" name="end_date">
                        </div>
                        <div class="col-md-2">
                            <label class="font-normal"><strong>Order Type</strong></label>
                            <select class="form-control" name="type">
                                <option value="">All Types</option>
                                <option value="manual">Manual</option>
                                <option value="branch">Branch</option>
                                <option value="auto">Auto</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="font-normal"><strong>Status</strong></label>
                            <select class="form-control" name="status">
                                <option value="">All Statuses</option>
                                <option value="pending">Pending</option>
                                <option value="baking">Baking</option>
                                <option value="ready">Ready</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-success btn-block"><i class="fa fa-filter"></i> Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Main Unified Table --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Ref ID</th>
                                        <th class="text-center">Type</th>
                                        <th>Customer / Branch Info</th>
                                        <th>Order Items Summary</th>
                                        <th>Required Date</th>
                                        <th>Status</th>
                                        <th class="text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                    <tr>
                                        <td><strong>#{{ $order['id'] }}</strong></td>
                                        <td class="text-center">
                                            <span class="type-label type-{{ $order['type'] }}">{{ ucfirst($order['type']) }}</span>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: #333;">{{ $order['customer'] }}</div>
                                            <small class="text-muted">{{ $order['contact'] }}</small>
                                        </td>
                                        <td>
                                            {{ $order['items'][0]['name'] }}
                                            @if(count($order['items']) > 1)
                                                <small class="text-muted"> (+{{ count($order['items']) - 1 }} more)</small>
                                            @endif
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($order['date'])->format('Y-m-d') }}
                                            <br><small class="text-navy">{{ \Carbon\Carbon::parse($order['date'])->format('h:i A') }}</small>
                                        </td>
                                        <td><span class="badge-status status-{{ $order['status'] }}">{{ ucfirst($order['status']) }}</span></td>
                                        <td class="text-right">
                                            <button class="btn btn-white btn-sm btn-edit"
                                                data-toggle="modal" 
                                                data-target="#editOrderModal"
                                                data-id="{{ $order['id'] }}"
                                                data-customer="{{ $order['customer'] }}"
                                                data-contact="{{ $order['contact'] }}"
                                                data-date="{{ $order['date'] }}"
                                                data-status="{{ $order['status'] }}"
                                                data-type="{{ $order['type'] }}"
                                                data-items="{{ json_encode($order['items']) }}">
                                                <i class="fa fa-pencil"></i> View / Edit
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-sm-12 text-right">
                                <div class="btn-group">
                                    <button class="btn btn-white"><i class="fa fa-chevron-left"></i></button>
                                    <button class="btn btn-white active">1</button>
                                    <button class="btn btn-white">2</button>
                                    <button class="btn btn-white"><i class="fa fa-chevron-right"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================= --}}
    {{--  MODAL 1: CREATE NEW ORDER                        --}}
    {{-- ================================================= --}}
    <div class="modal fade" id="createOrderModal" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Customer Order</h4>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                </div>
                <form action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>
                                        Customer Name 
                                        <button type="button" class="btn btn-xs btn-primary ml-2 rounded-circle" data-toggle="modal" data-target="#createCustomerModal" title="Add New Customer">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </label>
                                    <select class="form-control select2 select2-customer" name="customer_id" style="width: 100%;" required>
                                        <option></option> 
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input type="text" class="form-control" id="autoCustomerPhone" placeholder="Auto-filled" readonly tabindex="-1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Pickup Date & Time</label>
                                    <input type="datetime-local" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Advance Payment (Rs)</label>
                                    <input type="number" class="form-control" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <hr>
                                <h5>Order Items</h5>
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Product</th>
                                            <th width="20%">Qty</th>
                                            <th width="10%"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><select class="form-control"><option>Butter Cake (1kg)</option></select></td>
                                            <td><input type="number" class="form-control" value="1"></td>
                                            <td><button type="button" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-info btn-xs btn-outline"><i class="fa fa-plus"></i> Add Line</button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ================================================= --}}
    {{--  MODAL 2: CREATE NEW CUSTOMER (NESTED)            --}}
    {{-- ================================================= --}}
    <div class="modal fade" id="createCustomerModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Add New Customer</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" id="newCustName" class="form-control" placeholder="Ex: Kamal Perera">
                    </div>
                    <div class="form-group">
                        <label>Mobile Number</label>
                        <input type="text" id="newCustPhone" class="form-control" placeholder="07X-XXXXXXX">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary btn-sm" id="btnSaveCustomer">Save & Select</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================= --}}
    {{--  MODAL 3: VIEW / EDIT ORDER                       --}}
    {{-- ================================================= --}}
    <div class="modal fade" id="editOrderModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-header-custom">
                    <div class="row w-100">
                        <div class="col-8">
                            <span class="text-muted small">ORDER REFERENCE</span>
                            <div class="modal-order-id" id="modalOrderId">#ORD-0000</div>
                        </div>
                        <div class="col-4 text-right">
                             <span class="badge-status status-pending" id="modalStatusBadge">PENDING</span>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" style="position: absolute; right: 15px; top: 15px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form action="" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-7">
                                <div class="info-box h-100">
                                    <div class="modal-section-title mt-0">Customer Details</div>
                                    <div class="form-group row mb-2">
                                        <label class="col-sm-4 col-form-label text-muted">Name/Branch</label>
                                        <div class="col-sm-8"><input type="text" class="form-control font-weight-bold" id="modalCustomer" value=""></div>
                                    </div>
                                    <div class="form-group row mb-0">
                                        <label class="col-sm-4 col-form-label text-muted">Contact</label>
                                        <div class="col-sm-8"><input type="text" class="form-control" id="modalContact" value=""></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="info-box h-100">
                                    <div class="modal-section-title mt-0">Scheduling</div>
                                    <div class="form-group mb-2">
                                        <label class="text-muted small">Required Date</label>
                                        <input type="datetime-local" class="form-control" id="modalDate">
                                    </div>
                                    <div class="form-group mb-0">
                                        <label class="text-muted small">Update Status</label>
                                        <select class="form-control" name="status" id="modalStatusSelect">
                                            <option value="pending">Pending</option>
                                            <option value="baking">Baking</option>
                                            <option value="ready">Ready for Pickup</option>
                                            <option value="delivered">Delivered</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="modal-section-title">Order Items</div>
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="40%">Product Name</th>
                                            <th width="15%" class="text-center">Qty</th>
                                            <th width="30%">Notes</th>
                                            <th width="15%" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="modalItemsBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary"><i class="fa fa-save"></i> Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================= --}}
    {{--  MODAL 4: INGREDIENT STOCK (MODERN CARD DESIGN)   --}}
    {{-- ================================================= --}}
    <div class="modal fade" id="ingredientStockModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header bg-white border-0 pb-0">
                    <h5 class="modal-title font-weight-bold">
                        Stock Availability
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body pt-2">
                    <div class="mb-3 pb-2 border-bottom">
                        <span class="text-muted small text-uppercase">Product Analysis for</span><br>
                        <h4 class="text-info m-0" id="stockModalProductName">Product Name</h4>
                    </div>
                    
                    {{-- Container for JS generated cards --}}
                    <div id="ingredientStockContainer" style="background: #f8f9fa; padding: 10px; border-radius: 8px;">
                        {{-- Cards injected here via JS --}}
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Close Window</button>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function(){

        // --- 1. DUMMY INGREDIENTS DATABASE ---
        var dummyIngredients = {
            'PROD-001': [ // Chocolate Cake
                { name: 'Flour', required: 500, available: 1500, unit: 'g', status: 'sufficient' },
                { name: 'Sugar', required: 300, available: 800, unit: 'g', status: 'sufficient' },
                { name: 'Butter', required: 200, available: 150, unit: 'g', status: 'low' }, 
                { name: 'Eggs', required: 4, available: 2, unit: 'pcs', status: 'insufficient' } 
            ],
            'PROD-003': [ // Fish Buns
                { name: 'Flour', required: 2000, available: 1500, unit: 'g', status: 'insufficient' },
                { name: 'Fish Filling', required: 1000, available: 800, unit: 'g', status: 'low' }
            ],
            'PROD-004': [ // Chicken Rolls
                { name: 'Chicken', required: 500, available: 2000, unit: 'g', status: 'sufficient' }
            ],
            'PROD-006': [ // Bread
                { name: 'Flour', required: 5000, available: 2000, unit: 'g', status: 'insufficient' }
            ],
            // Fallback
            'default': [
                { name: 'General Flour', required: 500, available: 2000, unit: 'g', status: 'sufficient' },
                { name: 'Sugar', required: 200, available: 1000, unit: 'g', status: 'sufficient' }
            ]
        };

        // --- 2. LOGIC: COLOR MAIN TABLE ROWS BASED ON STOCK ---
        function checkAllOrdersForStockIssues() {
            $('.btn-edit').each(function() {
                var $btn = $(this);
                var items = $btn.data('items'); 
                var $row = $btn.closest('tr');
                var $refIdCell = $row.find('td:first'); 
                var worstStatus = 'sufficient'; 

                if(Array.isArray(items)) {
                    items.forEach(function(item) {
                        var prodId = item.productId || 'default';
                        var ingredients = dummyIngredients[prodId] || dummyIngredients['default'];
                        ingredients.forEach(function(ing) {
                            if(ing.status === 'insufficient') worstStatus = 'insufficient';
                            else if(ing.status === 'low' && worstStatus !== 'insufficient') worstStatus = 'low';
                        });
                    });
                }

                if(worstStatus === 'insufficient') {
                    $row.addClass('row-stock-critical');
                    $refIdCell.prepend('<i class="fa fa-times-circle stock-alert-icon text-critical" title="Insufficient Stock"></i> ');
                } else if (worstStatus === 'low') {
                    $row.addClass('row-stock-warning');
                    $refIdCell.prepend('<i class="fa fa-exclamation-triangle stock-alert-icon text-warning" title="Low Stock"></i> ');
                }
            });
        }

        checkAllOrdersForStockIssues();

        // --- 3. CUSTOMER SELECT2 ---
        var dummyCustomers = [
            { id: '101', text: 'John Doe', phone: '077-1234567' },
            { id: '102', text: 'Sarah Perera', phone: '071-9988776' },
            { id: '103', text: 'Colombo Main Branch', phone: '011-2233445' }
        ];

        function initializeSelect2() {
            var $customerSelect = $('.select2-customer');
            $customerSelect.empty().append('<option></option>');
            $.each(dummyCustomers, function(index, item) {
                var newOption = new Option(item.text, item.id, false, false);
                $(newOption).attr('data-phone', item.phone);
                $customerSelect.append(newOption);
            });
            $customerSelect.select2({ dropdownParent: $('#createOrderModal'), placeholder: "Select a Customer", allowClear: true, width: '100%' });
        }
        initializeSelect2();

        $(document).on('select2:select', '.select2-customer', function (e) {
            var phone = $(this).find(':selected').data('phone');
            $('#autoCustomerPhone').val(phone || ''); 
        });

        $('#btnSaveCustomer').click(function() {
            var name = $('#newCustName').val();
            var phone = $('#newCustPhone').val();
            if(name === '') { alert('Enter name'); return; }
            var newOption = new Option(name, 'NEW-'+Math.random(), true, true);
            $(newOption).attr('data-phone', phone);
            $('.select2-customer').append(newOption).trigger('change');
            $('#autoCustomerPhone').val(phone);
            $('#createCustomerModal').modal('hide');
        });

        // --- 4. VIEW STOCK (MODERN CARDS + REORDER BUTTON) ---
        $(document).on('click', '.btn-view-stock', function() {
            var productName = $(this).data('product-name');
            var productId = $(this).data('product-id');
            $('#stockModalProductName').text(productName);
            
            var ingredients = dummyIngredients[productId] || dummyIngredients['default'];
            var contentHtml = '';
            
            $.each(ingredients, function(index, ing) {
                var percentage = Math.min(100, Math.round((ing.available / ing.required) * 100));
                var colorClass = 'bg-success', badgeClass = 'badge-sufficient', statusLabel = 'Sufficient';

                if(ing.status === 'low') { colorClass = 'bg-warning'; badgeClass = 'badge-low'; statusLabel = 'Low Stock'; }
                else if (ing.status === 'insufficient') { colorClass = 'bg-danger'; badgeClass = 'badge-insufficient'; statusLabel = 'Insufficient'; }

                contentHtml += '<div class="stock-card">';
                contentHtml += '<div class="stock-info-row"><span class="stock-name">' + ing.name + '</span><span class="stock-badge ' + badgeClass + '">' + statusLabel + '</span></div>';
                contentHtml += '<div class="progress-custom"><div class="progress-bar-custom ' + colorClass + '" style="width: ' + percentage + '%"></div></div>';
                contentHtml += '<div class="stock-meta"><span>Required: <strong>' + ing.required + ' ' + ing.unit + '</strong></span><span>Available: <strong class="' + (ing.status !== 'sufficient' ? 'text-danger' : 'text-success') + '">' + ing.available + ' ' + ing.unit + '</strong></span></div>';
                
                // --- NEW REORDER BUTTON LOGIC ---
                if(ing.status === 'low' || ing.status === 'insufficient') {
                    contentHtml += '<div class="btn-reorder-group">';
                    contentHtml += '<button class="btn btn-sm btn-outline-danger btn-reorder" data-ingredient="'+ing.name+'">';
                    contentHtml += '<i class="fa fa-cart-plus"></i> Reorder Stock';
                    contentHtml += '</button>';
                    contentHtml += '</div>';
                }
                
                contentHtml += '</div>';
            });
            $('#ingredientStockContainer').html(contentHtml);
            $('#ingredientStockModal').modal('show');
        });
        
        // Handle Reorder Button Click
        $(document).on('click', '.btn-reorder', function() {
            var ingredientName = $(this).data('ingredient');
            alert('Reorder request initiated for: ' + ingredientName);
            // Here you would typically trigger an AJAX call to your backend
        });

        // --- 5. POPULATE EDIT MODAL (WITH COLORED ROWS) ---
        $('.btn-edit').click(function(){
            var id = $(this).data('id');
            var customer = $(this).data('customer');
            var contact = $(this).data('contact');
            var date = $(this).data('date');
            var status = $(this).data('status');
            var items = $(this).data('items'); 

            $('#modalOrderId').text('#' + id);
            $('#modalCustomer').val(customer);
            $('#modalContact').val(contact);
            $('#modalDate').val(date.replace(' ', 'T'));
            $('#modalStatusSelect').val(status);
            
            $('#modalStatusBadge').text(status.toUpperCase())
                .removeClass('status-pending status-baking status-ready status-delivered')
                .addClass('status-' + status);

            var itemsHtml = '';
            if(Array.isArray(items)) {
                $.each(items, function(index, item) {
                    var prodId = item.productId || 'default'; 
                    var ingredients = dummyIngredients[prodId] || dummyIngredients['default'];
                    
                    var itemStatus = 'sufficient';
                    ingredients.forEach(function(ing) {
                        if(ing.status === 'insufficient') itemStatus = 'insufficient';
                        else if(ing.status === 'low' && itemStatus !== 'insufficient') itemStatus = 'low';
                    });

                    var rowClass = '';
                    var iconHtml = '';
                    if(itemStatus === 'insufficient') {
                        rowClass = 'row-stock-critical';
                        iconHtml = '<i class="fa fa-times-circle text-danger mr-1" title="Insufficient Ingredients"></i> ';
                    } else if(itemStatus === 'low') {
                        rowClass = 'row-stock-warning';
                        iconHtml = '<i class="fa fa-exclamation-triangle text-warning mr-1" title="Low Ingredients"></i> ';
                    }

                    itemsHtml += '<tr class="' + rowClass + '">';
                    itemsHtml += '<td>' + iconHtml + '<strong>' + item.name + '</strong></td>';
                    itemsHtml += '<td class="text-center"><input type="number" class="form-control form-control-sm text-center" value="' + item.qty + '"></td>';
                    itemsHtml += '<td><input type="text" class="form-control form-control-sm" value="' + item.note + '"></td>';
                    itemsHtml += '<td class="text-center">';
                    itemsHtml += '<button type="button" class="btn btn-info btn-sm btn-view-stock" ';
                    itemsHtml += 'data-product-name="' + item.name + '" data-product-id="' + prodId + '">';
                    itemsHtml += '<i class="fa fa-eye"></i> Stock</button>';
                    itemsHtml += '</td></tr>';
                });
            }
            $('#modalItemsBody').html(itemsHtml);
        });

        });
    </script>
@endsection