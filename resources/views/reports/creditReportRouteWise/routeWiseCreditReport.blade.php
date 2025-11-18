@php

    $privilageId = \DB::table('pm_interfaces')
        ->select('pm_interfaces.id AS pageId', 'pm_interface_topic.id AS grupId')
        ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
        ->where('pm_interfaces.path', 'adminRouteWiseCreditReport')
        ->first();

@endphp

@extends('layout', ['pageId' => $privilageId->pageId, 'grupId' => $privilageId->grupId])

@section('content')

<Style>
    /*placeholder css*/
    #dateFrom::placeholder, #dateTo::placeholder {
        font-size: 11px;
        color: #bfbfbf;
        opacity: 0.7;
    }
</Style>

    <div class="row">
        <div class="col-sm-12">
            <h2 class="font-bold">Route Wise Credit Report</h2>

            <div class="ibox">
                <div class="ibox-content">
                    <div class="row mt-4">
                        <div class="col-lg-5">
                            <div class="form-group">
                                <label>Route</label>
                                <select class="select2_demo_3 form-control" id="route">
                                    <option value="0">-- Select One --</option>
                                    @foreach ($routes as $route)
                                        <option value="{{ $route->id }}">{{ $route->route_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-2">
                            <div class="form-group"id="data_1">
                                <label>Date From</label>
                                <div class="input-group date">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input
                                        type="text" class="form-control form-control-sm" id="dateFrom" maxlength="10" oninput="this.value = this.value.replace(/[^0-9/]/g, '').replace(/(\..*?)\..*/g, '$1');" placeholder="MM/DD/YYYY" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-2">
                            <div class="form-group"id="data_1">
                                <label>Date To</label>
                                <div class="input-group date">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input
                                        type="text" class="form-control form-control-sm" id="dateTo" maxlength="10" oninput="this.value = this.value.replace(/[^0-9/]/g, '').replace(/(\..*?)\..*/g, '$1');" placeholder="MM/DD/YYYY" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-1">
                            <label style="color: white">&nbsp;</label><br>
                            <button type="button" class="btn btn-info btn-sm" onclick="getCreditRouteReport()"><i class="fa fa-search" aria-hidden="true"></i> &nbsp; Search</button>
                        </div>
                        <div class="col-lg-1">
                            <label style="color: white">&nbsp;</label><br>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row" id="loadReportDetails">

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


        function getCreditRouteReport() {
            var csrf_token = $("#csrf_token").val();
            var dateFrom = $("#dateFrom").val();
            var dateTo = $("#dateTo").val();
            var route = $("#route").val();

            jQuery.ajax({
                url: "{{ url('/getCreditRouteReport') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "dateFrom": dateFrom,
                    "dateTo": dateTo,
                    "route": route,
                },
                beforeSend: function() {
                    showLder();
                },
                complete: function() {
                },
                error: function(data) {
                },
                success: function(data) {
                    $('#loadReportDetails').html(data);
                    hideLder();
                }
            });
        }
    </script>
@endsection
