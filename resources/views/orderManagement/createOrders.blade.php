@php
    $pageMeta = isset($privilageId) ? $privilageId : (object) ['pageId' => 'branchOrder', 'grupId' => 'branchGroup'];
    
    // DUMMY DATA: Products with metadata attached
    // 'ingredient_stock' simulates the calculated potential output based on raw materials in the warehouse.
    $products = [
        ['id' => 101, 'name' => 'Sandwich Bread', 'unit' => 'Loaf', 'ingredient_stock' => 150, 'max_limit' => 50],
        ['id' => 102, 'name' => 'Kade Paan (Roast)', 'unit' => 'Loaf', 'ingredient_stock' => 80, 'max_limit' => 40],
        ['id' => 201, 'name' => 'Fish Bun', 'unit' => 'Pcs', 'ingredient_stock' => 500, 'max_limit' => 200],
        ['id' => 202, 'name' => 'Chicken Roll', 'unit' => 'Pcs', 'ingredient_stock' => 300, 'max_limit' => 100],
        ['id' => 301, 'name' => 'Butter Cake (500g)', 'unit' => 'Pack', 'ingredient_stock' => 45, 'max_limit' => 20],
        ['id' => 302, 'name' => 'Chocolate Eclair', 'unit' => 'Pcs', 'ingredient_stock' => 120, 'max_limit' => 60],
    ];
@endphp

@extends('layout', ['pageId' => $pageMeta->pageId, 'grupId' => $pageMeta->grupId])

@section('content')

    {{-- Include Select2 CSS (If not already in layout) --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Fix Select2 Height to match Bootstrap */
        .select2-container .select2-selection--single { height: 38px; border: 1px solid #ced4da; }
        .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 38px; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px; }

        /* Table Inputs */
        .qty-input { width: 100px; text-align: center; border: 2px solid #e7eaec; border-radius: 4px; font-weight: bold; color: #333; }
        .qty-input:focus { border-color: #1ab394; outline: none; }
        
        /* Stock Badges */
        .stock-info { font-size: 12px; font-weight: 600; padding: 3px 8px; border-radius: 10px; }
        .stock-high { background: #d1fae5; color: #065f46; } /* Green */
        .stock-low { background: #fee2e2; color: #991b1b; } /* Red */
        .stock-med { background: #ffedd5; color: #9a3412; } /* Orange */

        .remove-row { color: #dc3545; cursor: pointer; transition: 0.2s; }
        .remove-row:hover { transform: scale(1.2); }
    </style>

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2><b>Branch Request</b></h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active"><strong>Create Request</strong></li>
            </ol>
        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">
        
        <form action="" method="POST" id="requestForm">
            @csrf
            
            {{-- 1. ORDER DETAILS HEADER --}}
            <div class="ibox mb-3">
                <div class="ibox-title">
                    <h5><i class="fa fa-info-circle"></i> Request Details</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-4">
                            <label>Required Date</label>
                            <input type="date" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>Priority</label>
                            <select class="form-control">
                                <option>Standard</option>
                                <option>Urgent</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Note</label>
                            <input type="text" class="form-control" placeholder="Optional notes...">
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. ADD ITEMS SECTION --}}
            <div class="ibox">
                <div class="ibox-title">
                    <h5><i class="fa fa-cart-plus"></i> Add Products</h5>
                </div>
                <div class="ibox-content" style="background-color: #f9f9f9; border-bottom: 1px solid #e7eaec;">
                    <div class="row align-items-end">
                        <div class="col-md-8">
                            <label class="font-weight-bold">Select Product</label>
                            <select id="productSelect" class="form-control select2" style="width: 100%;">
                                <option value="" disabled selected>Search for a product...</option>
                                @foreach($products as $p)
                                    <option value="{{ $p['id'] }}" 
                                        data-name="{{ $p['name'] }}" 
                                        data-unit="{{ $p['unit'] }}"
                                        data-stock="{{ $p['ingredient_stock'] }}"
                                        data-max="{{ $p['max_limit'] }}">
                                        {{ $p['name'] }} (Max: {{ $p['max_limit'] }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="button" id="btnAddRow" class="btn btn-primary btn-block" style="height: 38px;">
                                <i class="fa fa-plus"></i> Add to Request
                            </button>
                        </div>
                    </div>
                </div>

                {{-- 3. THE TABLE --}}
                <div class="ibox-content p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="requestTable">
                            <thead class="thead-light">
                                <tr>
                                    <th width="35%">Product Name</th>
                                    <th width="20%" class="text-center">Ingredient Availability</th>
                                    <th width="15%" class="text-center">Max Allowed</th>
                                    <th width="20%" class="text-center">Request Qty</th>
                                    <th width="10%" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <tr id="emptyRow">
                                    <td colspan="5" class="text-center text-muted p-4">
                                        <i class="fa fa-inbox fa-3x mb-2"></i><br>
                                        No items added yet. Search above to start.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                {{-- 4. FOOTER SUBMIT --}}
                <div class="ibox-footer text-right">
                    <button type="button" class="btn btn-white btn-lg mr-2">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fa fa-paper-plane"></i> Submit Request
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Scripts --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // 1. Initialize Select2
            $('.select2').select2({
                placeholder: "Type product name...",
                allowClear: true
            });

            // 2. Add Row Functionality
            $('#btnAddRow').click(function() {
                var select = $('#productSelect');
                var id = select.val();
                
                // Validation: Check if selected
                if (!id) {
                    alert('Please select a product first.');
                    return;
                }

                // Check if already exists in table
                if ($('#row-' + id).length > 0) {
                    alert('This product is already in the list. Please update the quantity there.');
                    // Highlight existing row
                    $('#row-' + id).css('background-color', '#fff3cd').animate({backgroundColor: "transparent"}, 1000);
                    return;
                }

                // Get Data Attributes from Option
                var option = select.find(':selected');
                var name = option.data('name');
                var unit = option.data('unit');
                var stock = parseInt(option.data('stock'));
                var max = parseInt(option.data('max'));

                // Determine Stock Color badge
                var stockClass = 'stock-med';
                if(stock > 50) stockClass = 'stock-high';
                if(stock < 20) stockClass = 'stock-low';

                // Remove Empty Row Message
                $('#emptyRow').remove();

                // Append Row HTML
                var rowHtml = `
                    <tr id="row-${id}">
                        <td style="vertical-align: middle;">
                            <h5 class="mb-0 text-navy">${name}</h5>
                            <small class="text-muted">Unit: ${unit}</small>
                            <input type="hidden" name="items[${id}][id]" value="${id}">
                        </td>
                        <td class="text-center" style="vertical-align: middle;">
                            <span class="stock-info ${stockClass}">
                                ${stock} Approx. Yield
                            </span>
                            <br><small class="text-muted">Based on Ingredients</small>
                        </td>
                        <td class="text-center" style="vertical-align: middle;">
                            <span class="badge badge-secondary">${max} ${unit}</span>
                            <input type="hidden" class="max-val" value="${max}">
                        </td>
                        <td class="text-center">
                            <input type="number" 
                                   name="items[${id}][qty]" 
                                   class="form-control qty-input mx-auto" 
                                   value="0" 
                                   min="1" 
                                   max="${max}"
                                   onkeyup="validateQty(this, ${max})">
                            <small class="text-danger error-msg" style="display:none;">Exceeds Limit!</small>
                        </td>
                        <td class="text-center" style="vertical-align: middle;">
                            <i class="fa fa-trash fa-lg remove-row" onclick="removeRow(${id})"></i>
                        </td>
                    </tr>
                `;

                $('#tableBody').append(rowHtml);

                // Reset Select2
                select.val(null).trigger('change');
            });

            // 3. Remove Row Functionality
            window.removeRow = function(id) {
                $('#row-' + id).remove();
                
                // Show empty message if table is clear
                if ($('#tableBody tr').length === 0) {
                    $('#tableBody').html(`
                        <tr id="emptyRow">
                            <td colspan="5" class="text-center text-muted p-4">
                                <i class="fa fa-inbox fa-3x mb-2"></i><br>
                                No items added yet. Search above to start.
                            </td>
                        </tr>
                    `);
                }
            };

            // 4. Quantity Validation Logic
            window.validateQty = function(input, max) {
                var val = parseInt($(input).val());
                var errorMsg = $(input).siblings('.error-msg');
                
                if (val > max) {
                    $(input).css('border-color', '#dc3545'); // Red border
                    errorMsg.show();
                } else {
                    $(input).css('border-color', '#e7eaec'); // Reset border
                    errorMsg.hide();
                }
            };
        });
    </script>

@endsection