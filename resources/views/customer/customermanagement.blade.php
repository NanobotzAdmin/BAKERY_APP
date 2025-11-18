@php

$privilageId = \DB::table('pm_interfaces')
    ->select('pm_interfaces.id AS pageId', 'pm_interface_topic.id AS grupId')
    ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
    ->where('pm_interfaces.path', 'adminCustomerManagement')
    ->first();
@endphp

@php
    $Nanobots_Admin = 1;
    $System_Admin = 2;
    $SalesRep = 3;
    $Driver = 4;
    $Manager = 5;
@endphp

@extends('layout', ['pageId' => $privilageId->pageId, 'grupId' => $privilageId->grupId])

@section('content')

    {{-- Sawarabi Gothic font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sawarabi+Gothic&display=swap" rel="stylesheet">

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

        span.activeStatusDot {
            display: inline-block;
            /* or block */
            height: 8px;
            width: 8px;
            vertical-align: 0px;
            background: #00dd1d;
            box-shadow: 0 0 6px #00dd1d;
            border-radius: 50%;
        }

        span.deactiveStatusDot {
            display: inline-block;
            /* or block */
            height: 8px;
            width: 8px;
            vertical-align: 0px;
            background: #e70000;
            box-shadow: 0 0 5px #ff0000;
            border-radius: 50%;
        }
    </style>

    <style>
        #map {
            height: 400px;
            width: 100%;
        }
    </style>

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2><b>Customer Management</b></h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/admindashboard">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a>People Management</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Customer Management</strong>
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


        {{-- ================================================================ LOCATION VIEWER ================================================================ --}}
            {{-- @php
                $customers_location_list = App\Customer::where('cm_routes_id', $assignedRoute->id)->where('is_active', 1)->whereNotNull('location_link')->get(['location_link']);
            @endphp

            <div id="map"></div> --}}
        {{-- ================================================================ LOCATION VIEWER ================================================================ --}}


            <!-- Modal -->
            <div class="modal fade" id="createCustomer" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form method="POST" action="saveCustomer">
                            {{ csrf_field() }}
                            <div class="modal-header">
                                <h4 class="modal-title" id="exampleModalLabel">Create Customer</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">

                                <div class="form-group">
                                    <label for="name">Customer Name <sup class="st-icon-pandora" style="color: #e70000;">*</sup></label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label for="address">Address <sup class="st-icon-pandora" style="color: #e70000;">*</sup></label>
                                    <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label for="contactPerson">Contact Person Name <sup class="st-icon-pandora" style="color: #e70000;">*</sup></label>
                                    <input type="text" class="form-control" id="contactPerson" name="contactPerson" value="{{ old('contactPerson') }}" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label for="contactNo">Contact Number <sup class="st-icon-pandora" style="color: #e70000;">*</sup></label>
                                    <input type="text" class="form-control" id="contactNo" name="contactNo" value="{{ old('contactNo') }}" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" autocomplete="off">
                                </div>

                                @if ($LoggedUser->pm_user_role_id == 1 || $LoggedUser->pm_user_role_id == 2) {{-- Nanobotz Admin & System Admin --}}
                                    <div class="form-group">
                                        <label for="maxCreditBills">Max Credit Bills</label>
                                        <input type="number" class="form-control" id="maxCreditBills" name="maxCreditBills" value="{{ old('maxCreditBills') }}" min="0" autocomplete="off">
                                    </div>
                                @else
                                    <div class="form-group" style="display: none;">
                                        <label for="maxCreditBills">Max Credit Bills</label>
                                        <input type="number" class="form-control" id="maxCreditBills" name="maxCreditBills" value="0" min="0" autocomplete="off">
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label for="maxCreditAmount">Max Credit Amount</label>
                                    <input type="text" class="form-control allow_decimal" id="maxCreditAmount" name="maxCreditAmount" value="{{ old('maxCreditAmount') }}" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label for="maxCreditAvailability"> Credit Bill Availability <small>(Days)</small></label>
                                    <input type="number" class="form-control allow_decimal" id="maxCreditAvailability" name="maxCreditAvailability" value="{{ old('maxCreditAvailability') }}" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label for="deliveryRoute">Delivery Route <sup class="st-icon-pandora" style="color: #e70000;">*</sup></label><br>
                                    <select class="select2_demo_3 form-control col-md" id="deliveryRoute" name="deliveryRoute">
                                        <option value="0">-- Select Route --</option>
                                        @foreach ($routes as $route)
                                            <option value="{{ $route->id }}">{{ $route->route_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="routeOrder">Route No</label>
                                    <input type="number" class="form-control" id="routeOrder" name="routeOrder" value="{{ old('routeOrder') }}" autocomplete="off">
                                </div>


                                <div class="form-group">
                                    <label for="latitude">Latitude</label>
                                    <input type="text" class="form-control" id="latitude" name="latitude" value="{{ old('latitude') }}" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label for="longitude">Longitude</label>
                                    <input type="text" class="form-control" id="longitude" name="longitude" value="{{ old('longitude') }}" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label for="location_link">Map Location Link</label>
                                    <input type="text" class="form-control" id="location_link" name="location_link" value="{{ old('location_link') }}" autocomplete="off">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save Customer</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            {{-- modal  --}}


            {{-- add store rack count modal  --}}
            <div class="modal fade" id="addStoreRackCount" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content" id="StoreLoadDataModal">

                    </div>
                </div>
            </div>
            {{-- add store rack count modal  --}}
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox" id="loadCustomers_DIV">
                {{-- Loading content in Ajax file --}}
            </div>


            <!-- Modal -->
            <div class="modal fade" id="updateUser" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content" id="cutomerLoadDataModal">

                    </div>
                </div>
            </div>
            {{-- modal  --}}


            {{-- update customer rack  --}}
            <div class="modal fade" id="updateCustomerRack" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content" id="CustomerRackLoadDataModal">

                    </div>
                </div>
            </div>
            {{-- update customer rack  --}}
        </div>
    </div>
@endsection

@section('footer')
    <script>
        $(document).ready(function() {
            // call get all Customer funtion
            loadAllCustomersDetails();

            $(".allow_decimal").on("input", function(evt) {
                var self = $(this);
                self.val(self.val().replace(/[^0-9\.]/g, ''));
                if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
                    evt.preventDefault();
                }
            });

            // $('#createCustomer').on('shown.bs.modal', function() {
            //     $('#deliveryRoute').select2({
            //         width: '100%'
            //     });
            // });

        });


        // load all Customers Details
        function loadAllCustomersDetails() {
            var csrf_token = $("#csrf_token").val();
            jQuery.ajax({
                url: "{{ url('/loadAllCustomersDetails') }}",
                type: "POST",
                data: {
                    "_token": csrf_token
                },
                beforeSend: function() {
                    showLder();
                },
                complete: function() {
                    // hideLder();  hidden part is calling in ajax blade file
                },
                error: function(data) {
                },
                success: function(data) {
                    $('#loadCustomers_DIV').html(data);
                }
            });
        }


        // Customer Status Change
        function customerStatusChange(cusID) {
            var csrf_token = $("#csrf_token").val();
            jQuery.ajax({
                url: "{{ url('/customerdelete') }}",
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

                    if (data.msg == 'Pending Credit Invoices exists') {
                        setTimeout(function () {
                            swal({
                                title: "Cannot Deactive !",
                                text: 'This customer have Pending "Credit" Invoices.',
                                type: "warning",
                                showConfirmButton: true
                            },
                            function () {
                                // window.location.reload();
                            });
                        }, 500);
                        // swal("Cannot Deactive !", 'This customer have Pending "Credit" Invoices.', "warning");
                        // location.reload();
                    } else if (data.msg == 'Customer Status Deactivated') {
                        setTimeout(function () {
                            swal({
                                title: "Deactivated",
                                text: 'Customer Deactivated Successfully !',
                                type: "success",
                                showConfirmButton: true
                            },
                            function () {
                                window.location.reload();
                            });
                        }, 500);

                        // swal("Deactivated", "Customer Status Deactivated Successfully !", "success");
                        // location.reload();
                    } else if (data.msg == 'Customer Status Activated') {
                        setTimeout(function () {
                            swal({
                                title: "Activated",
                                text: 'Customer Activated Successfully !',
                                type: "success",
                                showConfirmButton: true
                            },
                            function () {
                                window.location.reload();
                            });
                        }, 500);

                        // swal("Activated", "Customer Activated Successfully !", "success");
                        // location.reload();
                    }
                }
            });
        }


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
                complete: function() {},
                error: function(data) {},
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
                complete: function() {},
                error: function(data) {},
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
                complete: function() {},
                error: function(data) {},
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
                complete: function() {},
                error: function(data) {},
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
                complete: function() {},
                error: function(data) {},
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


    {{-- Location Viwe Scripts --}}
    {{-- <script async defer src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap"></script> --}}
    <script>
        // function initMap() {
        //     var map = new google.maps.Map(document.getElementById('map'), {
        //         zoom: 10,
        //         center: {lat: -34.397, lng: 150.644} // Default center
        //     });

        //     var customers = <?php //echo json_encode($customers_location_list); ?>; // Pass customers from Laravel to JavaScript

        //     // Loop through customers and add markers to the map
        //     customers.forEach(function(customer) {
        //         var latLng = getLatLngFromUrl(customer.location_link); // Parse URL to get latLng
        //         var marker = new google.maps.Marker({
        //             position: latLng,
        //             map: map,
        //             title: customer.customer_name // Or any other field you have in your database representing the customer name
        //         });
        //     });
        // }

        // // Function to extract latitude and longitude from Google Maps URL
        // function getLatLngFromUrl(url) {
        //     var match = url.match(/@(-?\d+\.\d+),(-?\d+\.\d+)/);
        //     if (match) {
        //         return { lat: parseFloat(match[1]), lng: parseFloat(match[2]) };
        //     }
        //     // Return default if no match found
        //     return { lat: 0, lng: 0 };
        // }
    </script>
@endsection
