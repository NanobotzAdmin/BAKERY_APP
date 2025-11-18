@php
    $privilageId = \DB::table('pm_interfaces')
        ->select('pm_interfaces.id AS pageId', 'pm_interface_topic.id AS grupId')
        ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
        ->where('pm_interfaces.path', 'adminviewInvoices')
        ->first();
@endphp

@extends('layout', ['pageId' => $privilageId->pageId, 'grupId' => $privilageId->grupId])
@section('content')

    <Style>
        span.select2-selection__placeholder,
        input::placeholder {
            font: 10px sans-serif;
            font-style: italic;
            color: #dbd8d0 !important;
            letter-spacing: 1.1px;
        }
    </Style>

    <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2><b>View Invoices</b></h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/admindashboard">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a>Admin Settings</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>View Invoices</strong>
                </li>
            </ol>
        </div>
    </div>
    <br>

    <div id="InvoiceContent">
        <div class="row">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-md-12">
                        <div class="ibox">
                            <div class="ibox-content">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label>Date</label>
                                        <div class="form-group" id="data_1">
                                            <div class="input-group date">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control form-control-sm" placeholder="Choose a date..." id="dateSelect" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <div style="width: 50px"></div>
                                    <div class="col-md-2">
                                        <label>Vehicle</label>
                                        <div class="form-group">
                                            <select class="select2_demo_3 form-control" id="vehicle">
                                                <option value="0">-- Select One --</option>
                                                @foreach ($vehicles as $vehicle)
                                                    <option value="{{ $vehicle->id }}">{{ $vehicle->reg_number }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div style="width: 50px"></div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label><br>
                                            <button type="button" class="btn btn-info btn-sm" onclick="getInvoices()">Search</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="row" id="loadInvoicesDiv">

        </div>

        <!-- Modal -->
        {{-- <div class="modal fade" id="invoiceNo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" id='InvoiceDataModal'>

                </div>
            </div>
        </div> --}}

        <!-- Modal -->
        <div class="modal fade" id="invoiceNo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection


@section('footer')

    <script>
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



        function getInvoices() {
            var csrf_token = $("#csrf_token").val();
            var dateSelect = $("#dateSelect").val();
            var vehicle = $("#vehicle").val();
            jQuery.ajax({
                url: "{{ url('/getInvoices') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "dateSelect": dateSelect,
                    "vehicle": vehicle,
                },
                beforeSend: function() {
                    showLder();
                },
                complete: function() {},
                error: function(data) {},
                success: function(data) {
                    $('#loadInvoicesDiv').html(data);
                    hideLder();
                }
            });

        }
    </script>
@endsection
