@php
    $privilageId = \DB::table('pm_interfaces')
        ->select('pm_interfaces.id AS pageId', 'pm_interface_topic.id AS grupId')
        ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
        ->where('pm_interfaces.path', 'adminRawMaterialManagement')
        ->first();
@endphp

@extends('layout', ['pageId' => $privilageId->pageId, 'grupId' => $privilageId->grupId])
@section('content')

<style>
    .table-hover tbody tr:hover {
        background-color: #faf6ec; /* Light blue color - adjust as needed */
        transition: background-color 0.2s; /* Add a smooth transition effect */
    }
</style>

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2><b>Add New Materials</b></h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/admindashboard">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a>Product Management</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Add New Materials</strong>
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

            <!-- Modal -->
            <div class="modal fade" id="newMaterial" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="exampleModalLabel">Add New Raw Materials</h4>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="saveMaterial" method="POST">
                            {{ csrf_field() }}
                            <div class="modal-body">

                                <div class="form-group">
                                    <label for="">Material Name</label>
                                    <input type="text" class="form-control" name="materialName"
                                        value="{{ old('materialName') }}" autocomplete="off">
                                </div>

                                <div class="form-group">
                                    <label for="">Product</label><br>
                                    <select class="form-control" name="product" value="{{ old('product') }}" style="width: 100%">
                                        <option value="0">-- Select One --</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }} ">{{ $product->sub_category_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">Available Count</label>
                                    <input type="number" class="form-control" name="availableCount"
                                        value="{{ old('availableCount') }}" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label for="">Reorder Level</label>
                                    <input type="number" class="form-control" name="reorderCount"
                                        value="{{ old('reorderCount') }}" autocomplete="off">
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save Material</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- </div> --}}

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Stock In</h5>
                </div>
                <div class="ibox-content">
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#newMaterial">Add New Raw Materials</button>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover dataTables-example">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Material Name</th>
                                    <th>Product</th>
                                    <th>Available Quantity</th>
                                    <th>Reorder Level</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($materialList as $materials)
                                    @php
                                        $product = App\SubCategory::find($materials->pm_product_sub_category_id);
                                    @endphp

                                    @if (floatval($materials->reorder_count) >= floatval($materials->available_count))
                                        <tr style="background-color: #a01028; color: #ffffff;">
                                    @else
                                        <tr>
                                    @endif
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $materials->material_name }}</td>
                                    <td>{{ $product->sub_category_name }}</td>
                                    <td>{{ $materials->available_count }}</td>
                                    <td>{{ $materials->reorder_count }}</td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#addStock" onclick="addStockLoadDataToModal({{ $materials->id }})">Add Stock</button>
                                        <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#updateStock" onclick="loadMaterialDataToModal({{ $materials->id }})"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp; Update</button>
                                        <button type="button" class="btn btn-secondary btn-xs" data-toggle="modal" data-target="#updateStockQuantity" onclick="loadQuantityUpdateModalMaerials({{ $materials->id }})"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp; Update Quantity</button>
                                    </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>



                    <!-- Modal -->
                    <div class="modal fade" id="addStock" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content" id="stockModalContent">

                            </div>
                        </div>
                    </div>



                    <!-- Modal -->
                    <div class="modal fade" id="updateStock" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content" id="RawMaterialContent">

                            </div>
                        </div>
                    </div>


                    <!-- Modal -->
                    <div class="modal fade" id="updateStockQuantity" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="exampleModalLabel">Update Stock</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body" id="modelUpdateContent">


                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-warning" onclick="updateBatchQty()">Update
                                        Stock</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>

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


        var mem = $('#data_1 .input-group.date').datepicker({
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: false,
            calendarWeeks: true,
            autoclose: true
        });

        $(".select2_demo_3").select2({
            placeholder: "Select a state",
            allowClear: true
        });


        function loadMaterialDataToModal(materialID) {
            var csrf_token = $("#csrf_token").val();
            jQuery.ajax({
                url: "{{ url('/loadMaterialDataToModal') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "materialID": materialID,
                    "url": 'rawMaterials.ajaxMaterial.loadRawMaterialDataToModal'
                },
                beforeSend: function() {
                    showLder();
                },
                complete: function() {
                },
                error: function(data) {
                },
                success: function(data) {
                    hideLder();
                    $('#RawMaterialContent').html(data);
                }
            });
        }



        function addStockLoadDataToModal(materialID) {
            var csrf_token = $("#csrf_token").val();
            jQuery.ajax({
                url: "{{ url('/loadMaterialDataToModal') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "materialID": materialID,
                    "url": 'rawMaterials.ajaxMaterial.loadStockRawMateriallModal'
                },
                beforeSend: function() {
                    showLder();
                },
                complete: function() {
                },
                error: function(data) {
                },
                success: function(data) {
                    hideLder();
                    $('#stockModalContent').html(data);
                }
            });
        }


        function loadQuantityUpdateModalMaerials(materialID) {
            var csrf_token = $("#csrf_token").val();
            jQuery.ajax({
                url: "{{ url('/loadQuantityUpdateModalMaerials') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "materialID": materialID,
                },
                beforeSend: function() {
                    showLder();
                },
                complete: function() {
                },
                error: function(data) {
                },
                success: function(data) {
                    hideLder();
                    $('#modelUpdateContent').html(data);
                }
            });
        }


        function updateBatchQty() {
            var csrf_token = $("#csrf_token").val();
            var batchId = $("#batchIdPro").val();
            var qty = $("#totStock").text().replace(/,/g, '');
            var action = $("#stockAction").val();

            jQuery.ajax({
                url: "{{ url('/updateMaterialQtyNext') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "batchId": batchId,
                    "qty": qty,
                    "action": action
                },
                beforeSend: function() {
                    showLder();
                },
                complete: function() {
                },
                error: function(data) {
                },
                success: function(data) {
                    if (data.msg == "success") {
                        swal("Success", "Save Success !", "success");
                        window.location = "/adminRawMaterialManagement";
                    } else if (data.msg == "rawmaterialError") {
                        swal("Sorry!", "Adding Qty Cannot be grater than to Material Available Qty!", "danger");
                    } else {
                        swal("Sorry!", "Save Failed!", "danger");
                    }
                }
            });
        }
    </script>
@endsection
