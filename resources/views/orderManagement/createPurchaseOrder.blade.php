@php
    $pageMeta = isset($privilageId) ? $privilageId : (object) ['pageId' => 'reorder', 'grupId' => 'inventory'];

    // --- DUMMY DATA: SUPPLIERS ---
    $suppliers = [
        ['id' => 1, 'name' => 'Prima Ceylon Ltd', 'contact' => '011-2345678', 'address' => 'Colombo 03'],
        ['id' => 2, 'name' => 'Cargills Food City (Bulk)', 'contact' => '077-1231231', 'address' => 'Rajagiriya'],
        ['id' => 3, 'name' => 'Wijaya Products', 'contact' => '034-2223334', 'address' => 'Kalutara'],
        ['id' => 4, 'name' => 'Farm Fresh Eggs', 'contact' => '071-5556667', 'address' => 'Kurunegala']
    ];

    // --- DUMMY DATA: RAW MATERIALS (Global List) ---
    // In a real app, you might filter these via AJAX based on the selected supplier
    $ingredients = [
        ['id' => 'ING-001', 'name' => 'Wheat Flour', 'unit' => 'kg', 'cost' => 250.00],
        ['id' => 'ING-002', 'name' => 'White Sugar', 'unit' => 'kg', 'cost' => 320.00],
        ['id' => 'ING-003', 'name' => 'Butter (Salted)', 'unit' => 'kg', 'cost' => 2800.00],
        ['id' => 'ING-004', 'name' => 'Eggs (Large)', 'unit' => 'pcs', 'cost' => 55.00],
        ['id' => 'ING-005', 'name' => 'Yeast', 'unit' => 'g', 'cost' => 4.50],
        ['id' => 'ING-006', 'name' => 'Vanilla Extract', 'unit' => 'ml', 'cost' => 12.00]
    ];
@endphp

@extends('layout', ['pageId' => $pageMeta->pageId, 'grupId' => $pageMeta->grupId])

@section('content')

    {{-- LIBRARIES --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .ibox {
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            border-radius: 6px;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }

        .total-box {
            background-color: #2f4050;
            color: #fff;
            padding: 15px;
            border-radius: 4px;
            text-align: right;
        }

        .total-label {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .total-amount {
            font-size: 24px;
            font-weight: bold;
        }

        .btn-add-item {
            margin-top: 29px;
        }

        /* Align with inputs */
    </style>

    {{-- Page Header --}}
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2><b>Create Purchase Order</b></h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                <li class="breadcrumb-item">Inventory</li>
                <li class="breadcrumb-item active"><strong>Reorder Stock</strong></li>
            </ol>
        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">

        <form id="purchaseOrderForm" action="" method="POST">
            @csrf

            {{-- SECTION 1: SUPPLIER DETAILS --}}
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox border-bottom">
                        <div class="ibox-title">
                            <h5><i class="fa fa-truck"></i> Select Supplier</h5>
                        </div>
                        <div class="ibox-content" style="background-color: #f3f6fb;">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Supplier Name <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="supplierSelect" name="supplier_id"
                                            required>
                                            <option></option>
                                            @foreach($suppliers as $sup)
                                                <option value="{{ $sup['id'] }}" data-contact="{{ $sup['contact'] }}"
                                                    data-address="{{ $sup['address'] }}">
                                                    {{ $sup['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Contact Number</label>
                                        <input type="text" class="form-control" id="supplierContact" readonly
                                            placeholder="---">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Expected Delivery</label>
                                        <input type="date" class="form-control" name="expected_date" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>PO Reference</label>
                                        <input type="text" class="form-control" value="PO-{{ rand(1000, 9999) }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 2: ADD ITEMS --}}
            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5><i class="fa fa-cubes"></i> Add Items to Order</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Raw Material / Ingredient</label>
                                        <select class="form-control select2" id="ingredientSelect">
                                            <option></option>
                                            @foreach($ingredients as $ing)
                                                <option value="{{ $ing['id'] }}" data-unit="{{ $ing['unit'] }}"
                                                    data-cost="{{ $ing['cost'] }}">
                                                    {{ $ing['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Unit Cost (Rs)</label>
                                        <input type="number" class="form-control" id="itemCost" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Quantity</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="itemQty" value="1" min="1">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="itemUnitDisplay">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Total (Rs)</label>
                                        <input type="text" class="form-control" id="itemTotalDisplay" readonly
                                            placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-info btn-block btn-add-item" id="btnAddToTable">
                                        <i class="fa fa-plus"></i> Add to List
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 3: BOTTOM TABLE (CART) --}}
            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="ibox">
                        <div class="ibox-content p-0">
                            <table class="table table-striped table-hover mb-0" id="poTable">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="35%">Description / Ingredient</th>
                                        <th width="15%" class="text-right">Unit Cost</th>
                                        <th width="15%" class="text-center">Quantity</th>
                                        <th width="15%" class="text-right">Line Total</th>
                                        <th width="15%" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- JS will populate this --}}
                                    <tr id="emptyRow">
                                        <td colspan="6" class="text-center text-muted p-4">
                                            <i class="fa fa-shopping-basket fa-3x mb-2"></i><br>
                                            No items added yet. Please select materials above.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="ibox-footer" style="background: #fff; border-top: 1px solid #e7eaec;">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <label>Internal Notes / Comments</label>
                                    <textarea class="form-control" name="notes" rows="2"
                                        placeholder="Ex: Please deliver before 10 AM"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <div class="total-box">
                                        <span class="total-label">Total Estimate:</span>
                                        <span class="total-amount">Rs. <span id="grandTotal">0.00</span></span>
                                    </div>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button type="button" class="btn btn-white btn-lg"
                                        onclick="window.history.back();">Cancel</button>
                                    <button type="submit" class="btn btn-primary btn-lg" id="btnCreatePO" disabled>
                                        <i class="fa fa-paper-plane"></i> Create Purchase Order
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- HIDDEN INPUT to store the actual array of items --}}
            <input type="hidden" name="cart_items" id="hiddenCartItems">

        </form>
    </div>

    {{-- SCRIPTS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> {{-- Optional for nice alerts --}}

    <script>
        $(document).ready(function () {

            // --- 1. INITIALIZE SELECT2 ---
            $('.select2').select2({
                placeholder: "Select an option",
                allowClear: true,
                width: '100%'
            });

            // --- 2. GLOBAL VARIABLES ---
            var cartItems = [];
            var grandTotal = 0.00;

            // --- 3. SUPPLIER CHANGE EVENT ---
            $('#supplierSelect').on('select2:select', function (e) {
                var contact = $(this).find(':selected').data('contact');
                $('#supplierContact').val(contact);
            });
            $('#supplierSelect').on('select2:clear', function (e) {
                $('#supplierContact').val('');
            });

            // --- 4. INGREDIENT CHANGE EVENT ---
            $('#ingredientSelect').on('select2:select', function (e) {
                var unit = $(this).find(':selected').data('unit');
                var cost = $(this).find(':selected').data('cost');

                $('#itemUnitDisplay').text(unit);
                $('#itemCost').val(cost);
                calculateLineTotal();
            });

            $('#ingredientSelect').on('select2:clear', function (e) {
                $('#itemUnitDisplay').text('-');
                $('#itemCost').val('');
                $('#itemTotalDisplay').val('');
            });

            // --- 5. CALCULATE LINE TOTAL ON INPUT ---
            $('#itemCost, #itemQty').on('input', function () {
                calculateLineTotal();
            });

            function calculateLineTotal() {
                var cost = parseFloat($('#itemCost').val()) || 0;
                var qty = parseFloat($('#itemQty').val()) || 0;
                var total = cost * qty;
                $('#itemTotalDisplay').val(total.toFixed(2));
            }

            // --- 6. ADD TO TABLE LOGIC ---
            $('#btnAddToTable').click(function () {
                // Validation
                var ingId = $('#ingredientSelect').val();
                var ingName = $('#ingredientSelect').find(':selected').text().trim();
                var unit = $('#itemUnitDisplay').text();
                var cost = parseFloat($('#itemCost').val());
                var qty = parseFloat($('#itemQty').val());

                if (!ingId || isNaN(cost) || isNaN(qty) || qty <= 0) {
                    // Use standard alert or SweetAlert
                    Swal.fire({
                        title: 'Error',
                        text: 'Please select a valid ingredient, cost, and quantity.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                // Create Object
                var lineTotal = cost * qty;
                var item = {
                    id: ingId,
                    name: ingName,
                    unit: unit,
                    cost: cost,
                    qty: qty,
                    total: lineTotal
                };

                // Add to Array
                cartItems.push(item);

                // Re-render Table
                renderTable();

                // Reset Inputs (keep Supplier)
                $('#ingredientSelect').val(null).trigger('change');
                $('#itemQty').val(1);
                $('#itemTotalDisplay').val('');
                $('#itemCost').val('');
            });

            // --- 7. RENDER TABLE FUNCTION ---
            function renderTable() {
                var $tbody = $('#poTable tbody');
                $tbody.empty();

                if (cartItems.length === 0) {
                    $tbody.html('<tr id="emptyRow"><td colspan="6" class="text-center text-muted p-4"><i class="fa fa-shopping-basket fa-3x mb-2"></i><br>No items added yet.</td></tr>');
                    $('#btnCreatePO').prop('disabled', true);
                    $('#grandTotal').text('0.00');
                    return;
                }

                $('#btnCreatePO').prop('disabled', false);
                var total = 0;

                $.each(cartItems, function (index, item) {
                    total += item.total;

                    var row = `
                            <tr>
                                <td>${index + 1}</td>
                                <td><strong>${item.name}</strong></td>
                                <td class="text-right">${item.cost.toFixed(2)}</td>
                                <td class="text-center">
                                    <span class="badge badge-info" style="font-size:12px;">${item.qty} ${item.unit}</span>
                                </td>
                                <td class="text-right"><strong>${item.total.toFixed(2)}</strong></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-xs btn-danger btn-remove" data-index="${index}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    $tbody.append(row);
                });

                // Update Grand Total
                $('#grandTotal').text(total.toFixed(2));

                // Update Hidden Input for Form Submission
                $('#hiddenCartItems').val(JSON.stringify(cartItems));
            }

            // --- 8. REMOVE ITEM LOGIC ---
            $(document).on('click', '.btn-remove', function () {
                var index = $(this).data('index');
                cartItems.splice(index, 1);
                renderTable();
            });

            // --- 9. HANDLE FORM SUBMISSION (WITH LOADER FIX) ---
            $('#purchaseOrderForm').on('submit', function (e) {
                e.preventDefault(); // Prevent actual submit

                // 1. Validation
                if (cartItems.length === 0) {

                    Swal.fire({
                        title: 'Error',
                        text: 'Please add items to the list first.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }

                // 2. SHOW LOADER (Button State)
                var $btn = $('#btnCreatePO');
                var originalBtnText = $btn.html(); // Save "Create Purchase Order" text

                // Change button text to spinner and disable it
                $btn.prop('disabled', true).html('<i class="fa fa-circle-o-notch fa-spin"></i> Processing...');

                // 3. SIMULATE SERVER REQUEST (AJAX)
                // Use setTimeout to simulate a 1.5 second delay for the "backend"
                setTimeout(function () {

                    // --- SUCCESS SCENARIO ---

                    // 4. HIDE LOADER (Reset Button to original state)
                    $btn.prop('disabled', false).html(originalBtnText);
                    hideLder();
                    // 5. Show Success Message
                    Swal.fire({
                        title: 'Purchase Order Created!',
                        text: 'PO has been sent to the supplier successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#1ab394' // Optional: Match your theme color
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Reload page or redirect
                            location.reload();
                        }
                    });

                }, 1500); // 1.5 Seconds Delay
            });

            // --- 10. CHECK URL PARAMS (If coming from Stock Modal) ---
            const urlParams = new URLSearchParams(window.location.search);
            const preSelectIngredient = urlParams.get('ingredient');
            if (preSelectIngredient) {
                // Try to match text in Select2
                $('#ingredientSelect option').filter(function () {
                    return $(this).text().trim() == preSelectIngredient;
                }).prop('selected', true).trigger('change');
            }

        });
    </script>
@endsection