@php

    $privilageId = \DB::table('pm_interfaces')
        ->select('pm_interfaces.id AS pageId', 'pm_interface_topic.id AS grupId')
        ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
        ->where('pm_interfaces.path', 'adminDailySalesReport')
        ->first();

@endphp


@extends('layout', ['pageId' => $privilageId->pageId, 'grupId' => $privilageId->grupId])

@section('content')

<style>
    /*placeholder css*/
    #dateFrom::placeholder, #dateTo::placeholder {
        font-size: 11px;
        color: #bfbfbf;
        opacity: 0.7;
    }
</style>

    <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">

    <div class="row">
        <div class="col-sm-12">
            <h2 class="font-bold">Daily Sales Report</h2>
            <div class="ibox">
                <div class="ibox-content">
                    <div class="row mt-4">
                        <div class="col-lg-3">
                            <div class="form-group" id="data_1">
                                <label>Date From <small style="color: #ff0000">*</small></label>
                                <div class="input-group date">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input
                                        type="text" class="form-control form-control-sm" id="dateFrom" maxlength="10" oninput="this.value = this.value.replace(/[^0-9/]/g, '').replace(/(\..*?)\..*/g, '$1');" placeholder="MM/DD/YYYY" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group" id="data_1">
                                <label>Date To <small style="color: #ff0000">*</small></label>
                                <div class="input-group date">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input
                                        type="text" class="form-control form-control-sm" id="dateTo" maxlength="10" oninput="this.value = this.value.replace(/[^0-9/]/g, '').replace(/(\..*?)\..*/g, '$1');" placeholder="MM/DD/YYYY" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Customer</label>
                                <select class="select2_demo_3 form-control" id="customer">
                                    <option value="0">-- Select One --</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Invoice Type</label>
                                <select class="select2_demo_3 form-control" id="invoiceType">
                                    <option value="0">-- Select One --</option>
                                    <option value="2">Cash</option>
                                    <option value="3">Cheque</option>
                                    <option value="1">Credit</option>

                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Vehicle <small style="color: #ff0000">*</small></label>
                                <select class="select2_demo_3 form-control" id="vehicle">
                                    <option value="0">-- Select One --</option>
                                    @foreach ($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}">{{ $vehicle->reg_number }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Sales rep</label>
                                <select class="select2_demo_3 form-control" id="salesRep">

                                    @if (session('user_type') == '3')
                                        <?php
                                            $salesRep2 = App\SaleRep::where('um_user_id', session('logged_user_id'))->first();
                                        ?>
                                        <option value="{{ $salesRep2->id }}">{{ $salesRep2->sales_rep_name }}</option>
                                    @else
                                        <option value="0">-- Select One --</option>
                                        @foreach ($salesRep as $salesReps)
                                            <option value="{{ $salesReps->id }}">{{ $salesReps->sales_rep_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                @if (session('user_type') == '3')
                                    <div style="display: none">
                                        <select class="select2_demo_3 form-control" id="drivers">
                                            <option value="0">-- Select One --</option>

                                        </select>
                                    </div>
                                @else
                                    <label>Driver</label>
                                    <select class="select2_demo_3 form-control" id="drivers">
                                        <option value="0">-- Select One --</option>
                                        @foreach ($drivers as $driver)
                                            <option value="{{ $driver->id }}">{{ $driver->driver_name }}</option>
                                        @endforeach
                                @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <button type="button" class="btn btn-info btn-sm pull-right" onclick="getSalesReportDaily()"><i class="fa fa-search" aria-hidden="true"></i> &nbsp; Search</button>
                    </div>
                    <br>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">

                <div class="ibox-title">
                    <h5>Sales Report</h5>
                </div>
                <div class="ibox-content">
                    <div id="loadSalesReportDetails">

                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="invoiceNo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3 class="modal-title" id="exampleModalLabel">Invoice No</h3>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body" id='InvoiceDataModal'>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Modal -->
                    <div class="modal fade" id="updateVehical" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content" id="vehicleLoadDataModal2">

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
                pageLength: 10,
                responsive: true,
                dom: '<"html5buttons"B>lTfgitp',
                buttons: [
                ]
            });
        });


        $(".select2_demo_3").select2({
            placeholder: "Select a state",
            allowClear: true
        });

        var mem = $('#data_1 .input-group.date').datepicker({
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: false,
            calendarWeeks: true,
            autoclose: true
        });



        function getSalesReportDaily() {
            var csrf_token = $("#csrf_token").val();
            var dateFrom = $("#dateFrom").val();
            var dateTo = $("#dateTo").val();
            var vehicle = $("#vehicle").val();
            var salesRep = $("#salesRep").val();
            var drivers = $("#drivers").val();
            var customer = $("#customer").val();
            var invoiceType = $("#invoiceType").val();

            if (dateFrom == "") {
                swal("", "Please select a Date From.", "warning");
            } else if (dateTo == "") {
                swal("", "Please select a Date To.", "warning");
            } else if (vehicle == '0') {
                swal("", "Please select a Vehicle.", "warning");
            } else {
                jQuery.ajax({
                    url: "{{ url('/getSalesReportDaily') }}",
                    type: "POST",
                    data: {
                        "_token": csrf_token,
                        "dateFrom": dateFrom,
                        "dateTo": dateTo,
                        "vehicle": vehicle,
                        "salesRep": salesRep,
                        "drivers": drivers,
                        "customer": customer,
                        "invoiceType": invoiceType,
                    },
                    beforeSend: function() {
                        showLder();
                    },
                    complete: function() {

                    },
                    error: function(data) {

                    },
                    success: function(data) {
                        $('#loadSalesReportDetails').html(data);
                        hideLder();
                    }
                });
            }
        }
    </script>
@endsection
