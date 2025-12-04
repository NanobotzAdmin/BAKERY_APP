@extends('layout', ['pageId' => 'recipeManagement', 'grupId' => 'products'])

@section('content')
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">

    <style>
        /* Custom Overrides */
        :root {
            --primary-orange: #f59e0b;
            --dark-orange: #d97706;
            --text-dark: #1f2937;
        }

        body {
            background-color: #f3f4f6;
            font-family: 'Inter', sans-serif;
        }

        /* --- Bootstrap 4 Gap Polyfill (Since BS4 lacks flex-gap) --- */
        .gap-1 {
            gap: 0.25rem;
        }

        .gap-2 {
            gap: 0.5rem;
        }

        .gap-3 {
            gap: 1rem;
        }

        .d-flex {
            display: flex;
        }

        /* Ensure flex is explicit for gap to work in some polyfills, though raw CSS supports it in modern browsers */

        /* Soften Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s;
        }

        .card-hover:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        /* Header Icon */
        .header-icon-box {
            width: 48px;
            height: 48px;
            background-color: var(--primary-orange);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        /* Stats Icons */
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        /* Custom Colors */
        .bg-light-blue {
            background-color: #e0f2fe;
            color: #0284c7;
        }

        .bg-light-green {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .bg-light-orange {
            background-color: #ffedd5;
            color: #ea580c;
        }

        .bg-light-purple {
            background-color: #f3e8ff;
            color: #9333ea;
        }

        /* Recipe Card Placeholder */
        .img-placeholder {
            background-color: #f3f4f6;
            height: 140px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            font-size: 40px;
        }

        /* Tags */
        .badge-soft-pink {
            background-color: #fce7f3;
            color: #db2777;
        }

        .badge-soft-orange {
            background-color: #ffedd5;
            color: #ea580c;
        }

        /* Upload Area */
        .upload-area {
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            background-color: #f9fafb;
            transition: all 0.2s;
        }

        .upload-area:hover {
            border-color: var(--primary-orange);
            background-color: #fffbeb;
        }

        /* Modal Sections */
        .section-box {
            padding: 15px;
            border-radius: 8px;
            border: 1px solid transparent;
        }

        .section-blue {
            background-color: #eff6ff;
            border-color: #dbeafe;
        }

        .section-blue .title {
            color: #1d4ed8;
        }

        .section-yellow {
            background-color: #fffbeb;
            border-color: #fef3c7;
        }

        .section-yellow .title {
            color: #b45309;
        }

        .section-purple {
            background-color: #f3e8ff;
            border-color: #e9d5ff;
        }

        .section-purple .title {
            color: #7e22ce;
        }

        /* Step Card */
        .step-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
        }

        /* Select2 Fixes for BS4 */
        .select2-container .select2-selection--single {
            height: 38px !important;
            border: 1px solid #ced4da;
            /* BS4 default border color */
        }

        .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
        }

        /* Font weight helpers missing in BS4 sometimes */
        .fw-bold {
            font-weight: 700 !important;
        }

        .fs-6 {
            font-size: 1rem;
        }

        /* Custom Super Wide Modal */
        @media (min-width: 992px) {
            .modal-xxl {
                max-width: 70% !important;
                width: 70% !important;
            }
        }

        /* Make the modal body scrollable so the footer stays fixed at bottom */
        .modal-body-scrollable {
            max-height: 75vh;
            overflow-y: auto;
        }
    </style>

    <div class="container-fluid py-4">

        <div class="card mb-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="header-icon-box shadow-sm">
                        <i class="fa fa-book"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="mb-0 fw-bold text-dark">Recipe Management</h4>
                        <p class="mb-0 text-muted small">Create and manage recipes with multi-level bill of materials</p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge badge-danger badge-pill px-3 py-2 mr-3">Coming Soon</span>
                    <button class="btn btn-warning text-white fw-bold d-flex align-items-center" data-toggle="modal"
                        data-target="#createRecipeModal">
                        <i class="fa fa-plus mr-2"></i> Create Recipe
                    </button>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12 col-md-6 col-xl-3 mb-3">
                <div class="card h-100 p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="text-muted small fw-bold">Total Recipes</span>
                            <h2 class="fw-bold text-dark mt-1 mb-0">127</h2>
                        </div>
                        <div class="stat-icon bg-light-blue">
                            <i class="fa fa-book"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3 mb-3">
                <div class="card h-100 p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="text-muted small fw-bold">Active Recipes</span>
                            <h2 class="fw-bold text-dark mt-1 mb-0">98</h2>
                        </div>
                        <div class="stat-icon bg-light-green">
                            <i class="fa fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3 mb-3">
                <div class="card h-100 p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="text-muted small fw-bold">Draft Recipes</span>
                            <h2 class="fw-bold text-dark mt-1 mb-0">24</h2>
                        </div>
                        <div class="stat-icon bg-light-orange">
                            <i class="fa fa-pencil-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3 mb-3">
                <div class="card h-100 p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="text-muted small fw-bold">Average Cost</span>
                            <h2 class="fw-bold text-dark mt-1 mb-0">Rs. 145</h2>
                        </div>
                        <div class="stat-icon bg-light-purple">
                            <i class="fa fa-calculator"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 col-xl-9">
                <div class="card p-3 mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-4 mb-2 mb-md-0">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white border-right-0"><i
                                            class="fa fa-search text-muted"></i></span>
                                </div>
                                <input type="text" class="form-control border-left-0" placeholder="Search recipes...">
                            </div>
                        </div>
                        <div class="col-md-2 mb-2 mb-md-0">
                            <select class="custom-select text-muted">
                                <option>Categories</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2 mb-md-0">
                            <select class="custom-select text-muted">
                                <option>Status</option>
                            </select>
                        </div>
                        <div class="col-md-4 text-md-right">
                            <select class="custom-select d-inline-block w-auto text-muted">
                                <option>Sort by: Newest</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    @foreach($recipe as $item)
                        <div class="col-md-6 col-xl-4 mb-3">
                            <div class="card card-hover h-100 p-3">
                                <div class="img-placeholder mb-3" style="height: 150px; overflow: hidden; border-radius: 8px;">
                                    @if($item->image)
                                        <img src="{{ asset($item->image) }}" alt="{{ $item->recipe_name }}"
                                            style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center h-100 bg-light text-muted">
                                            <i class="fa fa-image fa-2x"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-bold text-dark mb-0">{{ $item->recipe_name }}</h6>
                                    <span
                                        class="badge badge-soft-pink badge-pill">{{ $item->productItem->mainCategory->product_main_category_name ?? 'N/A' }}</span>
                                </div>

                                <div class="d-flex justify-content-between border-top border-bottom py-2 my-2 small text-muted">
                                    <div class="d-flex flex-column text-center">
                                        <i class="fa fa-cube mb-1"></i>
                                        @php
                                            $unit = collect($variationValueTypes)->firstWhere('id', $item->pm_variation_value_type_id);
                                        @endphp
                                        <span>{{ $item->yield }} {{ $unit['name'] ?? 'Units' }}</span>
                                    </div>
                                    <div class="d-flex flex-column text-center">
                                        <i class="fa fa-clock mb-1"></i>
                                        <span>{{ $item->steps->count() }} Steps</span>
                                    </div>
                                    <div class="d-flex flex-column text-center">
                                        <i class="fa fa-tag mb-1"></i>
                                        <span>Rs. {{ number_format($item->productItem->selling_price ?? 0, 2) }}</span>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-success fw-bold"><i class="fa fa-circle small mr-1"></i> Active</small>
                                    <div class="text-muted">
                                        <button class="btn btn-sm btn-link text-muted p-0 mr-2"
                                            onclick='showRecipeDetails(@json($item))'><i class="fa fa-eye"></i></button>
                                        <button class="btn btn-sm btn-link text-muted p-0 mr-2"><i
                                                class="fa fa-copy"></i></button>
                                        <button class="btn btn-sm btn-link text-muted p-0"><i
                                                class="fa fa-ellipsis-v"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-lg-4 col-xl-3">
                <div class="card p-3">
                    <h6 class="fw-bold mb-4">Recipe Categories</h6>

                    <div style="position: relative; height: 250px; width: 100%;">
                        <canvas id="categoryChart"></canvas>
                    </div>

                    <div class="mt-3">
                        <div class="d-flex justify-content-between small mb-2">
                            <span><i class="fa fa-circle mr-2" style="color: #f59e0b;"></i> Bread</span>
                            <span class="fw-bold">35%</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-2">
                            <span><i class="fa fa-circle mr-2" style="color: #ec4899;"></i> Pastry</span>
                            <span class="fw-bold">25%</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-2">
                            <span><i class="fa fa-circle mr-2" style="color: #8b5cf6;"></i> Cakes</span>
                            <span class="fw-bold">30%</span>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span><i class="fa fa-circle mr-2" style="color: #6b7280;"></i> Others</span>
                            <span class="fw-bold">10%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createRecipeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xxl" role="document">
            <div class="modal-content border-0 rounded-lg">
                <form id="createRecipeForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <div class="d-flex align-items-start gap-3">
                            <div class="bg-warning text-white rounded p-2 d-flex align-items-center justify-content-center mr-3"
                                style="width: 40px; height: 40px;">
                                <i class="fa fa-book"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold">Create New Recipe</h5>
                                <p class="text-muted small mb-0">Fill in the details below to create a new recipe.</p>
                            </div>
                        </div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body modal-body-scrollable p-4">
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <div class="section-box section-blue mb-3">
                                    <div class="d-flex align-items-center gap-2 title fw-bold">
                                        <i class="fa fa-cube mr-2"></i> Basic Information
                                    </div>
                                    <small class="text-muted pl-4">Enter the fundamental details</small>
                                </div>

                                <div class="mb-3">
                                    <label class="fw-bold small">Select Product *</label>
                                    <select class="form-control select2" id="productSelect" name="pm_product_item_id"
                                        style="width: 100%;" required>
                                        <option value="">Select a product...</option>
                                        @foreach($products as $product)
                                            @if($product->pm_product_item_type_id == 1 || $product->pm_product_item_type_id == 3)
                                                <option value="{{ $product->id }}">{{ $product->product_item_name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="fw-bold small">Recipe Image</label>
                                    <div class="upload-area" onclick="document.getElementById('recipeImageInput').click()">
                                        <div class="text-muted mb-2"><i class="fa fa-cloud-upload fa-2x"></i></div>
                                        <div class="fw-bold text-dark small">Click to upload or drag and drop</div>
                                        <div class="text-muted small">PNG, JPG up to 5MB</div>
                                        <img id="imagePreview" src="#" class="mt-2 rounded"
                                            style="display: none; max-width: 100%; max-height: 150px;">
                                    </div>
                                    <input type="file" id="recipeImageInput" name="image" hidden accept="image/*"
                                        onchange="previewImage(this)">
                                </div>

                                <div class="mb-3">
                                    <label class="fw-bold small">Recipe Name *</label>
                                    <input type="text" class="form-control" name="recipe_name"
                                        placeholder="Enter recipe name" required>
                                </div>

                                <div class="mb-3">
                                    <label class="fw-bold small">Yield *</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="yield"
                                            placeholder="Enter yield amount" required>
                                        <div class="input-group-append">
                                            <select class="custom-select" name="pm_variation_value_type_id" required>
                                                @foreach($variationValueTypes as $type)
                                                    <option value="{{ $type['id'] }}">{{ $type['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="discription">Description</label>
                                    <textarea name="description" id="discription" class="form-control"></textarea>
                                </div>
                            </div>

                            <div class="col-lg-6 mb-3">
                                <div class="section-box section-yellow mb-3">
                                    <div class="d-flex align-items-center gap-2 title fw-bold">
                                        <i class="fa fa-cubes mr-2"></i> Ingredients
                                    </div>
                                    <small class="text-muted pl-4">List raw materials</small>
                                </div>

                                <div class="mb-3">
                                    <label class="fw-bold small">Ingredients List</label>
                                    <div class="table-responsive">
                                        <table class="table table-borderless align-middle" id="ingredientsTable">
                                            <thead class="text-muted small border-bottom">
                                                <tr>
                                                    <th style="width: 50%">Item</th>
                                                    <th style="width: 25%">Qty</th>
                                                    <th style="width: 20%">Unit</th>
                                                    <th style="width: 5%"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                    <button type="button" class="btn btn-outline-secondary btn-block border-dashed"
                                        onclick="addIngredientRow()">
                                        <i class="fa fa-plus mr-1"></i> Add Another Ingredient
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="section-box section-purple mb-3">
                                    <div class="d-flex align-items-center gap-2 title fw-bold">
                                        <i class="fa fa-list-ol mr-2"></i> Preparation Instructions
                                    </div>
                                </div>

                                <div id="stepsContainer"></div>

                                <button type="button" class="btn btn-outline-secondary btn-block border-dashed mt-2"
                                    onclick="addStep()">
                                    <i class="fa fa-plus mr-1"></i> Add Another Step
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-0 rounded-bottom">
                        <button type="button" class="btn btn-light border" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning text-white fw-bold">Create Recipe</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Recipe Detail Modal -->
    <div class="modal fade" id="recipeDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content border-0 rounded-lg">
                <div class="modal-header border-0 pb-0">
                    <div class="d-flex align-items-start gap-3">
                        <div class="bg-info text-white rounded p-2 d-flex align-items-center justify-content-center mr-3"
                            style="width: 40px; height: 40px;">
                            <i class="fa fa-eye"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold" id="detailRecipeName">Recipe Details</h5>
                            <p class="text-muted small mb-0" id="detailRecipeDesc">View recipe information</p>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-lg-4">
                            <img id="detailRecipeImage" src="" class="img-fluid rounded mb-3"
                                style="width: 100%; max-height: 250px; object-fit: cover;">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="fw-bold">Details</h6>
                                    <p class="mb-1 small"><strong>Product:</strong> <span id="detailProductName"></span></p>
                                    <p class="mb-1 small"><strong>Yield:</strong> <span id="detailYield"></span></p>
                                    <p class="mb-1 small"><strong>Category:</strong> <span id="detailCategory"></span></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <h6 class="fw-bold text-warning"><i class="fa fa-cubes mr-1"></i> Ingredients</h6>
                            <div class="table-responsive mb-4">
                                <table class="table table-sm table-borderless table-striped" id="detailIngredientsTable">
                                    <thead class="text-muted small">
                                        <tr>
                                            <th>Ingredient</th>
                                            <th>Quantity</th>
                                            <th>Unit</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>

                            <h6 class="fw-bold text-purple"><i class="fa fa-list-ol mr-1"></i> Preparation Steps</h6>
                            <div id="detailStepsList"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            // 1. Initialize Select2 with Bootstrap 4 Theme
            $('.select2').select2({
                theme: 'bootstrap4', // Changed from bootstrap-5
                dropdownParent: $('#createRecipeModal')
            });

            // 2. Initialize Chart.js
            const ctx = document.getElementById('categoryChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Bread', 'Pastry', 'Cakes', 'Others'],
                    datasets: [{
                        data: [35, 25, 30, 10],
                        backgroundColor: ['#f59e0b', '#ec4899', '#8b5cf6', '#6b7280'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    cutout: '75%',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // Handle Form Submission
            $('#createRecipeForm').on('submit', function (e) {
                e.preventDefault();
                
                var formData = new FormData(this);
                var submitBtn = $(this).find('button[type="submit"]');
                var originalBtnText = submitBtn.html();

                // Show loading state
                submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Creating...');

                $.ajax({
                    url: "{{ route('saveRecipe') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function (response) {
                        if (response.success) {
                            $('#createRecipeModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                            });
                        }
                    },
                    error: function (xhr) {
                        var errorMessage = 'Something went wrong!';
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.errors) {
                                // Handle validation errors
                                errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                            } else if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage,
                        });
                    },
                    complete: function() {
                        // Reset button state
                        submitBtn.prop('disabled', false).html(originalBtnText);
                    }
                });
            });
        });

        // 3. Dynamic Ingredients Logic
        const variationTypes = @json($variationValueTypes);

        function addIngredientRow() {
            const rowId = Date.now();

            let unitOptions = '';
            variationTypes.forEach(type => {
                unitOptions += `<option value="${type.id}">${type.name}</option>`;
            });

            const row = `
                        <tr id="ing-row-${rowId}">
                            <td>
                                <select class="form-control select2-dynamic" name="ingredients[]" style="width: 100%;" required>
                                    <option value="">Select ingredient...</option>
                                    @foreach($products as $product)
                                        @if($product->pm_product_item_type_id == 2 || $product->pm_product_item_type_id == 3)
                                            <option value="{{ $product->id }}">{{ $product->product_item_name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" class="form-control form-control-sm" name="quantities[]" placeholder="0" required></td>
                            <td>
                                <select class="form-control form-control-sm" name="units[]">
                                    ${unitOptions}
                                </select>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm text-danger" onclick="removeIngredientRow('${rowId}')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        `;
            $('#ingredientsTable tbody').append(row);

            // Re-init Select2 for the new row (Bootstrap 4 theme)
            $(`#ing-row-${rowId} .select2-dynamic`).select2({
                theme: 'bootstrap4',
                dropdownParent: $('#createRecipeModal')
            });
        }

        function removeIngredientRow(rowId) {
            $(`#ing-row-${rowId}`).remove();
        }

        // 4. Dynamic Steps Logic
        function addStep() {
            const stepId = Date.now();
            const stepCount = $('#stepsContainer .step-card').length + 1;
            const step = `
                                <div class="step-card mb-2" id="step-${stepId}">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold small text-muted">Step <span class="step-number">${stepCount}</span></span>
                                        <div>
                                            <button type="button" class="btn btn-sm text-secondary" onclick="moveStepUp('${stepId}')"><i class="fa fa-arrow-up"></i></button>
                                            <button type="button" class="btn btn-sm text-secondary" onclick="moveStepDown('${stepId}')"><i class="fa fa-arrow-down"></i></button>
                                            <button type="button" class="btn btn-sm text-danger" onclick="removeStep('${stepId}')"><i class="fa fa-trash"></i></button>
                                        </div>
                                    </div>
                                    <textarea class="form-control" name="steps[]" rows="2" placeholder="Describe this step..." required></textarea>
                                </div>
                                `;
            $('#stepsContainer').append(step);
        }

        function removeStep(stepId) {
            $(`#step-${stepId}`).remove();
            updateStepNumbers();
        }

        function moveStepUp(stepId) {
            const current = $(`#step-${stepId}`);
            const prev = current.prev('.step-card');
            if (prev.length) {
                current.insertBefore(prev);
                updateStepNumbers();
            }
        }

        function moveStepDown(stepId) {
            const current = $(`#step-${stepId}`);
            const next = current.next('.step-card');
            if (next.length) {
                current.insertAfter(next);
                updateStepNumbers();
            }
        }

        function updateStepNumbers() {
            $('#stepsContainer .step-card').each(function (index) {
                $(this).find('.step-number').text(index + 1);
            });
        }

        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#imagePreview').attr('src', e.target.result).show();
                    $('.upload-area .text-muted').hide();
                    $('.upload-area .fw-bold').text('Change Image');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function showRecipeDetails(recipe) {
            $('#detailRecipeName').text(recipe.recipe_name);
            $('#detailRecipeDesc').text(recipe.description || 'No description available');
            $('#detailProductName').text(recipe.product_item ? recipe.product_item.product_item_name : 'N/A');
            $('#detailYield').text(recipe.yield); // Add unit if available
            $('#detailCategory').text(recipe.product_item && recipe.product_item.main_category ? recipe.product_item.main_category.product_main_category_name : 'N/A');

            if (recipe.image) {
                $('#detailRecipeImage').attr('src', "{{ asset('') }}" + recipe.image).show();
            } else {
                $('#detailRecipeImage').hide();
            }

            // Populate Ingredients
            let ingredientsHtml = '';
            if (recipe.ingredients && recipe.ingredients.length > 0) {
                recipe.ingredients.forEach(ing => {
                    // Find unit name from variationTypes
                    const unitType = variationTypes.find(t => t.id == ing.pm_variation_value_type_id);
                    const unitName = unitType ? unitType.name : '';

                    ingredientsHtml += `
                                <tr>
                                    <td>${ing.product_item ? ing.product_item.product_item_name : 'Unknown Item'}</td>
                                    <td>${ing.quantity}</td>
                                    <td>${unitName}</td>
                                </tr>
                            `;
                });
            } else {
                ingredientsHtml = '<tr><td colspan="3" class="text-center text-muted">No ingredients found</td></tr>';
            }
            $('#detailIngredientsTable tbody').html(ingredientsHtml);

            // Populate Steps
            let stepsHtml = '';
            if (recipe.steps && recipe.steps.length > 0) {
                recipe.steps.forEach(step => {
                    stepsHtml += `
                                <div class="d-flex mb-3">
                                    <div class="mr-3">
                                        <span class="badge badge-purple rounded-circle" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">${step.step_number}</span>
                                    </div>
                                    <div>
                                        <p class="mb-0 small text-dark">${step.instruction}</p>
                                    </div>
                                </div>
                            `;
                });
            } else {
                stepsHtml = '<p class="text-muted small">No steps found</p>';
            }
            $('#detailStepsList').html(stepsHtml);

            $('#recipeDetailModal').modal('show');
        }
    </script>
@endsection