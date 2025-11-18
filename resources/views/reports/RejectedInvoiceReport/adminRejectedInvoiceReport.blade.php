@php

    $privilageId = \DB::table('pm_interfaces')
        ->select('pm_interfaces.id AS pageId', 'pm_interface_topic.id AS grupId')
        ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
        ->where('pm_interfaces.path', 'adminRejectedInvoiceReport')
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

    <div class="row">
        <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">

        <div class="col-sm-12">
            <h2 class="font-bold">Rejected Invoice Report</h2>

            <div class="ibox">
                <div class="ibox-title">
                    <h5>Search Criteria</h5>
                </div>
                <div class="ibox-content">
                    <div class="row mt-4">
                        <div class="col-lg-3">
                            <div class="form-group" id="data_1">
                                <label>Date From</label>
                                <div class="input-group date">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" class="form-control form-control-sm" id="dateFrom" maxlength="10"
                                        oninput="this.value = this.value.replace(/[^0-9/]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                        placeholder="MM/DD/YYYY" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3">
                            <div class="form-group" id="data_1">
                                <label>Date To</label>
                                <div class="input-group date">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" class="form-control form-control-sm" id="dateTo" maxlength="10"
                                        oninput="this.value = this.value.replace(/[^0-9/]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                        placeholder="MM/DD/YYYY" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Invoice Type</label><br>
                                <select class="select2_demo_3 form-control" id="invoiceType" style="width: 100%">
                                    <option value="0">-- All --</option>
                                    <option value="1">Credit</option>
                                    <option value="2">Cash</option>
                                    <option value="3">Cheque</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3 d-flex align-items-end">
                            <div class="form-group w-100">
                                <button type="button" class="btn btn-info btn-sm w-100"
                                        onclick="getRejectedInvoiceReport()">
                                    <i class="fa fa-search" aria-hidden="true"></i> &nbsp; Search
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row" style="display: none;">
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Vehicle</label>
                                <select class="select2_demo_3 form-control" id="vehicle">
                                    <option value="0">-- All --</option>
                                    <option value="-1">⚠️ Get All Unassigned ⚠️</option>
                                    @foreach ($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}">{{ $vehicle->reg_number }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Sales Representative</label>
                                <select class="select2_demo_3 form-control" id="salesRep">
                                    <option value="0">-- All --</option>
                                    <option value="-1">⚠️ Get All Unassigned ⚠️</option>
                                    @foreach ($salesRep as $salesReps)
                                        <option value="{{ $salesReps->id }}">{{ $salesReps->sales_rep_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row" id="loadRejectedInvoiceReportDetails">
        {{-- Load data here with Ajax - REPORT DETAILS --}}
    </div>


    <!-- Modal -->
    <div class="modal fade" id="updateVehical" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content" id="vehicleLoadDataModal">

            </div>
        </div>
    </div>



    <!-- Modal -->
    <div class="modal fade" id="invoiceNo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="exampleModalLabel">Invoice Details</h3>
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



    <div class="modal fade" id="invoiceHistoryPayment" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="exampleModalLabel">Invoice Payment History</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id='InvoicePaymentHistory'>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    <script>
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


        function getRejectedInvoiceReport() {
            var csrf_token = $("#csrf_token").val();
            var invoiceType = $("#invoiceType").val();
            var dateFrom = $("#dateFrom").val();
            var dateTo = $("#dateTo").val();
            var vehicle = $("#vehicle").val();
            var salesRep = $("#salesRep").val();
            var drivers = $("#drivers").val();

            jQuery.ajax({
                url: "{{ url('/getRejectedInvoiceReport') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "invoiceType": invoiceType,
                    "dateFrom": dateFrom,
                    "dateTo": dateTo,
                    "vehicle": vehicle,
                    "salesRep": salesRep
                },
                beforeSend: function() {
                    showLder();
                },
                complete: function() {},
                error: function(data) {},
                success: function(data) {
                    hideLder();
                    $('#loadRejectedInvoiceReportDetails').html(data);
                }
            });
        }
    </script>
@endsection
