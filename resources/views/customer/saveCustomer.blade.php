@php
    $privilageId = \DB::table('pm_interfaces')
        ->select('pm_interfaces.id AS pageId', 'pm_interface_topic.id AS grupId')
        ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
        ->where('pm_interfaces.path', 'adminCustomerRegistration')
        ->first();
@endphp

@extends('layout', ['pageId' => $privilageId->pageId, 'grupId' => $privilageId->grupId])

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2><b>Customer Registration</b></h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/admindashboard">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a>People Management</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Customer Registration</strong>
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
                    <h5>Create New Customer</h5>
                </div>
                <div class="ibox-content">
                    <form method="POST" action="saveCustomer">
                        {{ csrf_field() }}
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name">Customer Name <sup class="st-icon-pandora" style="color: #e70000;">*</sup></label>
                                <input type="text" class="form-control form-control-sm" id="name" name="name" value="{{ old('name') }}" maxlength="200" autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label for="address">Address <sup class="st-icon-pandora" style="color: #e70000;">*</sup></label>
                                <input type="text" class="form-control form-control-sm" id="address" name="address" value="{{ old('address') }}" maxlength="200" autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label for="contactPerson">Contact Person Name <sup class="st-icon-pandora" style="color: #e70000;">*</sup></label>
                                <input type="text" class="form-control form-control-sm" id="contactPerson" name="contactPerson" value="{{ old('contactPerson') }}" maxlength="45" autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label for="contactNo">Contact Number <sup class="st-icon-pandora" style="color: #e70000;">*</sup></label>
                                <input type="text" class="form-control form-control-sm" id="contactNo" name="contactNo" value="{{ old('contactNo') }}" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*?)\..*/g, '$1');" autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control form-control-sm" id="email" name="email" value="{{ old('email') }}" maxlength="100" autocomplete="off">
                            </div>

                            @if ($LoggedUser->pm_user_role_id == 1 || $LoggedUser->pm_user_role_id == 2) {{-- Nanobotz Admin & System Admin --}}
                                <div class="form-group">
                                    <label for="maxCreditBills">Max Credit Bills</label>
                                    <input type="number" class="form-control form-control-sm" id="maxCreditBills" name="maxCreditBills" value="{{ old('maxCreditBills') }}" min="0" maxlength="3" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*?)\..*/g, '$1');" autocomplete="off">
                                </div>
                            @else
                                <div class="form-group" style="display: none;">
                                    <label for="maxCreditBills">Max Credit Bills</label>
                                    <input type="number" class="form-control form-control-sm" id="maxCreditBills" name="maxCreditBills" value="0" min="0" maxlength="3" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*?)\..*/g, '$1');" autocomplete="off">
                                </div>
                            @endif

                            <div class="form-group">
                                <label for="maxCreditAmount">Max Credit Amount</label>
                                <input type="text" class="form-control form-control-sm allow_decimal" id="maxCreditAmount" name="maxCreditAmount" value="{{ old('maxCreditAmount') }}" maxlength="15" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label for="maxCreditAvailability">Max Credit Bill Availability <small>(Days)</small></label>
                                <input type="number" class="form-control form-control-sm allow_decimal" id="maxCreditAvailability" name="maxCreditAvailability" value="{{ old('maxCreditAvailability') }}" min="0" max="365" autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label for="deliveryRoute">Delivery Route <sup class="st-icon-pandora" style="color: #e70000;">*</sup></label><br>
                                <select class="select2_demo_3 form-control-sm form-control col-md" name="deliveryRoute">
                                    <option value="0">-- Select One --</option>
                                    @foreach ($routes as $route)
                                        <option value="{{ $route->id }}">{{ $route->route_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="routeOrder">Route No</label>
                                <input type="number" class="form-control form-control-sm" id="routeOrder" name="routeOrder" value="{{ old('routeOrder') }}" min="0" maxlength="45" autocomplete="off">
                            </div>

                            <div class="form-group float-right">
                                <button type="submit" class="btn btn-primary">Register Customer</button>
                            </div>
                            <br>
                            <br>
                        </div>
                    </form>
                    {{-- modal  --}}
                </div>
            </div>
        </div>
    </div>

@endsection


@section('footer')
    <script>
        $(document).ready(function() {
            $('.select2_demo_3').select2();

            $('.dataTables-example').DataTable({
                pageLength: 10,
                responsive: true,
                dom: '<"html5buttons"B>lTfgitp',
                buttons: [
                ]
            });

            $(".allow_decimal").on("input", function(evt) {
                var self = $(this);
                self.val(self.val().replace(/[^0-9\.]/g, ''));
                if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which >
                    57)) {
                    evt.preventDefault();
                }
            });
        });


        function showCustomerUpdateModal(cusID) {
            var csrf_token = $("#csrf_token").val();
            jQuery.ajax({
                url: "{{ url('/loadCusDataToModal') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "cusID": cusID,
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
                    $('#cutomerLoadDataModal').html(data);
                }
            });
        }



        function updateStockRackCount(id) {
            var csrf_token = $("#csrf_token").val();
            jQuery.ajax({
                url: "{{ url('/updateStockRackCount') }}",
                type: "POST",
                data: {
                    "id": id,
                    "_token": csrf_token,
                    "rackCount": $("#totStock").text(),
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
                    if (data.msg == 'success') {
                        $("#totStock").text('0');
                        $("#addingNewQty").val('0');
                        $("#stockAction").val(0);
                        $("#addStoreRackCount").modal('hide');
                        swal("Success", "Save Success !", "success");
                        // window.location = "/adminInvoicePrint/"+data.InvoiceId;
                    } else {
                        swal("Sorry!", "Error!", "danger");
                    }
                }
            });
        }



        function calculateStock() {
            var stockAction = $("#stockAction").val();
            var stockAvailableToCalculate = $("#stockAvailableToCalculate").text();
            var stockAddingQty = $("#addingNewQty").val();
            if (stockAction == 0) {
                swal("Sorry!", "Select Valid method!", "warning");
            } else if (stockAction == 1) {
                $("#qtyAddingDiv").css("display", "none");
                $("#addingNewQty").val(0);
                $("#totStock").text('0');
            } else if (stockAction == 2) {
                $("#qtyAddingDiv").css("display", "block");
            } else {
                $("#qtyAddingDiv").css("display", "block");
            }
        }


        function calculateTot() {
            var stockAction = $("#stockAction").val();
            var stockAvailableToCalculate = $("#stockAvailableToCalculate").text();
            var stockAddingQty = $("#addingNewQty").val();
            var stockTotQty = $("#totStock").text();
            if (stockAction == 2) {
                var tot = parseFloat(stockAvailableToCalculate) + parseFloat(stockAddingQty);
                if (isNaN(tot)) {
                    $("#totStock").text('0');
                } else {
                    $("#totStock").text(tot);
                }
            } else if (stockAction == 3) {
                var tot = parseFloat(stockAvailableToCalculate) - parseFloat(stockAddingQty);
                if (parseFloat(tot) < 0) {
                    swal("Sorry!", "Enter valid amount!", "warning");
                    $("#totStock").text('0');
                    $("#addingNewQty").val(0);
                } else {
                    if (isNaN(tot)) {
                        $("#totStock").text('0');
                    } else {
                        $("#totStock").text(tot);
                    }
                }
            }
        }


        function viewStoreRackModel() {
            var csrf_token = $("#csrf_token").val();
            jQuery.ajax({
                url: "{{ url('/viewStoreRackModel') }}",
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
                    $('#StoreLoadDataModal').html(data);
                }
            });
        }


        function viewCustomerRackModel(cusId) {
            var csrf_token = $("#csrf_token").val();
            jQuery.ajax({
                url: "{{ url('/viewCustomerRackModel') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "cusId": cusId
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
                    $('#CustomerRackLoadDataModal').html(data);
                }
            });
        }


        function cutomerRackCountUpdate(cusId) {
            var csrf_token = $("#csrf_token").val();
            jQuery.ajax({
                url: "{{ url('/cutomerRackCountUpdate') }}",
                type: "POST",
                data: {
                    "cusId": cusId,
                    "_token": csrf_token,
                    "rackCount": $("#totStock2").text(),
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
                    if (data.msg == 'success') {
                        $("#updateCustomerRack").modal('hide');
                        swal("Success", "Save Success !", "success");
                        // window.location = "/adminInvoicePrint/"+data.InvoiceId;
                    } else {
                        swal("Sorry!", "Error!", "danger");
                    }
                }
            });
        }
    </script>
@endsection
