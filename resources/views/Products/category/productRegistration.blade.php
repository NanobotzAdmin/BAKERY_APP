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
        .variant-config-card {
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 12px;
            background-color: #fcfcfc;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        }

        .variant-config-card select {
            height: auto;
        }

        .variant-config-header .btn-xs {
            padding: 2px 6px;
            font-size: 12px;
            line-height: 1.2;
        }

        .selected-variation-tags {
            min-height: 38px;
            border: 1px dashed #dadada;
            background: #fff;
            border-radius: 4px;
            padding: 6px;
            margin-top: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .selected-variation-tags .tag-chip {
            background: #eef2ff;
            color: #394263;
            border-radius: 12px;
            padding: 3px 10px;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
        }

        .selected-variation-tags .tag-placeholder {
            color: #9b9b9b;
            font-size: 12px;
        }

        .select2-container .select2-selection--multiple {
            min-height: 40px;
            border-radius: 4px;
            border-color: #ced4da;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #5e72e4;
            border: none;
            color: #fff;
            padding: 2px 8px;
            margin-top: 4px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #fff;
            margin-right: 4px;
        }
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
                                <label for="pm_brands_id">Brand</label>
                                <select class="form-control" id="pm_brands_id" name="pm_brands_id">
                                    <option value="">-- Select Brand --</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->label }}</option>
                                    @endforeach
                                </select>
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
                            <label class="mb-3"><strong>Product Variants Configuration</strong></label>
                            <div class="row">
                                @foreach ($variations as $variation)
                                    <div class="col-md-4 mb-3">
                                        <div class="variant-config-card">
                                            <div class="variant-config-header d-flex justify-content-between align-items-center">
                                                <label class="mb-0 text-uppercase small font-weight-bold text-muted">
                                                    {{ $variation->variation_name }} <span class="text-danger">*</span>
                                                </label>
                                                <button type="button"
                                                        class="btn btn-dark btn-xs add-variation-btn"
                                                        data-variation-id="{{ $variation->id }}"
                                                        data-variation-name="{{ $variation->variation_name }}"
                                                        title='Manage Values for "{{ $variation->variation_name }}"'>
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                            <select class="form-control variation-value-select"
                                                    data-variation-id="{{ $variation->id }}"
                                                    data-variation-name="{{ $variation->variation_name }}"
                                                    data-placeholder="Select {{ $variation->variation_name }}"
                                                    multiple>
                                                <option value=""></option>
                                            </select>
                                            <div class="selected-variation-tags" data-variation-id="{{ $variation->id }}">
                                                <span class="tag-placeholder">No values selected</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <small class="text-muted">
                                Hold <kbd>Ctrl</kbd> (or <kbd>Cmd</kbd>) to select multiple values per attribute.
                            </small>
                        </div>
                        
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

<!-- Variation Values Modal -->
<div class="modal fade" id="productVariationValuesModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="productVariationValuesModalLabel">Manage Variation Values</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="pr_current_variation_id">
                <input type="hidden" id="pr_current_variation_name">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Add / Update Variation Value</h5>
                    </div>
                    <div class="ibox-content">
                        <form id="prVariationValueForm">
                            {{ csrf_field() }}
                            <input type="hidden" id="pr_variation_value_id">
                            <input type="hidden" id="pr_pm_variation_id">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="pr_variation_value_name">Value Name (Optional)</label>
                                    <input type="text" class="form-control" id="pr_variation_value_name" placeholder="e.g. Medium">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="pr_variation_value">Value <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="pr_variation_value" placeholder="e.g. 500, Red" autocomplete="off" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="pr_pm_variation_value_type_id">Unit Type <span class="text-danger">*</span></label>
                                    <select class="form-control" id="pr_pm_variation_value_type_id" required>
                                        <option value="">-- Select Unit --</option>
                                        <option value="0">Default</option>
                                        @foreach ($variationValueTypes as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="button" class="btn btn-secondary" id="pr_cancelVariationValueBtn">Reset</button>
                                <button type="submit" class="btn btn-primary" id="pr_saveVariationValueBtn">Save Value</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Existing Values</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="prVariationValuesTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Value</th>
                                        <th>Unit</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="prVariationValuesTableBody">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No values loaded</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')
    <script>
        var variationValueTypesMap = @json($variationValueTypes);

        $(document).ready(function() {
            if ($('#pm_product_item_type_id').val() == '1') {
                $('.price-fields-container').addClass('show');
            }
            
            initializeVariantSelects();

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
                        error: function() {
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
            
            $('#pm_product_item_type_id').on('change', function() {
                var productTypeId = $(this).val();
                if (productTypeId == '1') {
                    $('.price-fields-container').addClass('show');
                } else {
                    $('.price-fields-container').removeClass('show');
                }
            });
            
            $('#generateProductsBtn').on('click', function() {
                var productName = $('#product_name').val();
                var productCode = $('#product_code').val();
                var productDescription = $('#product_description').val();
                var productTypeId = $('#pm_product_item_type_id').val();
                var mainCategoryId = $('#pm_product_main_category_id').val();
                var subCategoryId = $('#pm_product_sub_category_id').val();
                
                if (!productName || !productCode || !productTypeId || !mainCategoryId || !subCategoryId) {
                    swal("Error", "Please fill all required fields", "error");
                    return;
                }
                
                var selectedVariations = collectSelectedVariations();
                
                if (selectedVariations.length === 0) {
                    swal("Error", "Please select at least one variation value", "error");
                    return;
                }
                
                var combinations = generateCombinations(selectedVariations);
                displayGeneratedProducts(combinations, productName, productCode, productDescription, productTypeId, mainCategoryId, subCategoryId);
                $('#generatedProductsContainer').show();
            });
            
            $('#productRegistrationForm').on('submit', function(e) {
                e.preventDefault();
                
                var productItems = [];
                var hasError = false;
                var csrf_token = $("#csrf_token").val();

                var productPayload = {
                    pm_brands_id: $('#pm_brands_id').val() || null,
                    product_name: $('#product_name').val(),
                    product_code: $('#product_code').val(),
                    product_description: $('#product_description').val(),
                    pm_product_item_type_id: $('#pm_product_item_type_id').val(),
                    pm_product_main_category_id: $('#pm_product_main_category_id').val(),
                    pm_product_sub_category_id: $('#pm_product_sub_category_id').val()
                };
                
                $('.product-variation-row').each(function() {
                    var productName = $(this).find('.product-name').val();
                    var binCode = $(this).find('.product-code').val();
                    var sellingPrice = $(this).find('.selling-price').val();
                    var costPrice = $(this).find('.cost-price').val();
                    var combination = parseCombinationData($(this).find('.product-name'));
                    
                    if (!productName || !binCode) {
                        swal("Error", "Product item name and bin code are required for all items", "error");
                        hasError = true;
                        return false;
                    }
                    
                    sellingPrice = sellingPrice || 0;
                    costPrice = costPrice || 0;
                    
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
                    
                    productItems.push({
                        product_item_name: productName,
                        bin_code: binCode,
                        pm_product_item_variation_id: combination.length > 0 ? combination[0].variationId : null,
                        pm_product_item_variation_value_id: combination.length > 0 ? combination[0].valueId : null,
                        selling_price: sellingPrice,
                        cost_price: costPrice,
                        status: 1
                    });
                });
                
                if (hasError) {
                    return;
                }
                
                if (productItems.length === 0) {
                    swal("Error", "No product items to save", "error");
                    return;
                }
                
                $.ajax({
                    url: "{{ url('/saveProductItems') }}",
                    type: "POST",
                    data: {
                        "_token": csrf_token,
                        "product": productPayload,
                        "items": productItems
                    },
                    beforeSend: function() {
                        showLder();
                        $('#saveProductsBtn').prop('disabled', true);
                    },
                    complete: function() {
                        hideLder();
                        $('#saveProductsBtn').prop('disabled', false);
                    },
                    error: function() {
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

            $(document).on('click', '.add-variation-btn', function() {
                var variationId = $(this).data('variation-id');
                var variationName = $(this).data('variation-name');
                openProductVariationValuesModal(variationId, variationName);
            });

            $('#prVariationValueForm').on('submit', function(e) {
                e.preventDefault();
                savePrVariationValue();
            });

            $('#pr_cancelVariationValueBtn').on('click', function() {
                resetPrVariationValueForm();
            });

            $(document).on('click', '.pr-edit-value', function() {
                editPrVariationValue($(this).data('value-id'));
            });

            $(document).on('click', '.pr-toggle-value', function() {
                togglePrVariationValueStatus($(this).data('value-id'));
            });

            $(document).on('click', '.pr-delete-value', function() {
                deletePrVariationValue($(this).data('value-id'));
            });
        });

        function initializeVariantSelects() {
            $('.variation-value-select').each(function() {
                var $select = $(this);
                var placeholder = $select.data('placeholder');

                if ($.fn.select2) {
                    $select.select2({
                        placeholder: placeholder,
                        width: '100%',
                        closeOnSelect: false,
                        allowClear: true
                    });
                }

                $select.on('change', function() {
                    updateSelectedTags($select);
                });

                loadVariationOptions($select);
            });
        }

        function loadVariationOptions($select, preservedSelection) {
            var variationId = $select.data('variation-id');
            var csrf_token = $("#csrf_token").val();
            var existingSelection = preservedSelection || ($select.val() || []);
            existingSelection = existingSelection.map(String);

            $.ajax({
                url: "{{ url('/loadVariationValuesByVariation') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "variation_id": variationId
                },
                beforeSend: function() {
                    showLder();
                    $select.prop('disabled', true);
                },
                complete: function() {
                    hideLder();
                    $select.prop('disabled', false);
                },
                error: function() {
                    hideLder();
                    swal("Error", "Failed to load variation values", "error");
                },
                success: function(response) {
                    hideLder();
                    if (response.status === 'success') {
                        var optionsHtml = '<option value=""></option>';
                        if (response.data.length > 0) {
                            $.each(response.data, function(index, value) {
                                var unitName = response.types[value.pm_variation_value_type_id] || '';
                                var displayName = value.variation_value_name ? value.variation_value_name : value.variation_value;
                                if (unitName) {
                                    displayName += ' (' + unitName + ')';
                                }
                                optionsHtml += '<option value="' + value.id + '">' + displayName + '</option>';
                            });
                        }
                        $select.html(optionsHtml);

                        if (existingSelection.length > 0) {
                            var filtered = existingSelection.filter(function(val) {
                                return $select.find('option[value="' + val + '"]').length > 0;
                            });
                            if (filtered.length > 0) {
                                $select.val(filtered).trigger('change');
                            } else {
                                $select.val(null).trigger('change');
                            }
                        } else {
                            $select.val(null).trigger('change');
                        }
                        updateSelectedTags($select);
                    } else {
                        swal("Error", response.message, "error");
                    }
                }
            });
        }

        function updateSelectedTags($select) {
            var container = $select.closest('.variant-config-card').find('.selected-variation-tags');
            var selectedData = $.fn.select2 ? $select.select2('data') : $select.find('option:selected').map(function() {
                return { id: $(this).val(), text: $(this).text() };
            }).get();

            var chips = '';
            $.each(selectedData, function(_, item) {
                if (item.id) {
                    chips += '<span class="tag-chip">' + item.text + '</span>';
                }
            });

            if (!chips) {
                container.html('<span class="tag-placeholder">No values selected</span>');
            } else {
                container.html(chips);
            }
        }

        function collectSelectedVariations() {
            var selectedVariations = [];
            $('.variation-value-select').each(function() {
                var $select = $(this);
                var variationId = $select.data('variation-id');
                var variationName = $select.data('variation-name');
                var selectedValues = [];

                $select.find('option:selected').each(function() {
                    var valueId = $(this).val();
                    if (!valueId) {
                        return;
                    }
                    selectedValues.push({
                        id: valueId,
                        name: $(this).text()
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
            return selectedVariations;
        }

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

        function parseCombinationData($input) {
            var combination = $input.data('combination') || [];
            if (typeof combination === 'string') {
                try {
                    combination = JSON.parse(combination);
                } catch (error) {
                    combination = [];
                }
            }
            return Array.isArray(combination) ? combination : [];
        }

        function displayGeneratedProducts(combinations, productName, productCode, productDescription, productTypeId, mainCategoryId, subCategoryId) {
            var html = '<div class="row">';

            $.each(combinations, function(index, combination) {
                var combinationName = productName;
                var combinationCode = productCode + '-' + (index + 1);

                $.each(combination, function(i, item) {
                    combinationName += ' ' + item.valueName;
                });

                html += '<div class="col-md-12 product-variation-row">';
                html += '<div class="form-row">';
                html += '<div class="form-group col-md-4">';
                html += '<label>Product Item Name</label>';
                html += '<input readonly type="text" class="form-control product-name" value="' + combinationName + '" data-original="' + productName + '" data-combination=\'' + JSON.stringify(combination) + '\'>';
                html += '</div>';
                html += '<div class="form-group col-md-3">';
                html += '<label>Bin Code</label>';
                html += '<input readonly type="text" class="form-control product-code" value="' + combinationCode + '">';
                html += '</div>';
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

        function openProductVariationValuesModal(variationId, variationName) {
            $('#pr_current_variation_id').val(variationId);
            $('#pr_current_variation_name').val(variationName);
            $('#pr_pm_variation_id').val(variationId);
            $('#productVariationValuesModalLabel').text('Manage Values for "' + variationName + '"');
            resetPrVariationValueForm();
            loadPrVariationValues(variationId);
            $('#productVariationValuesModal').modal('show');
        }

        function resetPrVariationValueForm() {
            $('#prVariationValueForm')[0].reset();
            $('#pr_variation_value_id').val('');
            $('#pr_saveVariationValueBtn').text('Save Value');
        }

        function loadPrVariationValues(variationId) {
            var csrf_token = $("#csrf_token").val();
            $.ajax({
                url: "{{ url('/getVariationValues') }}",
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
                error: function() {
                    hideLder();
                    swal("Error", "Failed to load variation values", "error");
                },
                success: function(response) {
                    hideLder();
                    if (response.status === 'success') {
                        renderPrVariationValues(response.data);
                    } else {
                        $('#prVariationValuesTableBody').html('<tr><td colspan="5" class="text-center">No values found</td></tr>');
                    }
                }
            });
        }

        function renderPrVariationValues(values) {
            var html = '';
            if (values.length > 0) {
                $.each(values, function(index, value) {
                    var unitName = variationValueTypesMap[value.pm_variation_value_type_id] || 'N/A';
                    var statusBadge = value.is_active == 1
                        ? '<span class="badge" style="color:#28a745;background:#e2f5e6;">Active</span>'
                        : '<span class="badge" style="color:#dc3545;background:#fceff0;">Inactive</span>';

                    html += '<tr>';
                    html += '<td>' + (value.variation_value_name || 'N/A') + '</td>';
                    html += '<td>' + value.variation_value + '</td>';
                    html += '<td>' + unitName + '</td>';
                    html += '<td class="text-center">' + statusBadge + '</td>';
                    html += '<td>';
                    html += '<button type="button" class="btn btn-xs btn-outline-warning pr-edit-value" data-value-id="' + value.id + '"><i class="fa fa-pencil"></i></button> ';
                    html += '<button type="button" class="btn btn-xs btn-outline-info pr-toggle-value" data-value-id="' + value.id + '" data-status="' + value.is_active + '">' + (value.is_active == 1 ? 'Deactivate' : 'Activate') + '</button> ';
                    html += '<button type="button" class="btn btn-xs btn-outline-danger pr-delete-value" data-value-id="' + value.id + '"><i class="fa fa-trash"></i></button>';
                    html += '</td>';
                    html += '</tr>';
                });
            } else {
                html = '<tr><td colspan="5" class="text-center">No values found</td></tr>';
            }
            $('#prVariationValuesTableBody').html(html);
        }

        function savePrVariationValue() {
            var csrf_token = $("#csrf_token").val();
            var variationValueId = $('#pr_variation_value_id').val();
            var url = variationValueId ? "{{ url('/updateVariationValue') }}" : "{{ url('/saveVariationValue') }}";
            var payload = {
                "_token": csrf_token,
                "pm_variation_id": $('#pr_pm_variation_id').val(),
                "pm_variation_value_type_id": $('#pr_pm_variation_value_type_id').val(),
                "variation_value": $('#pr_variation_value').val(),
                "variation_value_name": $('#pr_variation_value_name').val()
            };

            if (variationValueId) {
                payload.variation_value_id = variationValueId;
            }

            $.ajax({
                url: url,
                type: "POST",
                data: payload,
                beforeSend: function() {
                    showLder();
                    $('#pr_saveVariationValueBtn').prop('disabled', true);
                },
                complete: function() {
                    hideLder();
                    $('#pr_saveVariationValueBtn').prop('disabled', false);
                },
                error: function() {
                    hideLder();
                    swal("Error", "Failed to save variation value", "error");
                },
                success: function(response) {
                    hideLder();
                    if (response.status === 'success') {
                        swal("Success", response.message, "success");
                        resetPrVariationValueForm();
                        var currentVariationId = $('#pr_current_variation_id').val();
                        loadPrVariationValues(currentVariationId);
                        refreshVariationSelect(currentVariationId);
                    } else {
                        swal("Error", response.message, "error");
                    }
                }
            });
        }

        function editPrVariationValue(valueId) {
            var csrf_token = $("#csrf_token").val();
            $.ajax({
                url: "{{ url('/getVariationValue') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "variation_value_id": valueId
                },
                beforeSend: function() {
                    showLder();
                },
                complete: function() {
                    hideLder();
                },
                error: function() {
                    hideLder();
                    swal("Error", "Failed to load variation value", "error");
                },
                success: function(response) {
                    hideLder();
                    if (response.status === 'success') {
                        var value = response.data;
                        $('#pr_variation_value_id').val(value.id);
                        $('#pr_variation_value_name').val(value.variation_value_name);
                        $('#pr_variation_value').val(value.variation_value);
                        $('#pr_pm_variation_value_type_id').val(value.pm_variation_value_type_id);
                        $('#pr_saveVariationValueBtn').text('Update Value');
                    } else {
                        swal("Error", response.message, "error");
                    }
                }
            });
        }

        function togglePrVariationValueStatus(valueId) {
            var csrf_token = $("#csrf_token").val();
            $.ajax({
                url: "{{ url('/toggleVariationValueStatus') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "variation_value_id": valueId
                },
                beforeSend: function() {
                    showLder();
                },
                complete: function() {
                    hideLder();
                },
                error: function() {
                    hideLder();
                    swal("Error", "Failed to toggle variation value status", "error");
                },
                success: function(response) {
                    hideLder();
                    if (response.status === 'success') {
                        swal("Success", response.message, "success");
                        var currentVariationId = $('#pr_current_variation_id').val();
                        loadPrVariationValues(currentVariationId);
                        refreshVariationSelect(currentVariationId);
                    } else {
                        swal("Error", response.message, "error");
                    }
                }
            });
        }

        function deletePrVariationValue(valueId) {
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover this variation value!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                closeOnConfirm: false,
                closeOnCancel: true
            }, function(isConfirm) {
                if (isConfirm) {
                    var csrf_token = $("#csrf_token").val();
                    $.ajax({
                        url: "{{ url('/deleteVariationValue') }}",
                        type: "POST",
                        data: {
                            "_token": csrf_token,
                            "variation_value_id": valueId
                        },
                        beforeSend: function() {
                            showLder();
                        },
                        complete: function() {
                            hideLder();
                        },
                        error: function() {
                            hideLder();
                            swal("Error", "Failed to delete variation value", "error");
                        },
                        success: function(response) {
                            hideLder();
                            if (response.status === 'success') {
                                swal("Deleted!", response.message, "success");
                                var currentVariationId = $('#pr_current_variation_id').val();
                                loadPrVariationValues(currentVariationId);
                                refreshVariationSelect(currentVariationId);
                            } else {
                                swal("Error", response.message, "error");
                            }
                        }
                    });
                }
            });
        }

        function refreshVariationSelect(variationId) {
            var $select = $('.variation-value-select[data-variation-id="' + variationId + '"]');
            if ($select.length) {
                var existingSelection = $select.val() || [];
                loadVariationOptions($select, existingSelection);
            }
        }
    </script>
@endsection