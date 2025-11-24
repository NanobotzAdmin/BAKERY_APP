@php
    $privilageId = \DB::table('pm_interfaces')
        ->select('pm_interfaces.id AS pageId', 'pm_interface_topic.id AS grupId')
        ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
        ->where('pm_interfaces.path', 'adminIngredientManagement')
        ->first();

    $rawMaterialOptionHtml = '';
    foreach ($rawMaterials as $material) {
        $label = $material->product_item_name . ' (' . ($material->bin_code ?? 'N/A') . ')';
        $rawMaterialOptionHtml .= '<option value="' . $material->id . '">' . e($label) . '</option>';
    }

    $variationValueTypeMap = $variationValueTypeMap ?? [];

@endphp

@extends('layout', ['pageId' => $privilageId->pageId, 'grupId' => $privilageId->grupId])

@section('content')
    <style>
        .ingredient-card {
            border-left: 4px solid #1ab394;
        }

        .ingredient-form .form-control {
            height: 38px;
        }

        .ingredient-form label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #6b6b6b;
        }

        .ingredient-empty {
            background: #fdfaf4;
            border: 1px dashed #e0c29a;
        }

        #productIngredientTable td {
            vertical-align: middle;
        }
        #productIngredientTable td {
            vertical-align: middle;
        }

        /* ADD THIS NEW CLASS */
        @media (min-width: 768px) {
            .modal-wide {
                max-width: 70% !important; /* Adjust this percentage as needed (e.g., 95%) */
                width: 90% !important;
            }
        }
    </style>

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2 class=" text-dark"><strong>Ingredient Management</strong></h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/admindashboard">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a>Product Management</a>
                </li>
                <li class="breadcrumb-item active text-dark">
                    <strong>Ingredients</strong>
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
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title text-dark">
                    <h5>Build Ingredients for Selling Products</h5>
                </div>
                <div class="ibox-content">
                    <div class="row mb-3">
                        <div class="col">
                            <p class="text-dark mb-0">
                                Review the selling products below and click <strong>Manage Ingredients</strong> to
                                configure the raw materials required for each item.
                            </p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="sellingProductsTable">
                            <thead class="text-dark">
                                <tr>
                                    <th style="width: 60px;" class="text-center">#</th>
                                    <th>Selling Product</th>
                                    {{-- <th>Variation</th> --}}
                                    <th>Variation Value</th>
                                    <th>Category</th>
                                    <th style="width: 180px;" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sellingProducts as $index => $product)
                                    <tr data-product-id="{{ $product->id }}">
                                        <td class="align-middle text-center font-weight-bold">{{ $index + 1 }}</td>
                                        <td class="align-middle">
                                            <div class="font-weight-bold text-dark">{{ $product->product_item_name ?? 'N/A' }}</div>
                                            <div class="text-muted small">Bin: {{ $product->bin_code ?? 'N/A' }}</div>
                                        </td>
                                        {{-- <td class="align-middle">
                                            {{ optional($product->variation)->variation_name ?? 'N/A' }}
                                        </td> --}}
                                        <td class="align-middle">
                                            @php
                                                $variationValue = $product->variationValue;
                                                $variationValueLabel = 'N/A';
                                                if ($variationValue) {
                                                    $typeLabel = 'N/A';
                                                    if (!empty($variationValue->pm_variation_value_type_id)) {
                                                        $typeId = $variationValue->pm_variation_value_type_id;
                                                        $typeLabel = $variationValueTypeMap[$typeId]['name'] ?? 'N/A';
                                                    }
                                                    $variationValueLabel = trim(($variationValue->variation_value_name ?? '') . ' ' . ($variationValue->variation_value ? '(' . $variationValue->variation_value . ' ' . $typeLabel . ')' : ''));
                                                    $variationValueLabel = $variationValueLabel ?: 'N/A';
                                                }
                                            @endphp
                                            {{ $variationValueLabel }}
                                        </td>
                                        <td class="align-middle">
                                            @php
                                                $mainCategory = optional($product->mainCategory)->main_category_name ?? 'N/A';
                                                $subCategory = optional($product->subCategory)->sub_category_name ?? 'N/A';
                                            @endphp
                                            {{ $mainCategory }} / {{ $subCategory }}
                                        </td>
                                        <td class="align-middle text-center">
                                            <button type="button"
                                                class="btn btn-xs btn-primary manage-ingredient-btn"
                                                data-product-id="{{ $product->id }}">
                                                <i class="fa fa-cutlery"></i> Manage Ingredients
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            No selling products are available. Please register selling products first.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-dark" id="ingredientModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-wide modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-0">
                            Manage Ingredients - <span id="modalProductName" class="text-primary"></span>
                        </h5>
                        <small class="text-muted" id="modalProductMeta"></small>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="modalLoadingState" class="alert alert-info d-none">
                        <i class="fa fa-spinner fa-spin mr-2"></i> Loading existing ingredients...
                    </div>
                    <div class="ingredient-form card card-body mb-3">
                        <div class="form-row">
                            <div class="form-group col-md-8">
                                <label for="modalRawMaterialSelect">Raw Material</label>
                                <select class="form-control" id="modalRawMaterialSelect">
                                    <option value="">-- Select raw material --</option>
                                    {!! $rawMaterialOptionHtml !!}
                                </select>
                            </div>
                            <div class="form-group col-md-4 d-flex align-items-end">
                                <button type="button" class="btn btn-primary btn-block" id="modalAddIngredientBtn">
                                    <i class="fa fa-plus"></i> Add
                                </button>
                            </div>
                        </div>
                        <small class="text-muted">Add raw materials and then assign variation value types and values within the table below.</small>
                    </div>
                    <div id="modalIngredientEmptyState" class="ingredient-empty text-center text-muted py-3 rounded">
                        No raw materials added yet. Use the form above to start building the ingredient list.
                    </div>
                    <div class="table-responsive" id="modalIngredientTableWrapper" style="display: none;">
                        <table class="table table-sm table-striped table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">#</th>
                                    <th>Raw Material</th>
                                    <th style="width: 160px;" class="text-center">Variation Value</th>
                                    <th style="width: 140px;">Variation Value Type</th>
                                    <th style="width: 90px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="modalIngredientTableBody"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="modalSaveIngredientsBtn" disabled>
                        <i class="fa fa-save"></i> Save Ingredients
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    <script>
        (function() {
            const sellingProducts = @json($sellingProducts);
            const rawMaterials = @json($rawMaterials);
            const variationValueTypes = @json($variationValueTypes);
            const sellingProductMap = mapById(sellingProducts);
            const rawMaterialMap = mapById(rawMaterials);
            const csrfToken = '{{ csrf_token() }}';
            const ingredientFetchBaseUrl = '{{ url('/productIngredients') }}';
            const productIngredientStore = {};
            let activeProductId = null;

            $('.manage-ingredient-btn').on('click', function() {
                const productId = $(this).data('productId');
                openIngredientModal(productId);
            });

            $('#modalAddIngredientBtn').on('click', function() {
                addIngredientToState();
            });

            $('#modalSaveIngredientsBtn').on('click', function() {
                saveActiveProductIngredients();
            });

            $(document).on('click', '.modal-remove-ingredient', function() {
                const rawMaterialId = $(this).data('rawMaterialId');
                removeIngredientFromState(rawMaterialId);
            });

            $(document).on('change', '.variation-type-select', function() {
                const rawMaterialId = $(this).data('rawMaterialId');
                const value = $(this).val();
                updateIngredientField(rawMaterialId, 'variation_value_type_id', value);
            });

            $(document).on('input', '.variation-value-input', function() {
                const rawMaterialId = $(this).data('rawMaterialId');
                const value = $(this).val();
                updateIngredientField(rawMaterialId, 'variation_value', value);
            });

            $('#ingredientModal').on('hidden.bs.modal', function() {
                resetModalForm();
                toggleModalLoading(false);
                $('#modalIngredientTableBody').empty();
                $('#modalIngredientTableWrapper').hide();
                $('#modalIngredientEmptyState').show();
                $('#modalSaveIngredientsBtn').prop('disabled', true).html('<i class="fa fa-save"></i> Save Ingredients');
            });

            function openIngredientModal(productId) {
                const product = sellingProductMap[productId];

                if (!product) {
                    swal('Error', 'Unable to find the selected selling product.', 'error');
                    return;
                }

                activeProductId = productId;

                if (!productIngredientStore[productId]) {
                    productIngredientStore[productId] = {
                        product: product,
                        ingredients: [],
                        loadedFromServer: false
                    };
                }

                $('#modalProductName').text(product.product_item_name || 'N/A');
                $('#modalProductMeta').text([
                    `Bin: ${product.bin_code || 'N/A'}`,
                    `Category: ${getCategoryLabel(product.main_category, product.sub_category)}`,
                    `Variation: ${getVariationName(product.variation)} | ${getVariationValueText(product.variation_value)}`
                ].join(' • '));

                resetModalForm();
                renderModalIngredients();
                $('#ingredientModal').modal('show');

                if (!productIngredientStore[productId].loadedFromServer) {
                    fetchProductIngredients(productId);
                }
            }

            function fetchProductIngredients(productId) {
                toggleModalLoading(true);
                $.ajax({
                    url: `${ingredientFetchBaseUrl}/${productId}`,
                    method: 'GET',
                    success: function(response) {
                        if (response.status === 'success' && Array.isArray(response.ingredients)) {
                            productIngredientStore[productId].ingredients = response.ingredients.map(item => {
                                const rawMaterial = rawMaterialMap[item.raw_material_id] || item.raw_material;
                                if (rawMaterial && !rawMaterialMap[item.raw_material_id]) {
                                    rawMaterialMap[item.raw_material_id] = rawMaterial;
                                }
                                return {
                                    raw_material_id: item.raw_material_id,
                                    raw_material: rawMaterial,
                                    variation_value_type_id: item.variation_value_type_id || '',
                                    variation_value: item.variation_value || ''
                                };
                            });
                        }
                        productIngredientStore[productId].loadedFromServer = true;
                    },
                    error: function(xhr) {
                        let message = 'Unable to load existing ingredients.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        swal('Error', message, 'error');
                    },
                    complete: function() {
                        toggleModalLoading(false);
                        renderModalIngredients();
                    }
                });
            }

            function addIngredientToState() {
                if (!activeProductId) {
                    swal('Error', 'Please select a selling product first.', 'error');
                    return;
                }

                const rawMaterialId = $('#modalRawMaterialSelect').val();

                if (!rawMaterialId) {
                    swal('', 'Please select a raw material.', 'warning');
                    return;
                }

                const rawMaterial = rawMaterialMap[rawMaterialId];

                if (!rawMaterial) {
                    swal('Error', 'Unable to load raw material details.', 'error');
                    return;
                }

                if (!rawMaterial.pm_product_item_variation_id || !rawMaterial.pm_product_item_variation_value_id) {
                    swal('Incomplete Data', 'The selected raw material does not have variation details. Please update it first.', 'warning');
                    return;
                }

                const productData = productIngredientStore[activeProductId];
                const duplicate = productData.ingredients.find(ing => String(ing.raw_material_id) === String(rawMaterialId));

                if (duplicate) {
                    swal('', 'This raw material is already added to the product.', 'warning');
                    return;
                }

                productData.ingredients.push({
                    raw_material_id: rawMaterial.id,
                    raw_material: rawMaterial,
                    variation_value_type_id: '',
                    variation_value: ''
                });

                resetModalForm();
                renderModalIngredients();
            }

            function removeIngredientFromState(rawMaterialId) {
                if (!activeProductId || !rawMaterialId) {
                    return;
                }
                const productData = productIngredientStore[activeProductId];
                productData.ingredients = productData.ingredients.filter(ing => String(ing.raw_material_id) !== String(rawMaterialId));
                renderModalIngredients();
            }

            function saveActiveProductIngredients() {
                if (!activeProductId) {
                    swal('Error', 'Please select a selling product first.', 'error');
                    return;
                }

                const productData = productIngredientStore[activeProductId];

                if (!productData.ingredients.length) {
                    swal('', 'Please add at least one raw material before saving.', 'warning');
                    return;
                }

                const missingTypes = productData.ingredients.some(ingredient => !ingredient.variation_value_type_id);
                if (missingTypes) {
                    swal('', 'Please select a variation value type for every raw material.', 'warning');
                    return;
                }

                const invalidValues = productData.ingredients.some(ingredient => {
                    const value = parseFloat(ingredient.variation_value);
                    return !value || value <= 0;
                });
                if (invalidValues) {
                    swal('', 'Please enter a valid variation value (> 0) for every raw material.', 'warning');
                    return;
                }

                const payload = [{
                    product_id: activeProductId,
                    ingredients: productData.ingredients.map(ingredient => ({
                        raw_material_id: ingredient.raw_material_id,
                        variation_value_type_id: ingredient.variation_value_type_id,
                        variation_value: ingredient.variation_value
                    }))
                }];

                const button = $('#modalSaveIngredientsBtn');
                button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

                $.ajax({
                    url: '{{ url('/saveProductIngredients') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: {
                        products: payload
                    },
                    success: function(response) {
                        swal('Success', response.message || 'Ingredients saved successfully.', 'success');
                        $('#ingredientModal').modal('hide');
                    },
                    error: function(xhr) {
                        let message = 'Failed to save product ingredients.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        swal('Error', message, 'error');
                    },
                    complete: function() {
                        button.prop('disabled', false).html('<i class="fa fa-save"></i> Save Ingredients');
                    }
                });
            }

            function renderModalIngredients() {
                if (!activeProductId) {
                    return;
                }

                const productData = productIngredientStore[activeProductId];
                const ingredients = productData ? productData.ingredients : [];

                if (!ingredients.length) {
                    $('#modalIngredientTableWrapper').hide();
                    $('#modalIngredientEmptyState').show();
                    $('#modalSaveIngredientsBtn').prop('disabled', true);
                    return;
                }

                const rows = ingredients.map((ingredient, index) => {
                    const material = ingredient.raw_material || rawMaterialMap[ingredient.raw_material_id] || {};
                    const typeOptions = buildVariationTypeOptions(ingredient.variation_value_type_id);
                    const valueInput = formatVariationValueInput(ingredient.variation_value);
                    return `
                        <tr>
                            <td class="align-middle text-center">${index + 1}</td>
                            <td>
                                <div class="font-weight-bold">${escapeHtml(material.product_item_name || 'N/A')}</div>
                                <div class="text-muted small">Bin: ${escapeHtml(material.bin_code || 'N/A')}</div>
                            </td>
                            <td class="align-middle">
                                <input type="number"
                                       class="form-control form-control-sm variation-value-input text-right"
                                       data-raw-material-id="${material.id}"
                                       min="0.01"
                                       step="0.01"
                                       placeholder="0.00"
                                       value="${valueInput}">
                            </td>
                            <td class="align-middle">
                                <select class="form-control form-control-sm variation-type-select" data-raw-material-id="${material.id}">
                                    ${typeOptions}
                                </select>
                            </td>
                            <td class="align-middle text-center">
                                <button type="button" class="btn btn-xs btn-danger modal-remove-ingredient" data-raw-material-id="${material.id}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                }).join('');

                $('#modalIngredientTableBody').html(rows);
                $('#modalIngredientTableWrapper').show();
                $('#modalIngredientEmptyState').hide();
                $('#modalSaveIngredientsBtn').prop('disabled', false);
            }

            function resetModalForm() {
                $('#modalRawMaterialSelect').val('');
            }

            function toggleModalLoading(isLoading) {
                $('#modalLoadingState').toggleClass('d-none', !isLoading);
                $('#modalAddIngredientBtn').prop('disabled', isLoading);
                $('#modalRawMaterialSelect').prop('disabled', isLoading);
            }

            function updateIngredientField(rawMaterialId, key, value) {
                if (!activeProductId) {
                    return;
                }

                const productData = productIngredientStore[activeProductId];
                if (!productData) {
                    return;
                }

                const ingredient = productData.ingredients.find(ing => String(ing.raw_material_id) === String(rawMaterialId));
                if (!ingredient) {
                    return;
                }

                ingredient[key] = value;
            }

            function buildVariationTypeOptions(selectedId) {
                const selected = selectedId !== null && selectedId !== undefined ? String(selectedId) : '';
                const baseOption = '<option value="">-- Select type --</option>';
                const options = variationValueTypes.map(type => {
                    const isSelected = selected && String(type.id) === selected ? 'selected' : '';
                    return `<option value="${type.id}" ${isSelected}>${escapeHtml(type.name || '')}</option>`;
                }).join('');
                return baseOption + options;
            }

            function formatVariationValueInput(value) {
                if (value === null || value === undefined || value === '') {
                    return '';
                }
                const numeric = parseFloat(value);
                if (!numeric) {
                    return '';
                }
                return numeric;
            }

            function mapById(items) {
                return (items || []).reduce((acc, item) => {
                    acc[item.id] = item;
                    return acc;
                }, {});
            }

            function getCategoryLabel(mainCategory, subCategory) {
                const main = mainCategory && mainCategory.main_category_name ? mainCategory.main_category_name : 'N/A';
                const sub = subCategory && subCategory.sub_category_name ? subCategory.sub_category_name : 'N/A';
                return `${main} / ${sub}`;
            }

            function getVariationName(variation) {
                return variation && variation.variation_name ? variation.variation_name : 'N/A';
            }

            function getVariationValueText(variationValue) {
                if (!variationValue) {
                    return 'N/A';
                }
                if (variationValue.variation_value_name && variationValue.variation_value) {
                    return `${variationValue.variation_value_name} (${variationValue.variation_value})`;
                }
                return variationValue.variation_value_name || variationValue.variation_value || 'N/A';
            }

            function escapeHtml(text) {
                if (text === null || text === undefined) {
                    return '';
                }
                return $('<div>').text(text).html();
            }
        })();
    </script>
@endsection

