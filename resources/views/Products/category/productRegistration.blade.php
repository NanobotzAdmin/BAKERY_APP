@php
    $privilageId = \DB::table('pm_interfaces')
        ->select('pm_interfaces.id AS pageId', 'pm_interface_topic.id AS grupId')
        ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
        ->where('pm_interfaces.path', 'adminProductRegistration')
        ->first();
@endphp

@extends('layout', ['pageId' => $privilageId->pageId ?? 0, 'grupId' => $privilageId->grupId ?? 0])

@section('content')

    <style>
        .table-hover tbody tr:hover {
            background-color: #faf6ec;
            color: #000;
            transition: background-color 0.2s;
        }

        .table th {
            text-align: center;
            vertical-align: middle !important;
        }
        
        .variation-values-container {
            margin-top: 15px;
            padding: 15px;
            border: 1px solid #e7eaec;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        
        .variation-value-item {
            display: inline-block;
            margin: 5px;
            padding: 5px 10px;
            background-color: #fff;
            border: 1px solid #e7eaec;
            border-radius: 3px;
            cursor: pointer;
        }
        
        .variation-value-item.selected {
            background-color: #007bff;
            color: #fff;
        }
        
        .product-variation-row {
            border: 1px solid #e7eaec;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #f8f9fa;
        }
    </style>

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2><b>Product Registration</b></h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/admindashboard">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a>Product Management</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Product Registration</strong>
                </li>
            </ol>
        </div>
    </div>
    <br>

    <div class="row">
        <div class="col-sm-12">
            <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">
            @include('include.flash')
            @include('include.errors')

            <div class="ibox">
                <div class="ibox-title">
                    <h5>Product Registration Form</h5>
                </div>
                <div class="ibox-content">
                    <form id="productRegistrationForm">
                        {{ csrf_field() }}
                        
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="product_name">Product Name *</label>
                                <input type="text" class="form-control" id="product_name" name="product_name" required autocomplete="off">
                            </div>
                            
                            <div class="form-group col-md-6">
                                <label for="product_code">Product Code *</label>
                                <input type="text" class="form-control" id="product_code" name="product_code" required autocomplete="off">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="product_description">Product Description</label>
                                <textarea class="form-control" id="product_description" name="product_description" rows="3"></textarea>
                            </div>
                            
                            <div class="form-group col-md-6">
                                <label for="pm_product_item_type_id">Product Type *</label>
                                <select class="form-control" id="pm_product_item_type_id" name="pm_product_item_type_id" required>
                                    <option value="">-- Select Product Type --</option>
                                    @foreach ($productItemTypes as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="pm_product_main_category_id">Main Category *</label>
                                <select class="form-control" id="pm_product_main_category_id" name="pm_product_main_category_id" required>
                                    <option value="">-- Select Main Category --</option>
                                    @foreach ($mainCategories as $mainCategory)
                                        <option value="{{ $mainCategory->id }}">{{ $mainCategory->main_category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group col-md-6">
                                <label for="pm_product_sub_category_id">Sub Category *</label>
                                <select class="form-control" id="pm_product_sub_category_id" name="pm_product_sub_category_id" required disabled>
                                    <option value="">-- Select Main Category First --</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Variations</label>
                            <div class="row">
                                @foreach ($variations as $variation)
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input variation-checkbox" type="checkbox" 
                                                   value="{{ $variation->id }}" 
                                                   id="variation_{{ $variation->id }}" 
                                                   data-variation-name="{{ $variation->variation_name }}">
                                            <label class="form-check-label" for="variation_{{ $variation->id }}">
                                                {{ $variation->variation_name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <div id="variationValuesContainer"></div>
                        
                        <div class="form-group text-right">
                            <button type="button" class="btn btn-primary" id="generateProductsBtn">Generate Products</button>
                        </div>
                        
                        <div id="generatedProductsContainer" style="display: none;">
                            <h4>Generated Product Variations</h4>
                            <div id="generatedProductsList"></div>
                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-success" id="saveProductsBtn">Save All Products</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    <script>
        $(document).ready(function() {
            // Load subcategories when main category is selected
            $('#pm_product_main_category_id').on('change', function() {
                var mainCategoryId = $(this).val();
                var csrf_token = $("#csrf_token").val();
                
                if (mainCategoryId) {
                    $.ajax({
                        url: "{{ url('/loadSubCategoriesByMainCategory') }}",
                        type: "POST",
                        data: {
                            "_token": csrf_token,
                            "main_category_id": mainCategoryId
                        },
                        beforeSend: function() {
                            showLder();
                        },
                        complete: function() {
                            hideLder();
                        },
                        error: function(xhr) {
                            hideLder();
                            swal("Error", "Failed to load sub categories", "error");
                        },
                        success: function(response) {
                            hideLder();
                            if (response.status === 'success') {
                                var options = '<option value="">-- Select Sub Category --</option>';
                                $.each(response.data, function(index, subCategory) {
                                    options += '<option value="' + subCategory.id + '">' + subCategory.sub_category_name + '</option>';
                                });
                                $('#pm_product_sub_category_id').html(options).prop('disabled', false);
                            } else {
                                swal("Error", response.message, "error");
                            }
                        }
                    });
                } else {
                    $('#pm_product_sub_category_id').html('<option value="">-- Select Main Category First --</option>').prop('disabled', true);
                }
            });
            
            // Load variation values when variation checkbox is clicked
            $('.variation-checkbox').on('change', function() {
                var variationId = $(this).val();
                var variationName = $(this).data('variation-name');
                var csrf_token = $("#csrf_token").val();
                
                if ($(this).is(':checked')) {
                    // Load variation values
                    $.ajax({
                        url: "{{ url('/loadVariationValuesByVariation') }}",
                        type: "POST",
                        data: {
                            "_token": csrf_token,
                            "variation_id": variationId
                        },
                        beforeSend: function() {
                            showLder();
                        },
                        complete: function() {
                            hideLder();
                        },
                        error: function(xhr) {
                            hideLder();
                            swal("Error", "Failed to load variation values", "error");
                        },
                        success: function(response) {
                            hideLder();
                            if (response.status === 'success') {
                                var html = '<div class="form-group variation-values-group" id="variation-values-' + variationId + '">';
                                html += '<label>' + variationName + ' Values *</label>';
                                html += '<div class="variation-values-container">';
                                
                                if (response.data.length > 0) {
                                    $.each(response.data, function(index, value) {
                                        var unitName = response.types[value.pm_variation_value_type_id] || '';
                                        var displayName = value.variation_value_name ? value.variation_value_name : value.variation_value;
                                        if (unitName) {
                                            displayName += ' (' + unitName + ')';
                                        }
                                        html += '<span class="variation-value-item" data-value-id="' + value.id + '" data-value-name="' + (value.variation_value_name ? value.variation_value_name : value.variation_value) + '">' + displayName + '</span>';
                                    });
                                } else {
                                    html += '<span class="text-muted">No values available</span>';
                                }
                                
                                html += '</div>';
                                html += '</div>';
                                
                                $('#variationValuesContainer').append(html);
                                
                                // Add click event for variation value items
                                $('#variation-values-' + variationId + ' .variation-value-item').on('click', function() {
                                    $(this).toggleClass('selected');
                                });
                            } else {
                                swal("Error", response.message, "error");
                            }
                        }
                    });
                } else {
                    // Remove variation values container
                    $('#variation-values-' + variationId).remove();
                }
            });
            
            // Generate products button
            $('#generateProductsBtn').on('click', function() {
                var productName = $('#product_name').val();
                var productCode = $('#product_code').val();
                var productDescription = $('#product_description').val();
                var productTypeId = $('#pm_product_item_type_id').val();
                var mainCategoryId = $('#pm_product_main_category_id').val();
                var subCategoryId = $('#pm_product_sub_category_id').val();
                
                // Validate required fields
                if (!productName || !productCode || !productTypeId || !mainCategoryId || !subCategoryId) {
                    swal("Error", "Please fill all required fields", "error");
                    return;
                }
                
                // Get selected variation values
                var selectedVariations = [];
                $('.variation-values-group').each(function() {
                    var variationId = $(this).attr('id').replace('variation-values-', '');
                    var variationName = $(this).find('label').text().replace(' Values *', '');
                    var selectedValues = [];
                    
                    $(this).find('.variation-value-item.selected').each(function() {
                        selectedValues.push({
                            id: $(this).data('value-id'),
                            name: $(this).data('value-name')
                        });
                    });
                    
                    if (selectedValues.length > 0) {
                        selectedVariations.push({
                            id: variationId,
                            name: variationName,
                            values: selectedValues
                        });
                    }
                });
                
                if (selectedVariations.length === 0) {
                    swal("Error", "Please select at least one variation value", "error");
                    return;
                }
                
                // Generate product combinations
                var combinations = generateCombinations(selectedVariations);
                
                // Display generated products
                displayGeneratedProducts(combinations, productName, productCode, productDescription, productTypeId, mainCategoryId, subCategoryId);
                
                $('#generatedProductsContainer').show();
            });
            
            // Function to generate combinations
            function generateCombinations(variations) {
                if (variations.length === 0) return [];
                
                var result = [];
                
                function generate(index, current) {
                    if (index === variations.length) {
                        result.push(current.slice());
                        return;
                    }
                    
                    for (var i = 0; i < variations[index].values.length; i++) {
                        current.push({
                            variationId: variations[index].id,
                            variationName: variations[index].name,
                            valueId: variations[index].values[i].id,
                            valueName: variations[index].values[i].name
                        });
                        generate(index + 1, current);
                        current.pop();
                    }
                }
                
                generate(0, []);
                return result;
            }
            
            // Function to display generated products
            function displayGeneratedProducts(combinations, productName, productCode, productDescription, productTypeId, mainCategoryId, subCategoryId) {
                var html = '<div class="row">';
                
                $.each(combinations, function(index, combination) {
                    var combinationName = productName;
                    var combinationCode = productCode + '-' + (index + 1);
                    
                    // Build product name with variations
                    $.each(combination, function(i, item) {
                        combinationName += ' ' + item.valueName;
                    });
                    
                    html += '<div class="col-md-12 product-variation-row">';
                    html += '<div class="form-row">';
                    html += '<div class="form-group col-md-4">';
                    html += '<label>Product Name</label>';
                    html += '<input type="text" class="form-control product-name" value="' + combinationName + '" data-original="' + productName + '" data-combination=\'' + JSON.stringify(combination) + '\'>';
                    html += '</div>';
                    html += '<div class="form-group col-md-3">';
                    html += '<label>Product Code</label>';
                    html += '<input type="text" class="form-control product-code" value="' + combinationCode + '">';
                    html += '</div>';
                    html += '<div class="form-group col-md-2">';
                    html += '<label>Selling Price</label>';
                    html += '<input type="number" step="0.01" class="form-control selling-price" placeholder="0.00">';
                    html += '</div>';
                    html += '<div class="form-group col-md-2">';
                    html += '<label>Cost Price</label>';
                    html += '<input type="number" step="0.01" class="form-control cost-price" placeholder="0.00">';
                    html += '</div>';
                    html += '<div class="form-group col-md-1">';
                    html += '<label>Status</label>';
                    html += '<select class="form-control product-status">';
                    html += '<option value="1">Active</option>';
                    html += '<option value="0">Inactive</option>';
                    html += '</select>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                });
                
                html += '</div>';
                
                $('#generatedProductsList').html(html);
            }
            
            // Save all products
            $('#productRegistrationForm').on('submit', function(e) {
                e.preventDefault();
                
                var products = [];
                var hasError = false;
                
                $('.product-variation-row').each(function() {
                    var productName = $(this).find('.product-name').val();
                    var productCode = $(this).find('.product-code').val();
                    var sellingPrice = $(this).find('.selling-price').val();
                    var costPrice = $(this).find('.cost-price').val();
                    var status = $(this).find('.product-status').val();
                    var combination = $(this).find('.product-name').data('combination');
                    
                    // Validate required fields
                    if (!productName || !productCode) {
                        swal("Error", "Product name and code are required for all products", "error");
                        hasError = true;
                        return false;
                    }
                    
                    if (!sellingPrice || !costPrice) {
                        swal("Error", "Selling price and cost price are required for all products", "error");
                        hasError = true;
                        return false;
                    }
                    
                    // Validate prices
                    if (parseFloat(sellingPrice) < 0 || parseFloat(costPrice) < 0) {
                        swal("Error", "Prices cannot be negative", "error");
                        hasError = true;
                        return false;
                    }
                    
                    products.push({
                        product_name: productName,
                        product_code: productCode,
                        product_description: $('#product_description').val(),
                        pm_product_item_type_id: $('#pm_product_item_type_id').val(),
                        pm_product_main_category_id: $('#pm_product_main_category_id').val(),
                        pm_product_sub_category_id: $('#pm_product_sub_category_id').val(),
                        pm_product_item_variation_id: combination.length > 0 ? combination[0].variationId : null,
                        pm_product_item_variation_value_id: combination.length > 0 ? combination[0].valueId : null,
                        selling_price: sellingPrice,
                        cost_price: costPrice,
                        status: status
                    });
                });
                
                if (hasError) {
                    return;
                }
                
                if (products.length === 0) {
                    swal("Error", "No products to save", "error");
                    return;
                }
                
                // Send to server
                var csrf_token = $("#csrf_token").val();
                
                $.ajax({
                    url: "{{ url('/saveProductItems') }}",
                    type: "POST",
                    data: {
                        "_token": csrf_token,
                        "products": products
                    },
                    beforeSend: function() {
                        showLder();
                        $('#saveProductsBtn').prop('disabled', true);
                    },
                    complete: function() {
                        hideLder();
                        $('#saveProductsBtn').prop('disabled', false);
                    },
                    error: function(xhr) {
                        hideLder();
                        $('#saveProductsBtn').prop('disabled', false);
                        swal("Error", "Failed to save products", "error");
                    },
                    success: function(response) {
                        hideLder();
                        $('#saveProductsBtn').prop('disabled', false);
                        
                        if (response.status === 'success') {
                            swal("Success", response.message, "success");
                            // Reset form
                            $('#productRegistrationForm')[0].reset();
                            $('#pm_product_sub_category_id').html('<option value="">-- Select Main Category First --</option>').prop('disabled', true);
                            $('#variationValuesContainer').empty();
                            $('.variation-checkbox').prop('checked', false);
                            $('#generatedProductsContainer').hide();
                        } else {
                            swal("Error", response.message, "error");
                        }
                    }
                });
            });
        });
    </script>
@endsection