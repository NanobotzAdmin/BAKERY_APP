@php
    $privilageId = \DB::table('pm_interfaces')
        ->select('pm_interfaces.id AS pageId', 'pm_interface_topic.id AS grupId')
        ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
        ->where('pm_interfaces.path', 'adminPaymentReverse')
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

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2><b>Payment Reversal</b></h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/admindashboard">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a>Distribution</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Payment Reversal</strong>
                </li>
            </ol>
        </div>
    </div>
    <br>

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Search Criteria</h5>
                </div>
                <div class="ibox-content">
                    <div class="form-group row">
                        <div class="form-group col-md-4">
                            <label for="">Customer</label>
                            <select class="select2_demo_3 form-control" id="customer">
                                <option value="0">-- Select One --</option>
                                @foreach ($customerList as $customers)
                                    <option value="{{ $customers->id }}">{{ $customers->customer_name }}</option>
                                @endforeach

                            </select>
                        </div>

                        {{-- <div class="form-group col-md-3">
                        <label for="">Invoice Type</label>
                        <select class="select2_demo_3 form-control" id="invoiceType">
                            <option value="ALL">Select One </option>
                            <option value="0">Pending </option>
                            <option value="1">Completed </option>
                        </select>
                    </div> --}}

                        <div class="form-group col-md-2" id="data_1">
                            <label class="font-normal">Date From</label>
                            <div class="input-group date">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control form-control-sm" value="" id="dateFrom" maxlength="10" oninput="this.value = this.value.replace(/[^0-9/]/g, '').replace(/(\..*?)\..*/g, '$1');" placeholder="MM/DD/YYYY" autocomplete="off">
                            </div>
                        </div>

                        <div class="form-group col-md-2" id="data_1">
                            <label class="font-normal">Date To</label>
                            <div class="input-group date">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control form-control-sm" value="" id="dateTo" maxlength="10" oninput="this.value = this.value.replace(/[^0-9/]/g, '').replace(/(\..*?)\..*/g, '$1');" placeholder="MM/DD/YYYY" autocomplete="off">
                            </div>
                        </div>

                        <div class="form-group col-md-1">
                            <label class="font-normal">&nbsp;</label><br>
                            <button type="button" class="btn btn-info btn-sm" onclick="searchInvoices()"><i class="fa fa-search" aria-hidden="true"></i> &nbsp; Search</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-12">
            <div class="ibox" id="tblReverseInvoices">

            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="view" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" id="loadInvoiceModalArea">

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


        function searchInvoices() {
            var csrf_token = $("#csrf_token").val();
            jQuery.ajax({
                url: "{{ url('/searchInvoices') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "customer": $("#customer").val(),
                    "dateFrom": $("#dateFrom").val(),
                    "dateTo": $("#dateTo").val(),
                },
                beforeSend: function() {
                    showLder();
                },
                complete: function() {
                },
                error: function(data) {
                },
                success: function(data) {
                    // hideLder();
                    $("#tblReverseInvoices").html(data);
                }
            });
        }

    </script>
@endsection
