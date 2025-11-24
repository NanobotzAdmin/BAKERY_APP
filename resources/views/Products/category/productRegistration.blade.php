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
        
    </style>
<div class="container-fluid">
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2 class="text-dark"><b>Product Registration</b></h2>
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
        <div class="col-sm-12 p-0">
            <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">
            @include('include.flash')
            @include('include.errors')

            <div class="ibox text-dark">
                <div class="ibox-title">
                    <h5>Product Registration Form</h5>
                </div>
                <div class="ibox-content">
                    <form id="productRegistrationForm">
                        {{ csrf_field() }}
                        
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="product_name">Product Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="product_name" name="product_name" required autocomplete="off">
                            </div>
                            
                            <div class="form-group col-md-6">
                                <label for="product_code">Product Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="product_code" name="product_code" required autocomplete="off">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="product_description">Product Description</label>
                                <textarea class="form-control" id="product_description" name="product_description" rows="3"></textarea>
                            </div>
                            
                            <div class="form-group col-md-6">
                                <label for="pm_product_item_type_id">Product Type <span class="text-danger">*</span></label>
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
                                <label for="pm_product_main_category_id">Main Category <span class="text-danger">*</span></label>
                                <select class="form-control" id="pm_product_main_category_id" name="pm_product_main_category_id" required>
                                    <option value="">-- Select Main Category --</option>
                                    @foreach ($mainCategories as $mainCategory)
                                        <option value="{{ $mainCategory->id }}">{{ $mainCategory->main_category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group col-md-6">
                                <label for="pm_product_sub_category_id">Sub Category <span class="text-danger">*</span></label>
                                <select class="form-control" id="pm_product_sub_category_id" name="pm_product_sub_category_id" required disabled>
                                    <option value="">-- Select Main Category First --</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="mb-3"><strong>Select Variations</strong></label>
                            <div class="row">
                                @foreach ($variations as $variation)
                                    <div class="col-md-4 mb-3">
                                        <div class="variation-card" data-variation-id="{{ $variation->id }}">
                                            <div class="variation-card-header">
                                                <input class="variation-checkbox" type="checkbox" 
                                                       value="{{ $variation->id }}" 
                                                       id="variation_{{ $variation->id }}" 
                                                       data-variation-name="{{ $variation->variation_name }}">
                                                <label for="variation_{{ $variation->id }}">
                                                    {{ $variation->variation_name }}
                                                </label>
                                            </div>
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
</div>
@endsection

@section('footer')
    <script>
        $(document).ready(function() {
            // Check initial product type on page load
            if ($('#pm_product_item_type_id').val() == '1') {
                $('.price-fields-container').addClass('show');
            }
            
            // Handle variation checkbox click to update card styling
            $('.variation-checkbox').on('click', function() {
                var variationId = $(this).val();
                var card = $('.variation-card[data-variation-id="' + variationId + '"]');
                if ($(this).is(':checked')) {
                    card.addClass('selected');
                } else {
                    card.removeClass('selected');
                }
            });
            
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
            $(document).on('change', '.variation-checkbox', function() {
                var variationId = $(this).val();
                var variationName = $(this).data('variation-name');
                var csrf_token = $("#csrf_token").val();
                var card = $('.variation-card[data-variation-id="' + variationId + '"]');
                
                if ($(this).is(':checked')) {
                    card.addClass('selected');
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
                                var html = '<div class="form-group variation-values-group mb-4" id="variation-values-' + variationId + '">';
                                html += '<div class="variation-card selected">';
                                html += '<div class="variation-card-header">';
                                html += '<label class="mb-2"><strong>' + variationName + ' - Select Values</strong></label>';
                                html += '</div>';
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
                                html += '</div>';
                                
                                $('#variationValuesContainer').append(html);
                                
                                // Add click event for variation value items
                                $('#variation-values-' + variationId + ' .variation-value-item').on('click', function() {
                                    $(this).toggleClass('selected');
                                });
                                
                                // Update variation card to show selected state
                                $('.variation-card[data-variation-id="' + variationId + '"]').addClass('selected');
                            } else {
                                swal("Error", response.message, "error");
                            }
                        }
                    });
                } else {
                    // Remove variation values container
                    $('#variation-values-' + variationId).remove();
                    // Remove selected state from variation card
                    card.removeClass('selected');
                }
            });
            
            // Handle Product Type change to show/hide price fields
            $('#pm_product_item_type_id').on('change', function() {
                var productTypeId = $(this).val();
                if (productTypeId == '1') { // Selling Product
                    $('.price-fields-container').addClass('show');
                } else {
                    $('.price-fields-container').removeClass('show');
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
                    html += '<input readonly type="text" class="form-control product-name" value="' + combinationName + '" data-original="' + productName + '" data-combination=\'' + JSON.stringify(combination) + '\'>';
                    html += '</div>';
                    html += '<div class="form-group col-md-3">';
                    html += '<label>Product Code</label>';
                    html += '<input readonly type="text" class="form-control product-code" value="' + combinationCode + '">';
                    html += '</div>';
                    // Only show price fields if product type is Selling Product (1)
                    if (productTypeId == '1') {
                        html += '<div class="form-group col-md-2 price-fields-container show">';
                        html += '<label>Selling Price</label>';
                        html += '<input type="number" step="0.01" class="form-control selling-price" placeholder="0.00">';
                        html += '</div>';
                        html += '<div class="form-group col-md-2 price-fields-container show">';
                        html += '<label>Cost Price</label>';
                        html += '<input type="number" step="0.01" class="form-control cost-price" placeholder="0.00">';
                        html += '</div>';
                    } else {
                        html += '<input type="hidden" class="selling-price" value="0">';
                        html += '<input type="hidden" class="cost-price" value="0">';
                    }
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
                    var combination = $(this).find('.product-name').data('combination');
                    
                    // Validate required fields
                    if (!productName || !productCode) {
                        swal("Error", "Product name and code are required for all products", "error");
                        hasError = true;
                        return false;
                    }
                    
                    // Set default values if prices are empty
                    sellingPrice = sellingPrice || 0;
                    costPrice = costPrice || 0;
                    
                    // Validate prices (only if provided)
                    if (sellingPrice && parseFloat(sellingPrice) < 0) {
                        swal("Error", "Selling price cannot be negative", "error");
                        hasError = true;
                        return false;
                    }
                    if (costPrice && parseFloat(costPrice) < 0) {
                        swal("Error", "Cost price cannot be negative", "error");
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
                        status: 1 // Default to active
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
                            swal({
                                title: "Success",
                                text: response.message,
                                type: "success",
                                showConfirmButton: true
                            }, function() {
                                window.location.reload();
                            });
                        } else {
                            swal("Error", response.message, "error");
                        }
                    }
                });
            });
        });
    </script>
@endsection