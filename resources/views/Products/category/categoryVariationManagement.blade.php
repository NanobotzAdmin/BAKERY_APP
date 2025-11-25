@php
    $privilageId = \DB::table('pm_interfaces')
        ->select('pm_interfaces.id AS pageId', 'pm_interface_topic.id AS grupId')
        ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
        ->where('pm_interfaces.path', 'adminCategoryVariationManagement')
        ->first();
@endphp

@extends('layout', ['pageId' => $privilageId->pageId ?? 0, 'grupId' => $privilageId->grupId ?? 0])

@section('content')

    <style>
        .table-hover tbody tr:hover {
            background-color: #faf6ec;
            color: #000;
            /* Light blue color - adjust as needed */
            transition: background-color 0.2s;
            /* Add a smooth transition effect */
        }

        .table th {
            text-align: center; /* Horizontally center the text */
            vertical-align: middle !important; /* Vertically center the text */
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
        }
    </style>

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2><b>Category & Variation Management</b></h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/admindashboard">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a>Product Management</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Category & Variation Management</strong>
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

            <!-- Modal for Main Category -->
            <div class="modal fade" id="mainProduct" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="exampleModalLabel">Create New Main Category</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="saveMainCategory" method="POST">
                            {{ csrf_field() }}
                            <div class="modal-body">

                                <div class="form-group">
                                    <label for="category"> Main Category Name</label>
                                    <input type="text" class="form-control" id="category" name="category" value="{{ old('category') }}" autocomplete="off">
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Manage Main Categories</h5>
                </div>
                <div class="ibox-content">
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#mainProduct">Create New Main Category</button>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover dataTables-example" style="font-family: 'Lato', sans-serif;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Main Category Name</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $id = 0; ?>
                                @foreach ($MainCategory as $category)
                                    <?php $id++; ?>
                                    <tr>
                                        <td><?php echo $id; ?></td>
                                        <td>{{ $category->main_category_name }}</td>
                                        @if ($category->is_active == 1)
                                            <td style="min-width: 90px; color: #1ab394; text-align: center;"><span
                                                    class="badge"
                                                    style="color: #28a745; background-color: #e2f5e6;">Active</span></td>
                                        @else
                                            <td style="min-width: 90px; color: #e70000; text-align: center;"><span
                                                    class="badge"
                                                    style="color: #dc3545; background-color: #fceff0;">Inactive</span></td>
                                        @endif
                                        <td>
                                            <button type="button" class="btn btn-outline-warning btn-xs"
                                                data-toggle="modal" data-target="#updateMainCategory"
                                                onclick="showCategoryUpdateModal({{ $category->id }},'mainCategory')"><i
                                                    class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;
                                                Update</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Modal for updating main category -->
                    <div class="modal fade" id="updateMainCategory" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content" id="modelContentCategory">

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    
    <!------------ ////////////// SUB CATEGORY BEGIN HERE ///////////////////////// -->

    <div class="row">
        <div class="col-sm-12">

            <!-- Modal for Sub Category -->
            <div class="modal fade" id="subProduct" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="exampleModalLabel">Create New Product</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form method="POST" action="saveSubCategory">
                            {{ csrf_field() }}
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="sCategory">Product Name</label>
                                    <input type="text" class="form-control form-control-sm" id="sCategory" name="subCategoryName" value="{{ old('subCategoryName') }}" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label for="productCode">Product Code</label>
                                    <input type="text" class="form-control form-control-sm" id="productCode" name="productCode" value="{{ old('productCode') }}" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label for="category">Main Category</label>
                                    <select class="select2 form-control form-control-sm" name="mainCategorySelect" value="{{ old('mainCategorySelect') }}">
                                        <option value="0">-- Select One --</option>
                                        @foreach ($mainActiveCategory as $ActiveCategory)
                                            <option value="{{ $ActiveCategory->id }}">
                                                {{ $ActiveCategory->main_category_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="duration">Expire Duration</label>
                                    <input type="number" class="form-control form-control-sm" id="duration" name="duration" value="{{ old('duration') }}" maxlength="12" autocomplete="off">
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="">Selling Price</label>
                                        <input type="text" class="form-control form-control-sm" name="sellingPrice" maxlength="12" oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" autocomplete="off">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Retail Price</label>
                                        <input type="text" class="form-control form-control-sm" name="retailPrice" maxlength="12" oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="">Actual Cost</label>
                                        <input type="text" class="form-control form-control-sm" name="actualCost" maxlength="12" oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" autocomplete="off">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Discounted Price</label>
                                        <input type="text" class="form-control form-control-sm" name="discountedPrice" maxlength="12" oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="">Discountable Qty</label>
                                        <input type="text" class="form-control form-control-sm" name="discountedQty" maxlength="12" oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" autocomplete="off">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Sequence No</label>
                                        <input type="text" class="form-control form-control-sm" name="sequenceNo" maxlength="2" oninput="this.value = this.value.replace(/[^0-9]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save Product</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    
    
    <!------------ ////////////// VARIATIONS BEGIN HERE ///////////////////////// -->
    
    <div class="row">
        <div class="col-sm-12">
            <!-- Modal for Variation -->
            <div class="modal fade" id="variationModal" tabindex="-1" role="dialog" aria-labelledby="variationModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="variationModalLabel">Create New Variation</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="variationForm">
                            {{ csrf_field() }}
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="variation_name">Variation Name</label>
                                    <input type="text" class="form-control" id="variation_name" name="variation_name" autocomplete="off">
                                    <input type="hidden" id="variation_id" name="variation_id">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary" id="saveVariationBtn">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Manage Variations</h5>
                </div>
                <div class="ibox-content">
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#variationModal" onclick="openVariationModal()">Create New Variation</button>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover dataTables-example" id="variationsTable" style="font-family: 'Lato', sans-serif;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Variation Name</th>
                                    <th>Status</th>
                                    <th>Values</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $id = 0; ?>
                                @foreach ($variations as $variation)
                                    <?php $id++; ?>
                                    <tr id="variation-row-{{ $variation->id }}">
                                        <td>{{ $id }}</td>
                                        <td>{{ $variation->variation_name }}</td>
                                        @if ($variation->is_active == 1)
                                            <td style="min-width: 90px; color: #1ab394; text-align: center;"><span
                                                    class="badge"
                                                    style="color: #28a745; background-color: #e2f5e6;">Active</span></td>
                                        @else
                                            <td style="min-width: 90px; color: #e70000; text-align: center;"><span
                                                    class="badge"
                                                    style="color: #dc3545; background-color: #fceff0;">Inactive</span></td>
                                        @endif
                                        <td>
                                            @if ($variation->variationValues->count() > 0)
                                                <div class="variation-values-container">
                                                    @foreach ($variation->variationValues as $value)
                                                        <span class="variation-value-item">
                                                            {{ $value->variation_value_name ? $value->variation_value_name : $value->variation_value }}
                                                            @if (isset($variationValueTypes[$value->pm_variation_value_type_id]))
                                                                ({{ $variationValueTypes[$value->pm_variation_value_type_id] }})
                                                            @endif
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted">No values added</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-outline-warning btn-xs"
                                                onclick="editVariation({{ $variation->id }}, '{{ $variation->variation_name }}')">
                                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;Edit
                                            </button>
                                            <button type="button" class="btn btn-outline-info btn-xs"
                                                onclick="toggleVariationStatus({{ $variation->id }}, {{ $variation->is_active }})">
                                                @if ($variation->is_active == 1)
                                                    Deactivate
                                                @else
                                                    Activate
                                                @endif
                                            </button>
                                            <button type="button" class="btn btn-outline-primary btn-xs"
                                                onclick="manageVariationValues({{ $variation->id }})">
                                                <i class="fa fa-cog" aria-hidden="true"></i>&nbsp;Manage Values
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!------------ ////////////// VARIATION VALUES MODAL BEGIN HERE ///////////////////////// -->
    
    <div class="modal fade" id="variationValuesModal" tabindex="-1" role="dialog" aria-labelledby="variationValuesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="variationValuesModalLabel">Manage Variation Values</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="current_variation_id">
                    <input type="hidden" id="current_variation_name">
                    
                    <!-- Add new variation value form -->
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Add New Variation Value</h5>
                        </div>
                        <div class="ibox-content">
                            <form id="variationValueForm">
                                {{ csrf_field() }}
                                <input type="hidden" id="variation_value_id" name="variation_value_id">
                                <input type="hidden" id="pm_variation_id" name="pm_variation_id">
                                
                                <div class="form-group">
                                    <label for="variation_value_name">Value Name (Optional)</label>
                                    <input type="text" class="form-control" id="variation_value_name" name="variation_value_name" placeholder="e.g. Small, Medium, Large" autocomplete="off">
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="variation_value">Value *</label>
                                        <input type="text" class="form-control" id="variation_value" name="variation_value" placeholder="e.g. 500, 1, 2.5" autocomplete="off" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="pm_variation_value_type_id">Unit Type *</label>
                                        <select class="form-control" id="pm_variation_value_type_id" name="pm_variation_value_type_id" required>
                                            <option value="">-- Select Unit --</option>
                                            <option value="0" selected>Default</option>
                                            @foreach ($variationValueTypes as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary" id="saveVariationValueBtn">Add Value</button>
                                <button type="button" class="btn btn-secondary" id="cancelVariationValueBtn">Cancel</button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Variation values list -->
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Variation Values</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="variationValuesTable">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Value</th>
                                            <th>Unit</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="variationValuesTableBody">
                                        <!-- Values will be loaded here dynamically -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    <script>
        $(document).ready(function() {
            $('.dataTables-example').DataTable({
                pageLength: 25,
                responsive: true,
                dom: '<"html5buttons"B>lTfgitp',
                buttons: []
            });
        });

        function showCategoryUpdateModal(CategoryId, categoryType) {
            var csrf_token = $("#csrf_token").val();
            jQuery.ajax({
                url: "{{ url('/loadCategoryDataToModal') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "CategoryId": CategoryId,
                    "categoryType": categoryType
                },
                beforeSend: function() {
                    showLder();
                },
                complete: function() {},
                error: function(data) {},
                success: function(data) {
                    hideLder();
                    $('#modelContentCategory').html(data);
                }
            });
        }

        // sub-Catogory Status Change
        function subCatogoryStatusChange(subCatID) {
            var csrf_token = $("#csrf_token").val();
            jQuery.ajax({
                url: "{{ url('/subCatogoryStatusChange') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "subCatID": subCatID,
                },
                beforeSend: function() {
                    showLder();
                },
                complete: function() {},
                error: function(data) {},
                success: function(data) {
                    hideLder();
                    var messageTitle;
                    var messageBody;

                    if (data.msg === 'Sub-Category Deactivated') {
                        messageTitle = 'Deactivated';
                        messageBody = 'Product successfully deactivated.';
                    } else if (data.msg === 'Sub-Category Activated') {
                        messageTitle = 'Activated';
                        messageBody = 'Product successfully activated.';
                    } else {
                        messageBody = 'Operation Error...';
                    }

                    swal({
                            title: messageTitle,
                            text: messageBody,
                            type: "success",
                            showConfirmButton: false, // Hide the OK button
                            timer: 1500, // Set the duration for 1.5 seconds
                        },
                        function() {
                            location.reload();
                        }
                    );
                }
            });
        }
        
        // Variation Management Functions
        function openVariationModal() {
            $('#variationForm')[0].reset();
            $('#variation_id').val('');
            $('#variationModalLabel').text('Create New Variation');
            $('#saveVariationBtn').text('Save');
        }
        
        function editVariation(id, name) {
            $('#variationForm')[0].reset();
            $('#variation_id').val(id);
            $('#variation_name').val(name);
            $('#variationModalLabel').text('Edit Variation');
            $('#saveVariationBtn').text('Update');
            $('#variationModal').modal('show');
        }
        
        function toggleVariationStatus(variationId, currentStatus) {
            var csrf_token = $("#csrf_token").val();
            var newStatus = currentStatus == 1 ? 0 : 1;
            
            jQuery.ajax({
                url: "{{ url('/toggleVariationStatus') }}",
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
                    swal("Error", "Failed to toggle variation status", "error");
                },
                success: function(response) {
                    hideLder();
                    if (response.status === 'success') {
                        swal("Success", response.message, "success");
                        // Reload the page to reflect changes
                        location.reload();
                    } else {
                        swal("Error", response.message, "error");
                    }
                }
            });
        }
        
        // Handle variation form submission
        $('#variationForm').on('submit', function(e) {
            e.preventDefault();
            
            var csrf_token = $("#csrf_token").val();
            var variationId = $('#variation_id').val();
            var variationName = $('#variation_name').val();
            var url = variationId ? "{{ url('/updateVariation') }}" : "{{ url('/saveVariation') }}";
            var data = {
                "_token": csrf_token,
                "variation_name": variationName
            };
            
            if (variationId) {
                data.variation_id = variationId;
            }
            
            jQuery.ajax({
                url: url,
                type: "POST",
                data: data,
                beforeSend: function() {
                    showLder();
                    $('#saveVariationBtn').prop('disabled', true);
                },
                complete: function() {
                    hideLder();
                    $('#saveVariationBtn').prop('disabled', false);
                },
                error: function(xhr) {
                    hideLder();
                    $('#saveVariationBtn').prop('disabled', false);
                    swal("Error", "Failed to save variation", "error");
                },
                success: function(response) {
                    hideLder();
                    $('#saveVariationBtn').prop('disabled', false);
                    
                    if (response.status === 'success') {
                        swal("Success", response.message, "success");
                        $('#variationModal').modal('hide');
                        // Reload the page to reflect changes
                        location.reload();
                    } else {
                        swal("Error", response.message, "error");
                    }
                }
            });
        });
        
        // Manage variation values
        function manageVariationValues(variationId) {
            var variationName = $('#variation-row-' + variationId + ' td:nth-child(2)').text();
            $('#current_variation_id').val(variationId);
            $('#current_variation_name').val(variationName);
            $('#variationValuesModalLabel').text('Manage Values for "' + variationName + '"');
            $('#pm_variation_id').val(variationId);
            
            // Reset form
            $('#variationValueForm')[0].reset();
            $('#variation_value_id').val('');
            $('#saveVariationValueBtn').text('Add Value');
            
            // Load existing values
            loadVariationValues(variationId);
            
            $('#variationValuesModal').modal('show');
        }
        
        // Load variation values
        function loadVariationValues(variationId) {
            var csrf_token = $("#csrf_token").val();
            
            jQuery.ajax({
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
                error: function(xhr) {
                    hideLder();
                    swal("Error", "Failed to load variation values", "error");
                },
                success: function(response) {
                    hideLder();
                    if (response.status === 'success') {
                        renderVariationValues(response.data);
                    } else {
                        $('#variationValuesTableBody').html('<tr><td colspan="5" class="text-center">No values found</td></tr>');
                    }
                }
            });
        }
        
        // Render variation values in table
        function renderVariationValues(values) {
            var html = '';
            var variationValueTypes = @json($variationValueTypes);
            
            if (values.length > 0) {
                $.each(values, function(index, value) {
                    var unitName = variationValueTypes[value.pm_variation_value_type_id] || 'N/A';
                    var statusText = value.is_active == 1 ? 
                        '<span class="badge" style="color: #28a745; background-color: #e2f5e6;">Active</span>' : 
                        '<span class="badge" style="color: #dc3545; background-color: #fceff0;">Inactive</span>';
                    
                    html += '<tr id="value-row-' + value.id + '">';
                    html += '<td>' + (value.variation_value_name || 'N/A') + '</td>';
                    html += '<td>' + value.variation_value + '</td>';
                    html += '<td>' + unitName + '</td>';
                    html += '<td style="text-align: center;">' + statusText + '</td>';
                    html += '<td>';
                    html += '<button type="button" class="btn btn-outline-warning btn-xs" onclick="editVariationValue(' + value.id + ')"><i class="fa fa-pencil-square-o"></i> Edit</button> ';
                    html += '<button type="button" class="btn btn-outline-info btn-xs" onclick="toggleVariationValueStatus(' + value.id + ', ' + value.is_active + ')">' + (value.is_active == 1 ? 'Deactivate' : 'Activate') + '</button> ';
                    html += '<button type="button" class="btn btn-outline-danger btn-xs" onclick="deleteVariationValue(' + value.id + ')"><i class="fa fa-trash"></i> Delete</button>';
                    html += '</td>';
                    html += '</tr>';
                });
            } else {
                html = '<tr><td colspan="5" class="text-center">No values found</td></tr>';
            }
            
            $('#variationValuesTableBody').html(html);
        }
        
        // Edit variation value
        function editVariationValue(valueId) {
            var csrf_token = $("#csrf_token").val();
            
            jQuery.ajax({
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
                error: function(xhr) {
                    hideLder();
                    swal("Error", "Failed to load variation value", "error");
                },
                success: function(response) {
                    hideLder();
                    if (response.status === 'success') {
                        var value = response.data;
                        $('#variation_value_id').val(value.id);
                        $('#variation_value_name').val(value.variation_value_name);
                        $('#variation_value').val(value.variation_value);
                        $('#pm_variation_value_type_id').val(value.pm_variation_value_type_id);
                        $('#saveVariationValueBtn').text('Update Value');
                    } else {
                        swal("Error", response.message, "error");
                    }
                }
            });
        }
        
        // Handle variation value form submission
        $('#variationValueForm').on('submit', function(e) {
            e.preventDefault();
            
            var csrf_token = $("#csrf_token").val();
            var variationValueId = $('#variation_value_id').val();
            var url = variationValueId ? "{{ url('/updateVariationValue') }}" : "{{ url('/saveVariationValue') }}";
            
            var data = {
                "_token": csrf_token,
                "pm_variation_id": $('#pm_variation_id').val(),
                "pm_variation_value_type_id": $('#pm_variation_value_type_id').val(),
                "variation_value": $('#variation_value').val(),
                "variation_value_name": $('#variation_value_name').val()
            };
            
            if (variationValueId) {
                data.variation_value_id = variationValueId;
            }
            
            jQuery.ajax({
                url: url,
                type: "POST",
                data: data,
                beforeSend: function() {
                    showLder();
                    $('#saveVariationValueBtn').prop('disabled', true);
                },
                complete: function() {
                    hideLder();
                    $('#saveVariationValueBtn').prop('disabled', false);
                },
                error: function(xhr) {
                    hideLder();
                    $('#saveVariationValueBtn').prop('disabled', false);
                    swal("Error", "Failed to save variation value", "error");
                },
                success: function(response) {
                    hideLder();
                    $('#saveVariationValueBtn').prop('disabled', false);
                    
                    if (response.status === 'success') {
                        swal("Success", response.message, "success");
                        // Reset form
                        $('#variationValueForm')[0].reset();
                        $('#variation_value_id').val('');
                        $('#saveVariationValueBtn').text('Add Value');
                        // Reload values
                        loadVariationValues($('#current_variation_id').val());
                    } else {
                        swal("Error", response.message, "error");
                    }
                }
            });
        });
        
        // Cancel variation value editing
        $('#cancelVariationValueBtn').on('click', function() {
            $('#variationValueForm')[0].reset();
            $('#variation_value_id').val('');
            $('#saveVariationValueBtn').text('Add Value');
        });
        
        // Toggle variation value status
        function toggleVariationValueStatus(valueId, currentStatus) {
            var csrf_token = $("#csrf_token").val();
            
            jQuery.ajax({
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
                error: function(xhr) {
                    hideLder();
                    swal("Error", "Failed to toggle variation value status", "error");
                },
                success: function(response) {
                    hideLder();
                    if (response.status === 'success') {
                        swal("Success", response.message, "success");
                        // Reload values
                        loadVariationValues($('#current_variation_id').val());
                    } else {
                        swal("Error", response.message, "error");
                    }
                }
            });
        }
        
        // Delete variation value
        function deleteVariationValue(valueId) {
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
                    
                    jQuery.ajax({
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
                        error: function(xhr) {
                            hideLder();
                            swal("Error", "Failed to delete variation value", "error");
                        },
                        success: function(response) {
                            hideLder();
                            if (response.status === 'success') {
                                swal("Deleted!", response.message, "success");
                                // Remove row from table
                                $('#value-row-' + valueId).remove();
                            } else {
                                swal("Error", response.message, "error");
                            }
                        }
                    });
                }
            });
        }
    </script>
@endsection