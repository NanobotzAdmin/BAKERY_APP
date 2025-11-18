@php

$privilageId = \DB::table('pm_interfaces')
    ->select('pm_interfaces.id AS pageId', 'pm_interface_topic.id AS grupId')
    ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
    ->where('pm_interfaces.path', 'adminRouteWiseCreditReport')
    ->first();

@endphp

@extends('layout', ['pageId' => $privilageId->pageId, 'grupId' => $privilageId->grupId])

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <h2 class="font-bold">Route Wise Credit Report</h2>
            <div class="ibox">
                <div class="ibox-content">
                    <div class="row mt-4">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Select Route</label>
                                <select class="select2_demo_3 form-control" id="route">
                                    <option value="0">Select One</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3">
                            <div class="form-group"id="data_1">
                                <label>Date From</label>
                                <div class="input-group date">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input
                                        type="text" class="form-control form-control-sm" id="dateFrom">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group"id="data_1">
                                <label>Date To</label>
                                <div class="input-group date">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input
                                        type="text" class="form-control form-control-sm" id="dateTo">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-1">
                            <label style="color: white">btn group</label><br>
                            <button type="button" class="btn btn-info btn-sm">Search</button>
                        </div>
                        <div class="col-lg-1">
                            <label style="color: white">btn group</label><br>
                            <button type="button" class="btn btn-success btn-sm">Print</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Invoice No</th>
                                    <th>Customer Name</th>
                                    <th> Date</th>
                                    <th>Full Amount</th>
                                    <th>Paid Amount</th>
                                    <th>Age</th>
                                    <th>Received Amount</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
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
    </script>
@endsection
