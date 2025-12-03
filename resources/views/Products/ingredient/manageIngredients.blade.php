@php
    $privilageId = \DB::table('pm_interfaces')
        ->select('pm_interfaces.id AS pageId', 'pm_interface_topic.id AS grupId')
        ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
        ->where('pm_interfaces.path', 'adminIngredientManagement')
        ->first();

    $rawMaterialOptionHtml = '';
    foreach ($rawMaterials as $material) {
        $productName = $material->product_name ?? 'N/A';
        $productCode = $material->product_code ?? 'N/A';
        $label = $productName . ' (' . $productCode . ')';
        $rawMaterialOptionHtml .= '<option value="' . $material->id . '" data-type="raw">' . e($label) . '</option>';
    }

    $semiFinishedOptionHtml = '';
    foreach ($semiFinishedIngredients as $sfIngredient) {
        $productName = $sfIngredient->product_name ?? 'N/A';
        $productCode = $sfIngredient->product_code ?? 'N/A';
        $label = $productName . ' (' . $productCode . ')';
        $semiFinishedOptionHtml .= '<option value="' . $sfIngredient->id . '" data-type="semi">' . e($label) . '</option>';
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
                max-width: 70% !important;
                /* Adjust this percentage as needed (e.g., 95%) */
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
                    <h5>Manage Ingredients</h5>
                </div>
                <div class="ibox-content">
                    <div class="row mb-3">
                        <div class="col">
                            <p class="text-dark mb-0">
                                Select a product type tab below and click <strong>Manage Ingredients</strong> to configure
                                the required ingredients.
                            </p>
                        </div>
                    </div>

                    <ul class="nav nav-tabs" id="productTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="selling-tab" data-toggle="tab" href="#selling" role="tab"
                                aria-controls="selling" aria-selected="true">Selling Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="semi-tab" data-toggle="tab" href="#semi" role="tab" aria-controls="semi"
                                aria-selected="false">Semi-Finished Products</a>
                        </li>
                    </ul>

                    <div class="tab-content mt-3" id="productTabsContent">
                        <!-- Selling Products Tab -->
                        <div class="tab-pane fade show active" id="selling" role="tabpanel" aria-labelledby="selling-tab">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" id="sellingProductsTable">
                                    <thead class="text-dark">
                                        <tr>
                                            <th style="width: 60px;" class="text-center">#</th>
                                            <th>Selling Product</th>
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
                                                    <div class="font-weight-bold text-dark">
                                                        {{ $product->product_item_name ?? 'N/A' }}</div>
                                                    <div class="text-muted small">Bin: {{ $product->bin_code ?? 'N/A' }}</div>
                                                </td>
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
                                                    <button type="button" class="btn btn-xs btn-primary manage-ingredient-btn"
                                                        data-product-id="{{ $product->id }}" data-product-type="selling">
                                                        <i class="fa fa-cutlery"></i> Manage Ingredients
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">
                                                    No selling products are available.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Semi-Finished Products Tab -->
                        <div class="tab-pane fade" id="semi" role="tabpanel" aria-labelledby="semi-tab">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" id="semiFinishedProductsTable">
                                    <thead class="text-dark">
                                        <tr>
                                            <th style="width: 60px;" class="text-center">#</th>
                                            <th>Semi-Finished Product</th>
                                            <th>Variation Value</th>
                                            <th>Category</th>
                                            <th style="width: 180px;" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($semiFinishedProducts as $index => $product)
                                            <tr data-product-id="{{ $product->id }}">
                                                <td class="align-middle text-center font-weight-bold">{{ $index + 1 }}</td>
                                                <td class="align-middle">
                                                    <div class="font-weight-bold text-dark">
                                                        {{ $product->product_item_name ?? 'N/A' }}</div>
                                                    <div class="text-muted small">Bin: {{ $product->bin_code ?? 'N/A' }}</div>
                                                </td>
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
                                                    <button type="button" class="btn btn-xs btn-primary manage-ingredient-btn"
                                                        data-product-id="{{ $product->id }}" data-product-type="semi">
                                                        <i class="fa fa-cutlery"></i> Manage Ingredients
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">
                                                    No semi-finished products are available.
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
                                    <option value="">-- Select ingredient --</option>
                                    <optgroup label="Raw Materials">
                                        {!! $rawMaterialOptionHtml !!}
                                    </optgroup>
                                    <optgroup label="Semi-Finished Products" id="semiFinishedOptGroup">
                                        {!! $semiFinishedOptionHtml !!}
                                    </optgroup>
                                </select>
                            </div>
                            <div class="form-group col-md-4 d-flex align-items-end">
                                <button type="button" class="btn btn-primary btn-block" id="modalAddIngredientBtn">
                                    <i class="fa fa-plus"></i> Add
                                </button>
                            </div>
                        </div>
                        <small class="text-muted">Add raw materials and then assign variation value types and values within
                            the table below.</small>
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
        (function () {
            const sellingProducts = @json($sellingProducts);
            const semiFinishedProducts = @json($semiFinishedProducts);
            const rawMaterials = @json($rawMaterials);
            const semiFinishedIngredients = @json($semiFinishedIngredients);
            const variationValueTypes = @json($variationValueTypes);

            const sellingProductMap = mapByProductId(sellingProducts);
            const semiFinishedProductMap = mapByProductId(semiFinishedProducts);

            // Combine maps for easy lookup
            const allProductMap = { ...sellingProductMap, ...semiFinishedProductMap };

            const rawMaterialMap = mapById(rawMaterials);
            const semiFinishedIngredientMap = mapById(semiFinishedIngredients);

            // Combined ingredient map
            const allIngredientMap = { ...rawMaterialMap, ...semiFinishedIngredientMap };

            const csrfToken = '{{ csrf_token() }}';
            const ingredientFetchBaseUrl = '{{ url('/productIngredients') }}';
            const productIngredientStore = {};
            const $ingredientModal = $('#ingredientModal');
            const $rawMaterialSelect = $('#modalRawMaterialSelect');
            let activeProductId = null;
            let activeProductType = null;

            $('.manage-ingredient-btn').on('click', function () {
                const productId = $(this).data('productId');
                const productType = $(this).data('productType'); // 'selling' or 'semi'
                openIngredientModal(productId, productType);
            });

            $('#modalAddIngredientBtn').on('click', function () {
                addIngredientToState();
            });

            $('#modalSaveIngredientsBtn').on('click', function () {
                saveActiveProductIngredients();
            });

            $(document).on('click', '.modal-remove-ingredient', function () {
                const rawMaterialId = $(this).data('rawMaterialId');
                removeIngredientFromState(rawMaterialId);
            });

            $(document).on('change', '.variation-type-select', function () {
                const rawMaterialId = $(this).data('rawMaterialId');
                const value = $(this).val();
                updateIngredientField(rawMaterialId, 'variation_value_type_id', value);
            });

            $(document).on('input', '.variation-value-input', function () {
                const rawMaterialId = $(this).data('rawMaterialId');
                const value = $(this).val();
                updateIngredientField(rawMaterialId, 'variation_value', value);
            });

            $('#ingredientModal').on('hidden.bs.modal', function () {
                resetModalForm();
                toggleModalLoading(false);
                $('#modalIngredientTableBody').empty();
                $('#modalIngredientTableWrapper').hide();
                $('#modalIngredientEmptyState').show();
                $('#modalSaveIngredientsBtn').prop('disabled', true).html('<i class="fa fa-save"></i> Save Ingredients');
                destroySelect2Instances();
            });
            $('#ingredientModal').on('shown.bs.modal', function () {
                initializeRawMaterialSelect(true);
                applySelect2ToVariationSelects(true);
            });

            function openIngredientModal(productId, productType) {
                const product = allProductMap[productId];

                if (!product) {
                    swal('Error', 'Unable to find the selected selling product.', 'error');
                    return;
                }

                activeProductId = productId;
                activeProductType = productType;

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
                    success: function (response) {
                        if (response.status === 'success' && Array.isArray(response.ingredients)) {
                            productIngredientStore[productId].ingredients = response.ingredients.map(item => {
                                const rawMaterial = allIngredientMap[item.raw_material_id] || item.raw_material;
                                if (rawMaterial && !allIngredientMap[item.raw_material_id]) {
                                    allIngredientMap[item.raw_material_id] = rawMaterial;
                                }
                                const allowedTypeIds = getAvailableVariationTypeIds(rawMaterial);
                                return {
                                    raw_material_id: item.raw_material_id,
                                    raw_material: rawMaterial,
                                    variation_value_type_id: item.variation_value_type_id || '',
                                    variation_value: item.variation_value || '',
                                    allowed_variation_type_ids: allowedTypeIds
                                };
                            });
                        }
                        productIngredientStore[productId].loadedFromServer = true;
                    },
                    error: function (xhr) {
                        let message = 'Unable to load existing ingredients.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        swal('Error', message, 'error');
                    },
                    complete: function () {
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

                const rawMaterial = allIngredientMap[rawMaterialId];

                if (!rawMaterial) {
                    swal('Error', 'Unable to load raw material details.', 'error');
                    return;
                }

                const allowedTypeIds = getAvailableVariationTypeIds(rawMaterial);
                if (!allowedTypeIds.length) {
                    swal('Incomplete Data', 'The selected raw material does not have variation type information. Please update its items first.', 'warning');
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
                    variation_value_type_id: allowedTypeIds.length === 1 ? allowedTypeIds[0] : '',
                    variation_value: '',
                    allowed_variation_type_ids: allowedTypeIds
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
                console.log(productData);

                if (!productData.ingredients.length) {
                    swal('', 'Please add at least one raw material before saving.', 'warning');
                    return;
                }

                const missingTypes = productData.ingredients.some(ingredient => !ingredient.variation_value_type_id);
                if (missingTypes) {
                    swal('', 'Please select a variation value type for every raw material.', 'warning');
                    return;
                }

                const invalidTypeSelections = productData.ingredients.some(ingredient => {
                    const allowed = ingredient.allowed_variation_type_ids || getAvailableVariationTypeIds(ingredient.raw_material || allIngredientMap[ingredient.raw_material_id]);
                    if (!allowed || !allowed.length) {
                        return true;
                    }
                    return !allowed.map(id => String(id)).includes(String(ingredient.variation_value_type_id));
                });
                if (invalidTypeSelections) {
                    swal('', 'One or more raw materials have invalid variation value types selected.', 'warning');
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
                    product_item_id: activeProductId,
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
                    success: function (response) {
                        swal('Success', response.message || 'Ingredients saved successfully.', 'success');
                        $('#ingredientModal').modal('hide');
                    },
                    error: function (xhr) {
                        let message = 'Failed to save product ingredients.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        swal('Error', message, 'error');
                    },
                    complete: function () {
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
                    const material = ingredient.raw_material || allIngredientMap[ingredient.raw_material_id] || {};
                    const allowedTypeIds = ingredient.allowed_variation_type_ids || getAvailableVariationTypeIds(material);
                    const typeOptions = buildVariationTypeOptions(ingredient.variation_value_type_id, allowedTypeIds);
                    const valueInput = formatVariationValueInput(ingredient.variation_value);
                    return `
                            <tr>
                                <td class="align-middle text-center">${index + 1}</td>
                                <td>
                                    <div class="font-weight-bold">${escapeHtml(material.product_name || 'N/A')}</div>
                                    <div class="text-muted small">Code: ${escapeHtml(material.product_code || 'N/A')}</div>
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
                applySelect2ToVariationSelects(true);
            }

            function resetModalForm() {
                if ($rawMaterialSelect.data('select2')) {
                    $rawMaterialSelect.val(null).trigger('change.select2');
                } else {
                    $rawMaterialSelect.val('');
                }
            }

            function toggleModalLoading(isLoading) {
                $('#modalLoadingState').toggleClass('d-none', !isLoading);
                $('#modalAddIngredientBtn').prop('disabled', isLoading);
                $rawMaterialSelect.prop('disabled', isLoading);
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

                if (key === 'variation_value_type_id') {
                    const allowed = ingredient.allowed_variation_type_ids || getAvailableVariationTypeIds(ingredient.raw_material || allIngredientMap[rawMaterialId]);
                    if (!allowed || !allowed.length || !allowed.map(id => String(id)).includes(String(value))) {
                        swal('', 'Selected variation value type is not available for this raw material.', 'warning');
                        return;
                    }
                }

                ingredient[key] = value;
            }

            function buildVariationTypeOptions(selectedId, allowedTypeIds = null) {
                const selected = selectedId !== null && selectedId !== undefined ? String(selectedId) : '';
                const baseOption = '<option value="">-- Select type --</option>';
                const allowed = allowedTypeIds && allowedTypeIds.length
                    ? variationValueTypes.filter(type => allowedTypeIds.map(id => String(id)).includes(String(type.id)))
                    : variationValueTypes;
                const options = allowed.map(type => {
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

            function mapByProductId(items) {
                return (items || []).reduce((acc, item) => {
                    acc[item.id] = item;
                    return acc;
                }, {});
            }

            function getAvailableVariationTypeIds(rawMaterial) {
                if (!rawMaterial) {
                    return [];
                }

                // If it's a semi-finished product (used as ingredient), allow all variation types
                if (semiFinishedIngredientMap[rawMaterial.id]) {
                    return variationValueTypes.map(type => parseInt(type.id, 10));
                }

                if (Array.isArray(rawMaterial.available_variation_type_ids) && rawMaterial.available_variation_type_ids.length) {
                    return rawMaterial.available_variation_type_ids;
                }

                if (!Array.isArray(rawMaterial.items)) {
                    return [];
                }

                const typeSet = {};
                rawMaterial.items.forEach(item => {
                    const variation = item.variation || {};
                    const variationValues = variation.variation_values || variation.variationValues || [];
                    variationValues.forEach(value => {
                        const typeId = parseInt(value.pm_variation_value_type_id, 10);
                        if (!Number.isNaN(typeId)) {
                            typeSet[typeId] = true;
                        }
                    });

                    const variationValue = item.variation_value || item.variationValue;
                    if (variationValue && variationValue.pm_variation_value_type_id) {
                        const typeId = parseInt(variationValue.pm_variation_value_type_id, 10);
                        if (!Number.isNaN(typeId)) {
                            typeSet[typeId] = true;
                        }
                    }
                });
                return Object.keys(typeSet)
                    .map(id => parseInt(id, 10))
                    .filter(id => !Number.isNaN(id));
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

            function initializeRawMaterialSelect(forceRefresh = false) {
                if (!$rawMaterialSelect.length || !$.fn.select2) {
                    return;
                }
                if (forceRefresh && $rawMaterialSelect.data('select2')) {
                    $rawMaterialSelect.select2('destroy');
                }
                if (!$rawMaterialSelect.data('select2')) {
                    $rawMaterialSelect.select2({
                        placeholder: '-- Select ingredient --',
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $ingredientModal
                    });
                }

                // Filter options based on activeProductType
                // If 'semi', hide Semi-Finished Products optgroup
                // If 'selling', show everything
                if (activeProductType === 'semi') {
                    $rawMaterialSelect.find('optgroup[label="Semi-Finished Products"]').prop('disabled', true);
                    $rawMaterialSelect.find('option[data-type="semi"]').prop('disabled', true);
                } else {
                    $rawMaterialSelect.find('optgroup[label="Semi-Finished Products"]').prop('disabled', false);
                    $rawMaterialSelect.find('option[data-type="semi"]').prop('disabled', false);
                }

                // Re-initialize select2 to reflect disabled state if needed, or just trigger change
                // Select2 should pick up disabled attributes automatically on init/open, but sometimes needs refresh
                // Since we are inside 'shown.bs.modal', select2 might be already initialized.
                // If we just initialized it above, it picked up the disabled state? No, we set disabled AFTER init.
                // So we might need to destroy and re-init if we want to be 100% sure, or just rely on Select2 observing DOM.
                // Better approach: Destroy and Re-init always to be safe with dynamic options.
                if ($rawMaterialSelect.data('select2')) {
                    $rawMaterialSelect.select2('destroy');
                    $rawMaterialSelect.select2({
                        placeholder: '-- Select ingredient --',
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $ingredientModal
                    });
                }
            }

            function destroySelect2Instances() {
                if ($rawMaterialSelect.length && $rawMaterialSelect.data('select2')) {
                    $rawMaterialSelect.select2('destroy');
                }
                $('#modalIngredientTableBody .variation-type-select').each(function () {
                    const $select = $(this);
                    if ($select.data('select2')) {
                        $select.select2('destroy');
                    }
                });
            }

            function applySelect2ToVariationSelects(forceRefresh = false) {
                if (!$.fn.select2) {
                    return;
                }
                $('#modalIngredientTableBody .variation-type-select').each(function () {
                    const $select = $(this);
                    if (forceRefresh && $select.data('select2')) {
                        $select.select2('destroy');
                    }
                    if (!$select.data('select2')) {
                        $select.select2({
                            placeholder: '-- Select type --',
                            width: '100%',
                            dropdownParent: $ingredientModal
                        });
                    }
                });
            }
        })();
    </script>
@endsection