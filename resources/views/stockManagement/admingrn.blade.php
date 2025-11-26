@php
    $pageMeta = isset($privilageId) ? $privilageId : (object) ['pageId' => 'grnPage', 'grupId' => 'invGroup'];

    // DUMMY DATA: Raw Materials available for purchase
    $materials = [
        ['id' => 101, 'name' => 'Wheat Flour (Prima)', 'category' => 'Dry', 'stock' => 500, 'unit' => 'kg', 'cost' => 250.00],
        ['id' => 102, 'name' => 'White Sugar', 'category' => 'Dry', 'stock' => 120, 'unit' => 'kg', 'cost' => 320.00],
        ['id' => 103, 'name' => 'Butter (Astra)', 'category' => 'Cold', 'stock' => 15, 'unit' => 'kg', 'cost' => 1500.00],
        ['id' => 104, 'name' => 'Eggs (Large)', 'category' => 'Cold', 'stock' => 1500, 'unit' => 'pcs', 'cost' => 45.00],
        ['id' => 105, 'name' => 'Yeast (Instant)', 'category' => 'Baking', 'stock' => 5, 'unit' => 'kg', 'cost' => 800.00],
    ];

    // DUMMY DATA: Suppliers
    $suppliers = ['Prima Ceylon', 'Cargills Food City', 'Local Farm (Eggs)', 'Packaging Suppliers Ltd'];
@endphp

@extends('layout', ['pageId' => $pageMeta->pageId, 'grupId' => $pageMeta->grupId])

@section('content')

    {{-- Dependencies --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <style>
        /* Table Input Styling */
        .table-input { border: 1px solid #e5e6e7; border-radius: 4px; padding: 6px; width: 100%; font-size: 13px; }
        .table-input:focus { border-color: #1ab394; outline: none; }
        .table-input-sm { width: 80px; text-align: right; }
        
        /* Select2 Tweaks */
        .select2-container .select2-selection--single { height: 38px; border: 1px solid #e5e6e7; }
        .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 38px; }

        /* Total Box */
        .total-box { background: #f3f3f4; padding: 15px; border-radius: 5px; text-align: right; border: 1px solid #e7eaec; }
        .total-label { font-size: 14px; color: #676a6c; font-weight: 600; }
        .total-amount { font-size: 24px; color: #1ab394; font-weight: 700; }
    </style>

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2><b>Good Received Note (GRN)</b></h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/inventory">Inventory</a></li>
                <li class="breadcrumb-item active"><strong>Receive Stock</strong></li>
            </ol>
        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">
        <form action="" method="POST" id="grnForm">
            @csrf
            
            {{-- 1. GENERAL INFORMATION --}}
            <div class="ibox mb-3">
                <div class="ibox-title">
                    <h5>General Information</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="font-bold">Supplier</label>
                            <select class="form-control select2">
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $sup)
                                    <option value="{{ $sup }}">{{ $sup }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="font-bold">Invoice / Ref No</label>
                            <input type="text" class="form-control" placeholder="e.g. INV-2023-001" required>
                        </div>
                        <div class="col-md-3">
                            <label class="font-bold">Received Date</label>
                            <input type="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="font-bold">Payment Status</label>
                            <select class="form-control">
                                <option>Credit</option>
                                <option>Paid (Cash)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. ITEM SELECTION --}}
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Items Received</h5>
                </div>
                <div class="ibox-content pt-2">
                    
                    {{-- Selection Bar --}}
                    <div class="row align-items-end mb-4 p-3" style="background: #f9f9f9; border-radius: 6px;">
                        <div class="col-md-8">
                            <label class="small text-muted">Select Raw Material</label>
                            <select id="materialSelect" class="form-control select2">
                                <option value="" disabled selected>Search by name...</option>
                                @foreach($materials as $item)
                                    <option value="{{ $item['id'] }}" 
                                        data-name="{{ $item['name'] }}"
                                        data-unit="{{ $item['unit'] }}"
                                        data-stock="{{ $item['stock'] }}"
                                        data-cost="{{ $item['cost'] }}">
                                        {{ $item['name'] }} (Current Stock: {{ $item['stock'] }} {{ $item['unit'] }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="button" id="btnAddRow" class="btn btn-primary btn-block" style="height: 38px;">
                                <i class="fa fa-plus-circle"></i> Add to GRN
                            </button>
                        </div>
                    </div>

                    {{-- Data Table --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="grnTable">
                            <thead class="thead-light">
                                <tr>
                                    <th width="30%">Material Details</th>
                                    <th width="10%" class="text-center">Current Stock</th>
                                    <th width="15%" class="text-center">Received Qty</th>
                                    <th width="15%" class="text-right">Unit Cost (Rs)</th>
                                    <th width="15%" class="text-right">Line Total (Rs)</th>
                                    <th width="15%">Expiry Date</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <tr id="emptyRow">
                                    <td colspan="7" class="text-center text-muted p-4">
                                        No items added. Please select materials above.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Footer Totals --}}
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <label>Note / Remarks</label>
                            <textarea class="form-control" rows="3" placeholder="e.g. Delivered by lorry XX-1234"></textarea>
                        </div>
                        <div class="col-md-6">
                            <div class="total-box">
                                <div class="total-label">Grand Total</div>
                                <div class="total-amount" id="grandTotal">0.00</div>
                                <small class="text-muted">Total items: <span id="totalItems">0</span></small>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg btn-block mt-3 shadow">
                                <i class="fa fa-check"></i> Save GRN
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>

    {{-- JS Libraries --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // 1. Init Select2
            $('.select2').select2({ width: '100%' });

            // 2. Add Row Function
            $('#btnAddRow').click(function() {
                var select = $('#materialSelect');
                var id = select.val();

                if (!id) {
                    alert('Please select a material first.');
                    return;
                }

                if ($('#row-' + id).length > 0) {
                    alert('This item is already added. Please update the quantity below.');
                    return;
                }

                // Get Data
                var option = select.find(':selected');
                var name = option.data('name');
                var unit = option.data('unit');
                var stock = option.data('stock');
                var defaultCost = parseFloat(option.data('cost')).toFixed(2);

                // Remove Empty Row
                $('#emptyRow').remove();

                // Append HTML
                var row = `
                    <tr id="row-${id}">
                        <td>
                            <strong>${name}</strong>
                            <input type="hidden" name="items[${id}][id]" value="${id}">
                        </td>
                        <td class="text-center" style="vertical-align: middle;">
                            <span class="badge badge-info">${stock} ${unit}</span>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" step="0.01" class="table-input" name="items[${id}][qty]" value="1" min="0" onkeyup="calcRow(${id})" onchange="calcRow(${id})" id="qty-${id}">
                                <div class="input-group-append">
                                    <span class="input-group-text" style="padding: 2px 8px; font-size: 11px;">${unit}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <input type="number" step="0.01" class="table-input text-right" name="items[${id}][cost]" value="${defaultCost}" onkeyup="calcRow(${id})" onchange="calcRow(${id})" id="cost-${id}">
                        </td>
                        <td class="text-right font-bold" style="vertical-align: middle;">
                            <span id="total-${id}">${defaultCost}</span>
                        </td>
                         <td>
                            <input type="date" class="table-input" name="items[${id}][expiry]">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-xs btn-danger" onclick="removeRow(${id})"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                `;

                $('#tableBody').append(row);
                select.val(null).trigger('change');
                calcGrandTotal();
            });

            // 3. Remove Row Function
            window.removeRow = function(id) {
                $('#row-' + id).remove();
                if ($('#tableBody tr').length === 0) {
                    $('#tableBody').html('<tr id="emptyRow"><td colspan="7" class="text-center text-muted p-4">No items added.</td></tr>');
                }
                calcGrandTotal();
            }

            // 4. Calculate Single Row
            window.calcRow = function(id) {
                var qty = parseFloat($('#qty-' + id).val()) || 0;
                var cost = parseFloat($('#cost-' + id).val()) || 0;
                var total = qty * cost;
                $('#total-' + id).text(total.toFixed(2));
                calcGrandTotal();
            }

            // 5. Calculate Grand Total
            window.calcGrandTotal = function() {
                var grandTotal = 0;
                var count = 0;

                $('#tableBody tr').not('#emptyRow').each(function() {
                    var id = $(this).attr('id').replace('row-', '');
                    var rowTotal = parseFloat($('#total-' + id).text()) || 0;
                    grandTotal += rowTotal;
                    count++;
                });

                $('#grandTotal').text(grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                $('#totalItems').text(count);
            }
        });
    </script>

@endsection