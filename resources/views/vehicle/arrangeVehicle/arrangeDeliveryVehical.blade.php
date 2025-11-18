@php
    $privilageId = \DB::table('pm_interfaces')
        ->select('pm_interfaces.id AS pageId', 'pm_interface_topic.id AS grupId')
        ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
        ->where('pm_interfaces.path', 'adminDeliveryVehicleManagement')
        ->first();
@endphp

@extends('layout', ['pageId' => $privilageId->pageId, 'grupId' => $privilageId->grupId])

@section('content')
{{-- google fonts --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto&family=Roboto+Slab&display=swap" rel="stylesheet">

<style>
    /* --- Table CSS begins --- */
    .styled-table th:first-child {
        border-radius: 5px 0 0 0;
    }
    .styled-table th:last-child {
        border-radius: 0 5px 0 0;
    }
    .styled-table {
        border-collapse: collapse;
        margin: 25px 0;
        font-size: 14px;
        font-family:'Roboto Slab', serif;
        min-width: 400px;
    }
    .styled-table thead tr {
        background-color: #846f5d;
        color: #ffffff;
        text-align: left;
        font-size: 12px;
        font-weight: bold;
        font-family: 'Roboto Slab', serif;
        letter-spacing: 1px;
    }
    .styled-table th,
    .styled-table td {
        padding: 12px 15px;
    }
    .styled-table tbody tr {
        border-bottom: 1px solid #dddddd;
    }
    .styled-table tbody tr:nth-of-type(even) {
        background-color: #ffffff;
    }
    .styled-table tbody tr:last-of-type {
        border-bottom: 2px solid #846f5d;
    }
    .styled-table tbody tr:hover td {
        background-color: #faf6ec;
        color: #e47a00;
    }

    .list-tbl {
        font-family: 'Roboto Slab', serif;
    }
    .list-tbl tbody tr:hover td {
        background-color: #faf6ec;
    }
</style>

<style>
    /* --- Table 2 CSS begins --- */
    .styled-table2 th:nth-child(2) {
        border-radius: 5px 0 0 0;
    }
    .styled-table2 th:last-child {
        border-radius: 0 5px 0 0;
    }
    .styled-table2 {
        border-collapse: collapse;
        margin: 25px 0;
        font-size: 14px;
        font-family:'Roboto Slab', serif;
        min-width: 400px;
    }
    .styled-table2 thead tr {
        background-color: #846f5d;
        color: #ffffff;
        text-align: left;
        font-size: 12px;
        font-weight: bold;
        font-family: 'Roboto Slab', serif;
        letter-spacing: 1px;
    }
    .styled-table2 th,
    .styled-table2 td {
        padding: 12px 15px;
    }
    .styled-table2 tbody tr {
        border-bottom: 1px solid #dddddd;
    }
    .styled-table2 tbody tr:nth-of-type(even) {
        background-color: #ffffff;
    }
    .styled-table2 tbody tr:last-of-type {
        border-bottom: 2px solid #846f5d;
    }
    .styled-table2 tbody tr:hover td {
        background-color: #faf6ec;
        color: #e47a00;
    }

    /* table Input box */
    td input {
        height: 21px !important;
        width: 70px !important;
        text-align: center !important;
    }

    /*placeholder css*/
    #deliveryDate::placeholder, #startMilage::placeholder, #endMilage::placeholder {
        font-size: 11px;
        /* font-style: italic; */
        color: #b9b8b8;
        opacity: 0.7;
    }
</style>

{{-- PAGINATOR CSS --}}
<style>
    .pagination-container {
        display: flex;
        flex-direction: column; /* Stack the elements vertically */
        align-items: left; /* Center elements horizontally */
        margin-top: 20px; /* Add some space above if needed */
    }

    .pagination-info {
        margin-bottom: 10px; /* Space between text and pagination links */
        font-size: 11px;
        font-family: 'Roboto', sans-serif;
    }

    .pagination {
        margin: 0; /* Remove default margin */
    }
</style>

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2><b>Arrange Delivery Vehicle</b></h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/admindashboard">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a>Delivery Management</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Arrange Delivery Vehicle</strong>
                </li>
            </ol>
        </div>
    </div>
    <br>

    <div class="row">
        <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">
        <div class="col-sm-12">
            @include('include.flash')
            @include('include.errors')
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Create Delivery Vehicle</h5>
                </div>
                <div class="ibox-content">
                    <form action="saveDeliveryVehicle" method="POST">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label>Vehicle <sup class="st-icon-pandora" style="color: #ff001e">*</sup></label><br>
                            <select class="select2_demo_3 form-control col-md-4" name="vehicle" id="vehicle">
                                <option value="0">-- Select One --</option>
                                @foreach ($vehicleList as $vehicles)
                                    <option value="{{ $vehicles->id }}">🚚 {{ $vehicles->reg_number }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Driver <sup class="st-icon-pandora" style="color: #ff001e">*</sup></label><br>
                            <select class="select2_demo_3 form-control col-md-4" name="driver" id="driver">
                                <option value="0">-- Select One --</option>
                                @foreach ($driverList as $drivers)
                                    <option value="{{ $drivers->id }}">🧑‍✈️ {{ $drivers->driver_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Sales Representative <sup class="st-icon-pandora" style="color: #ff001e">*</sup></label><br>
                            <select class="select2_demo_3 form-control col-md-4" name="saleRep" id="saleRep">
                                <option value="0">-- Select One --</option>
                                @foreach ($salesRepList as $saleRep)
                                    <option value="{{ $saleRep->id }}">👨‍💼 {{ $saleRep->sales_rep_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="chNo">Delivery Route <sup class="st-icon-pandora" style="color: #ff001e">*</sup></label><br>
                            <select class="select2_demo_3 form-control col-md-4" name="deliveryRoute" id="deliveryRoute">
                                <option value="0">-- Select One --</option>
                                @foreach ($routes as $route)
                                    <option value="{{ $route->id }}">♻️ {{ $route->route_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- <div class="form-group">
                            <label>Delivery Date</label><br>
                            <input type="date" class="form-control col-md-2" name="deliveryDate"/>
                        </div> --}}

                        <div class="form-group col-md-4" id="data_1" style="padding: 0;">
                            <label class="font-normal">Delivery Date <sup class="st-icon-pandora" style="color: #ff001e">*</sup></label>
                            <div class="input-group date">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <input type="text" class="form-control form-control-sm" name="deliveryDate" id="deliveryDate" maxlength="10" oninput="this.value = this.value.replace(/[^0-9-]/g, '').replace(/(\..*?)\..*/g, '$1');" placeholder="YYYY-MM-DD" autocomplete="off">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="chNo">Starting Milage <sup class="st-icon-pandora" style="color: #ff001e">*</sup></label>
                            <input type="text" class="form-control form-control-sm col-md-4" name="startMilage" id="startMilage" value="{{ old('startMilage') }}" maxlength="15" placeholder="Enter starting mileage here..." oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" autocomplete="off">
                        </div>

                        <br>
                        <div class="form-group col-md-4" style="padding-left: 0; padding-right: 0;">
                            {{-- <div class="col-md-3">&nbsp;</div> --}}
                            {{-- <div class="col-md-4"> --}}
                                <button class="btn btn-primary btn-sm btn-block pull-right" type="submit"><i class="fa fa-truck" aria-hidden="true"></i> &nbsp; Create Delivery Vehicle</button>
                            {{-- </div> --}}
                        </div>
                    </form>
                    <br>
                </div>
            </div>
        </div>
    </div>

    {{-- </div> --}}

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Delivery Vehicle Details</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-hover dataTables-example styled-table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="min-width: 50px;">#</th>
                                    <th style="min-width: 120px;">Vehicle No</th>
                                    <th style="min-width: 120px;">Driver Name</th>
                                    <th style="min-width: 220px;">Sales Rep Name</th>
                                    <th style="min-width: 130px;">Satus</th>
                                    <th style="min-width: 130px;">Rack Count</th>
                                    <th style="min-width: 130px;">Date</th>
                                    <th style="min-width: 470px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $id = 0; ?>
                                @foreach ($deliveryVehicles as $vehicles)
                                    <?php
                                    $invoiceVehicle = App\customerInvoiceHasStock::where('dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id', $vehicles->id)->get();
                                    $id++;
                                    $vehicle = App\Vehicles::find($vehicles->vm_vehicles_id);
                                    $driver = App\Driver::find($vehicles->vm_drivers_id);
                                    $saleRep = App\SaleRep::find($vehicles->vm_sales_reps_id);
                                    $rackCount = App\DeliveryVehicleHasStock::where('dm_delivery_vehicle_id', $vehicles->id)->sum('racks_count');
                                    $countDelivery = count($invoiceVehicle);
                                    ?>

                                    <tr style="font-size: 13px;">
                                        <td>{{ $id }}</td>
                                        <td>{{ $vehicle->reg_number }}</td>
                                        <td>{{ $driver->driver_name }}</td>
                                        <td>{{ $saleRep->sales_rep_name }}</td>
                                        <td>
                                            @if($vehicles->status == 0)
                                                <span><i class="fa fa-exclamation-circle" aria-hidden="true" style="color: #ffc800;"></i> Pending</span>
                                            @elseif ($vehicles->status == 1)
                                                <span><i class="fa fa-ship" aria-hidden="true" style="color: #0074f1;"></i> Loaded</span>
                                            @elseif ($vehicles->status == 2)
                                                <span><i class="fa fa-check-circle" aria-hidden="true" style="color: #12d900;"></i> Completed</span>
                                            @elseif ($vehicles->status == 3)
                                                <span><i class="fa fa-ban" aria-hidden="true" style="color: #d80019;"></i> Deleted</span>
                                            @endif
                                        </td>
                                        @if (App\DeliveryVehicleHasStock::where('dm_delivery_vehicle_id', $vehicles->id)->exists())
                                            <td style="padding-left: 15px;">{{ $rackCount }}</td>
                                        @else
                                            <td style="padding-left: 15px;">0</td>
                                        @endif
                                        <td>{{ date('Y-m-d', strtotime($vehicles->created_at)) }}</td>
                                        <td>
                                            @if (App\DeliveryVehicleHasStock::where('dm_delivery_vehicle_id', $vehicles->id)->exists())
                                            @else
                                            @endif

                                            @if ($vehicles->status == 1)
                                                <button type="button" class="btn btn-danger btn-xs" onclick="viewCompleteModal({{ $vehicles->id }})">Complete</button>
                                            @else
                                            @endif

                                            @if ($countDelivery == 0)
                                                <button type="button" class="btn btn-danger btn-xs" onclick="deleteDelivery({{ $vehicles->id }})" style="background-color: #a64bb6; border-color: #a64bb6;">&nbsp;&nbsp; Delete &nbsp;&nbsp;</button>
                                            @endif

                                            @if ($vehicles->status == 2)
                                            @else
                                                <button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#addItems" data-backdrop="static" onclick="loadItemsModal({{ $vehicles->id }})">Add Items</button>
                                                <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#updateItems" data-backdrop="static" onclick="loadItemsModalUpdate({{ $vehicles->id }})">Update Items</button>
                                            @endif

                                            <button type="button" class="btn btn-secondary btn-xs" data-toggle="modal" data-target="#updateReturns" data-backdrop="static" onclick="loadUpdateModalOfUnloadingsAndReturns({{ $vehicles->id }})">Update Unloadings & Returns</button>
                                            <button type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#viewItems" onclick="viewDeliveryData({{ $vehicles->id }})">View</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{-- Custom Paginator --}}
                        <div class="pagination-container">
                            <div class="pagination-info">
                                Showing {{ $deliveryVehicles->firstItem() }} to {{ $deliveryVehicles->lastItem() }} of {{ $deliveryVehicles->total() }} results
                            </div>
                            <div class="pagination-links">
                                {{ $deliveryVehicles->links('vendor.pagination.bootstrap-4') }}  {{-- Use custom Bootstrap 4 view --}}
                            </div>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="addItems" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content" id="loadItemsModalDiv">

                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="updateItems" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content" id="loadItemsModalDivUpdate">

                            </div>
                        </div>
                    </div>


                    <!----load Update Returns modal--->
                    <div class="modal fade" id="updateReturns" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content" id="load_UnloadingsAndReturns_Modal_DIV">

                            </div>
                        </div>
                    </div>



                    <!----load delivery deta modakl--->
                    <div class="modal fade" id="viewItems" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content" id="loadDeliveryDataModalDiv">

                            </div>
                        </div>
                    </div>


                    <!-------Complete Modal------->


                    <div class="modal fade" id="CompleteModal" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content" id="completeModalLoadData">

                            </div>
                        </div>
                    </div>


                    <div class="modal" tabindex="-1" role="dialog" id="show">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content" id="loadModel">

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
            $(".allow_decimal").on("input", function(evt) {
                var self = $(this);
                self.val(self.val().replace(/[^0-9\.]/g, ''));
                if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
                    evt.preventDefault();
                }
            });

            var mem = $('#data_1 .input-group.date').datepicker({
                todayBtn: "linked",
                keyboardNavigation: false,
                forceParse: false,
                calendarWeeks: true,
                autoclose: true,
                format: "yyyy-mm-dd"
            });

            // $('.dataTables-example').DataTable({
            //     pageLength: 10,
            //     responsive: true,
            //     dom: '<"html5buttons"B>lTfgitp',
            //     buttons: [
            //     ]
            // });


            $('#addItems').on('show.bs.modal', function() {
                $('.select2_demo_3').select2();
            })


            $(".select2_demo_3").select2({
                placeholder: "-- Select One --",
                allowClear: true
            });

        });



        // function saveDeliveryVehicle() {
        //     var csrf_token = $("#csrf_token").val();
        //     var vehicle = $('#vehicle option:selected').val();
        //     var driver = $('#driver option:selected').val();
        //     var saleRep = $('#saleRep option:selected').val();
        //     var deliveryRoute = $('#deliveryRoute option:selected').val();
        //     var deliveryDate = $('#deliveryDate').val();
        //     var startMilage = $('#startMilage').val();

        //     jQuery.ajax({
        //         url: "{{ url('/saveDeliveryVehicle') }}",
        //         type: "POST",
        //         data: {
        //             "_token": csrf_token,
        //             "vehicle": vehicle,
        //             "driver": driver,
        //             "saleRep": saleRep,
        //             "deliveryRoute": deliveryRoute,
        //             "deliveryDate": deliveryDate,
        //             "startMilage": startMilage
        //         },
        //         beforeSend: function() {
        //             showLder();
        //         },
        //         complete: function() {
        //         },
        //         error: function(data) {
        //         },
        //         success: function(data) {
        //             hideLder();
        //             swal("Success", "Delivery Vehicle Saved.", "success");
        //         }
        //     });
        // }


        function loadItemsModal(deliveryId) {
            var csrf_token = $("#csrf_token").val();
            jQuery.ajax({
                url: "{{ url('/loadItemsModal') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "deliveryId": deliveryId,
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
                    $("#TblAddProductTODelivery tbody").empty();
                    $('#loadItemsModalDiv').html(data);
                }
            });
        }

        function loadItemsModalUpdate(deliveryId) {
            var csrf_token = $("#csrf_token").val();
            jQuery.ajax({
                url: "{{ url('/loadItemsModalUpdate') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "deliveryId": deliveryId,
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
                    $('#loadItemsModalDivUpdate').html(data);
                }
            });
        }


        function removeDeliveryProducts(batchID, VehicleID) {
            var csrf_token = $("#csrf_token").val();
            jQuery.ajax({
                url: "{{ url('/removeDeliveryProducts') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "batchID": batchID,
                    "VehicleID": VehicleID
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
                    if (data.msg == 'sucess') {
                        swal("Success", "Delivery RemoveSuccess !", "success");
                        window.location = "/adminDeliveryVehicleManagement";
                    } else {
                        swal("Stop", "Delivery remove Failed !", "error");
                    }
                }
            });
        }


        function loadAvailableQty() {
            var batchCombo = $('#batchCombo option:selected').val();
            var csrf_token = $("#csrf_token").val();
            if (batchCombo == 0) {
                swal("Sorry!", "Select Batch!", "warning");
            } else {
                jQuery.ajax({
                    url: "{{ url('/loadstockUpdateData') }}",
                    type: "POST",
                    data: {
                        "_token": csrf_token,
                        "batchId": batchCombo,
                        "url": "vehicle.arrangeVehicle.ajaxDeliveryVehicle.loadBatchAvailable"
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
                        $('#loadAvailableQty').html(data);
                    }
                });
            }
        }


        function loadSubCategories() {
            var MainCategory = $('#MainCategory option:selected').val();
            var csrf_token = $("#csrf_token").val();
            $("#batchCombo").empty();
            $('#batchCombo').append("<option value='0'> -- Select One -- </option>");
            $("#subCategory").empty();
            $('#subCategory').append("<option value='0'> -- Select One -- </option>");

            if (MainCategory == 0) {
                swal("Sorry!", "Select Main Category!", "warning");
            } else {
                jQuery.ajax({
                    url: "{{ url('/loadSubCategories') }}",
                    type: "POST",
                    data: {
                        "_token": csrf_token,
                        "MainCategory": MainCategory,
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
                        $("#subCategory").empty();
                        $('#subCategory').append("<option value='0'> -- Select One -- </option>");
                        var html = '';
                        $.each(data.products, function(key, val) {
                            html += '<option value =' + val.id + '>' + val.sub_category_name + '</option>';
                        });
                        $('#subCategory').append(html);
                        $("#MODAL_AVAILABLE_QTY").text(0);
                    }
                });
            }
        }


        function loadModel() {
            var csrf_token = $("#csrf_token").val();
            jQuery.ajax({
                url: "{{ url('/loadModel') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
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
                    $('#loadModel').html(data);
                }
            });
        }


        function loadBatchDetails() {
            var subCategory = $('#subCategory option:selected').val();
            var csrf_token = $("#csrf_token").val();
            if (subCategory == 0) {
                swal("Sorry!", "Select Sub Category!", "warning");
            } else {
                jQuery.ajax({
                    url: "{{ url('/loadBatchDetails') }}",
                    type: "POST",
                    data: {
                        "_token": csrf_token,
                        "subCategory": subCategory,
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
                        $("#batchCombo").empty();
                        $('#batchCombo').append("<option value='0'> -- Select One -- </option>");
                        var html = '';
                        $.each(data.batchCodes, function(key, val) {
                            html += '<option value =' + val.id + '>' + val.batch_code + ' | Price - Rs.' +
                                val.selling_price + ' | Qty - ' + val.available_quantity + '</option>';
                        });
                        $('#batchCombo').append(html);
                        $("#MODAL_AVAILABLE_QTY").text(0);
                    }
                });
            }
        }


        function addProductTODelivery(value) {
            var csrf_token = $("#csrf_token").val();
            var tableUsers = document.getElementById("TblAddProductTODelivery");
            var rowCount = tableUsers.rows.length;
            var dublicateData = true;
            var productId = $('#subCategory option:selected').val();
            var vehicleId = $('#vehicleId').val();
            var productName = $('#subCategory option:selected').text();
            var batchId = $('#batchCombo option:selected').val();
            var StockAvailable = $('#stockQty').text();
            var qty = $('#MODAL_QTY').val();
            var rackQty1 = $('#rackQty').val();
            var MainCategory = $('#MainCategory option:selected').val();
            var avaialableQty = $("#MODAL_AVAILABLE_QTY").text();
            var batchCodeStock = $("#batchCodeStock").val();

            if (rackQty1 == '') {
                rackQty = 0;
            } else {
                rackQty = rackQty1;
            }

            if (MainCategory == 0) {
                swal("", "Please select a Main Category.", "warning");
            } else if (productId == 0) {
                swal("", "Please select a Sub-Category.", "warning");
            } else if (batchId == 0) {
                swal("", "Please select a Batch.", "warning");
            } else if (qty == '') {
                swal("", "Please enter a valid Loading Quantity.", "warning");
                $('#MODAL_QTY').focus();
            } else if (parseInt(qty) > parseInt(avaialableQty)) {
                swal("", "Loading quantity cannot be exceeded the Available Stock.", "warning");
            } else {
                for (var i = 1; i < rowCount; i++) {
                    var Checkvehicle = document.getElementById("TblAddProductTODelivery").rows[i].cells[4].innerText;
                    var checkBatch = document.getElementById("TblAddProductTODelivery").rows[i].cells[2].innerText;
                    if (Checkvehicle === vehicleId && checkBatch === batchId) {
                        dublicateData = false;
                    }
                }

                if (dublicateData) {
                    var row = tableUsers.insertRow(1);
                    $('#TblAddProductTODelivery > tbody:last').append(row);
                    var cell1 = row.insertCell(0);
                    var cell2 = row.insertCell(1);
                    var cell3 = row.insertCell(2);
                    var cell4 = row.insertCell(3);
                    var cell5 = row.insertCell(4);
                    var cell6 = row.insertCell(5);
                    var cell7 = row.insertCell(6);
                    var cell8 = row.insertCell(7);
                    var cell9 = row.insertCell(8);
                    var cell10 = row.insertCell(9);
                    var cell11 = row.insertCell(10);
                    //                                                var cell4 = row.insertCell(3);
                    //                                                cell1.innerHTML = xx;
                    cell1.innerHTML = productId;
                    cell1.style.display = "none";
                    cell2.innerHTML = productName;
                    cell3.innerHTML = batchId;
                    cell3.style.display = "none";
                    cell4.innerHTML = rackQty;
                    // cell4.style.display = "none";
                    cell5.innerHTML = vehicleId;
                    cell5.style.display = "none";
                    cell6.innerHTML = qty;
                    cell7.innerHTML = qty;
                    cell7.style.display = "none";
                    cell8.innerHTML = "0";
                    // cell9.innerHTML = "<input type='number' value='0' disabled/>";
                    cell9.innerHTML = "0";
                    cell10.innerHTML =
                        "<button onclick='$(this).parent().parent().remove();' type='button' class='btn btn-xs btn-danger' value='Remove'><i class='fa fa-trash' aria-hidden='true'></i> Remove</button>";
                    cell11.innerHTML = "1";
                    cell11.style.display = "none";

                    $('select option[value="0"]').attr("selected", true);
                    $("#rackQty").val(0);
                    $("#MODAL_QTY").val(0);
                    $("#MODAL_AVAILABLE_QTY").text(0);
                } else {
                    swal("Stop", "This Delivery Stock already in Table !", "error");
                }
            }
        }


        var json_delivery_details = {};

        function saveDeliveryData(deliveryId) {
            var deliveryRackTot = 0;
            var deliveryRackTot1 = 0;
            var deliveryRackTot2 = 0;
            var rack1 = 0;
            var rack2 = 0;
            var rowCount = document.getElementById("TblAddProductTODelivery").rows.length;
            var tbodyCount = document.getElementById("tblDeliveryBody").rows.length;
            var csrf_token = $("#csrf_token").val();

            if (rowCount == 1) {
                swal("Sorry!", "Enter Products!", "warning");
            } else {
                var deliveryData = new Array();

                for (var x = 1; x < rowCount; x++) {
                    var productId = document.getElementById("TblAddProductTODelivery").rows[x].cells[0].innerHTML;
                    var batchId = document.getElementById("TblAddProductTODelivery").rows[x].cells[2].innerHTML;
                    var qty = document.getElementById("TblAddProductTODelivery").rows[x].cells[5].innerHTML;
                    var rackQty = document.getElementById("TblAddProductTODelivery").rows[x].cells[3].innerHTML;
                    var vehicle = document.getElementById("TblAddProductTODelivery").rows[x].cells[4].innerHTML;
                    //    var rackCount = document.getElementById("TblAddProductTODelivery").rows[x].cells[8].children[0].value;
                    var stock = document.getElementById("TblAddProductTODelivery").rows[x].cells[6].innerHTML;
                    var update = document.getElementById("TblAddProductTODelivery").rows[x].cells[10].innerHTML;

                    if (update == '1') {
                        var rowOfData = {};
                        rowOfData['productId'] = productId;
                        rowOfData['batchId'] = batchId;
                        rowOfData['qty'] = qty;
                        rowOfData['rackQty'] = rackQty;
                        rowOfData['vehicle'] = vehicle;
                        //   rowOfData['updateRack'] = rackCount;
                        rowOfData['stock'] = stock;

                        if (rackQty == '') {
                            rack1 = 0;
                        } else {
                            rack1 = rackQty;
                        }

                        deliveryRackTot1 += parseFloat(rack1);
                        // deliveryRackTot2 += parseFloat(rack2);
                        deliveryData.push(rowOfData);
                    }
                }

                deliveryRackTot = parseFloat(deliveryRackTot1);
                json_delivery_details['deliveryDetails'] = deliveryData;

                if (deliveryData.length == 0) {
                    swal("Sorry!", " You dont have stock batches!", "warning");
                } else {
                    jQuery.ajax({
                        url: "{{ url('/saveDeliveryData') }}",
                        type: "POST",
                        data: {
                            "_token": csrf_token,
                            "deliveryData": json_delivery_details,
                            "deliveryId": deliveryId,
                            "deliveryRackTot": deliveryRackTot
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
                            if (data.msg == 'sucess') {
                                swal("Success", "Delivery Stock Save Success !", "success");
                                window.location = "/adminDeliveryVehicleManagement";
                            } else if (data.msg == "rack") {
                                swal("Cannot Save Data", "Increase Store Rack", "warning");

                            } else {
                                swal("Stop", data.msgDB, "danger");

                            }
                        }
                    });
                }
            }
        }


        function viewCompleteModal(deliveryId) {
            var csrf_token = $("#csrf_token").val();
            jQuery.ajax({
                url: "{{ url('/viewCompleteModal') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "deliveryId": deliveryId,
                    "url": "vehicle.arrangeVehicle.ajaxDeliveryVehicle.loadCompleteModal"
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
                    $('#CompleteModal').modal('show');
                    $('#completeModalLoadData').html(data);
                }
            });
        }


        function completeDelivery(deliveryId) {
            var csrf_token = $("#csrf_token").val();
            var endMilage = $("#endMilage").val();
            if (endMilage == '' || endMilage < 0 || isNaN(endMilage)) {
                swal("Sorry!", "Enter Valid End Mile Value!", "warning");
            } else {
                jQuery.ajax({
                    url: "{{ url('/completeDelivery') }}",
                    type: "POST",
                    data: {
                        "_token": csrf_token,
                        "endMilage": endMilage,
                        "deliveryId": deliveryId,
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
                        if (data.msg == 'sucess') {
                            swal("Success", "Delivery Complete Sucess !", "success");
                            window.location = "/adminDeliveryVehicleManagement";
                        } else {
                            swal("Stop", "Delivery Complete Failed !", "error");
                        }
                    }
                });
            }
        }


        function checkAvailable(value) {
            var AvailableQty = $("#MODAL_AVAILABLE_QTY").text();
            if (parseFloat(value) > parseFloat(AvailableQty)) {
                swal("Stop", "Loading Quantity cannot be exceeded the Available Stock.", "warning");
                $("#MODAL_QTY").val(0);
            }
        }


        // load Update Modal ~ Unloadings & Returns
        function loadUpdateModalOfUnloadingsAndReturns(deliveryId) {
            var csrf_token = $("#csrf_token").val();
            jQuery.ajax({
                url: "{{ url('/loadUpdateModalOfUnloadingsAndReturns') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "deliveryId": deliveryId,
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
                    $("#load_UnloadingsAndReturns_Modal_DIV").empty();
                    $('#load_UnloadingsAndReturns_Modal_DIV').html(data);
                }
            });
        }


        // Update UNLOADINGS
        function updateUnloadings(deliveryId) {
            var deliveryID = deliveryId;
            var csrf_token = $("#csrf_token").val();
            var emptyValue_Count = 0;
            // get Unloadings table data
            var cp = [];
            var table = $("#updateUnloadings_TBL tbody");

            table.find('tr').each(function (i, el) {
                var $tds = $(this).find('td');
                var sn = $tds.eq(0).html();
                var product = $tds.eq(1).html();
                var loadingQty = $tds.eq(2).html();
                var unloadingQty = $tds.eq(3).html();
                var physicalUnloadingQty = $tds.eq(4).find("input").val();
                var sellQty = $tds.eq(5).html();
                var stockBatchID = $tds.eq(6).html();

                if (physicalUnloadingQty == "") {
                    emptyValue_Count++;
                } else {
                    cp.push({
                        "sn": sn,
                        "product": product,
                        "loadingQty": loadingQty,
                        "unloadingQty": unloadingQty,
                        "physicalUnloadingQty": physicalUnloadingQty,
                        "sellQty": sellQty,
                        "stockBatchID": stockBatchID
                    });
                }
            });
            dataString = JSON.stringify(cp);
            if (emptyValue_Count == 0) {
                jQuery.ajax({
                    url: "{{ url('/updateUnloadings') }}",
                    type: "POST",
                    data: {
                        "_token": csrf_token,
                        "deliveryID": deliveryID,
                        "dataString": dataString
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
                        if (data.msg == 'sucess') {
                            $('#updateReturns').modal('hide'); // close the modal
                            swal({
                                title: "Success",
                                text: "Unloadings quantities successfully Updated.",
                                type: "success"});
                            // swal({
                            //     title: "Success",
                            //     text: "Unloadings quantities successfully Updated.",
                            //     type: "success"}).then(okay => {
                            //     if (okay) {
                            //         loadUpdateModalOfUnloadingsAndReturns(deliveryID);
                            //     }
                            // });
                        } else {
                            swal("Stop", "Error in Unloadings Quantities update!!!", "error");
                        }
                    }
                });
            } else {
                swal('', 'Please enter all the Physical Unloading Quantities before proceed to update !!!', 'warning');
            }
        }


        // Update RETURNS
        function updateReturns(deliveryId) {
            var deliveryID = deliveryId;
            var csrf_token = $("#csrf_token").val();
            var emptyValue_Count = 0;
            // get Returns table data
            var cp = [];
            var table = $("#updateReturns_TBL tbody");
            table.find('tr').each(function (i, el) {
                var $tds = $(this).find('td');
                var sn = $tds.eq(0).html();
                var product = $tds.eq(1).html();
                var stockBatchID = $tds.eq(2).html();
                var systemReturnQty = $tds.eq(3).html();
                var physicalReturnQty = $tds.eq(4).find("input").val();

                if (physicalReturnQty == "") {
                    emptyValue_Count++;
                } else {
                    cp.push({
                        "sn": sn,
                        "product": product,
                        "stockBatchID": stockBatchID,
                        "systemReturnQty": systemReturnQty,
                        "physicalReturnQty": physicalReturnQty,
                    });
                }
            });
            dataString = JSON.stringify(cp);
            if (emptyValue_Count == 0) {
                jQuery.ajax({
                    url: "{{ url('/updateReturns') }}",
                    type: "POST",
                    data: {
                        "_token": csrf_token,
                        "deliveryID": deliveryID,
                        "dataString": dataString
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
                        if (data.msg == 'sucess') {
                            $('#updateReturns').modal('hide'); // close the modal
                            swal({
                                title: "Success",
                                text: "Return quantities successfully Updated.",
                                type: "success"});
                        } else {
                            swal("Stop", "Returns updating failed !", "error");
                        }
                    }
                });
            } else {
                swal('', 'Please enter all the "Physical Return Qty" before updating !', 'error');
            }
        }


        // Delivery Vehicle details view
        function viewDeliveryData(deliveryId) {
            var csrf_token = $("#csrf_token").val();
            jQuery.ajax({
                url: "{{ url('/viewDeliveryData') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "deliveryId": deliveryId,
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
                    $("#DeliveryData tbody").empty();
                    $('#loadDeliveryDataModalDiv').html(data);
                }
            });
        }


        function changeSubTotal(avaialableQty, enteredQty, batchId, loadedQty) {
            var totEntered = parseFloat(loadedQty) + parseFloat(avaialableQty);
            if (parseFloat(enteredQty) > parseFloat(avaialableQty)) {
                swal("Sorry!", "Qty Cannot be greater than to Available Qty!", "warning");
                $("#" + batchId + "").val(0);
            }
        }


        // DELETE Delivery Vehicle
        function deleteDelivery(deliveryId) {
            var csrf_token = $("#csrf_token").val();
            swal({
                    title: "Delete Delivery Vehicle?",
                    text: "You will not be able to recover this action. Please confirm the deletion.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#ED5565",
                    confirmButtonText: "Yes, Delete",
                    cancelButtonText: "Cancel",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm) {
                    if (isConfirm) {
                        jQuery.ajax({
                            url: "{{ url('/deleteDelivery') }}",
                            type: "POST",
                            data: {
                                "_token": csrf_token,
                                "deliveryId": deliveryId,
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
                                if (data.msg == 'sucess') {
                                    swal("Success", "Delivery Vehicle Deleted.", "success");
                                    window.location = "/adminDeliveryVehicleManagement";
                                } else {
                                    // swal("Stop", "Delivery Delete Failed !", "error");
                                    swal("Stop", data.msg, "error");
                                }
                            }
                        });
                    } else {
                        swal.close();
                    }
                })
        }
    </script>
@endsection
