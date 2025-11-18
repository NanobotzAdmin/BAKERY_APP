@php

$privilageId = \DB::table('pm_interfaces')
    ->select('pm_interfaces.id AS pageId', 'pm_interface_topic.id AS grupId')
    ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
    ->where('pm_interfaces.path', 'adminRouteWiseSalesReport')
    ->first();

@endphp


@extends('layout', ['pageId' => $privilageId->pageId, 'grupId' => $privilageId->grupId])

@section('content')
    <div class="row">
        <Style>
            /*placeholder css*/
            #dateFrom::placeholder, #dateTo::placeholder {
                font-size: 11px;
                color: #bfbfbf;
                opacity: 0.7;
            }
        </Style>

        <div class="col-sm-12">
            <h2 class="font-bold">Route Wise Sales Report</h2>

            <div class="ibox">
                <div class="ibox-content">
                    <div class="row mt-4">

                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Route <small style="color: #ff0000">*</small></label>
                                <select class="select2_demo_3 form-control" id="route">
                                    <option value="0" disabled selected>-- Select One --</option>
                                    @foreach ($routes as $route)
                                        <option value="{{ $route->id }}">{{ $route->route_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-2">
                            <div class="form-group" id="data_1">
                                <label>Date From <small style="color: #ff0000">*</small></label>
                                <div class="input-group date">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control form-control-sm" id="dateFrom" onchange="checkDatePicker1()" maxlength="10" oninput="this.value = this.value.replace(/[^0-9/]/g, '').replace(/(\..*?)\..*/g, '$1');" placeholder="MM/DD/YYYY" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group" id="data_1">
                                <label>Date To <small style="color: #ff0000">*</small></label>
                                <div class="input-group date">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control form-control-sm" id="dateTo" onchange="checkDatePicker2()" maxlength="10" oninput="this.value = this.value.replace(/[^0-9/]/g, '').replace(/(\..*?)\..*/g, '$1');" placeholder="MM/DD/YYYY" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="button" class="btn btn-info btn-sm" onclick="getRouteWiseSalesReport()"><i class="fa fa-search" aria-hidden="true"></i> &nbsp; Search</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row" id="loadRouteWiseSalesReportDetails">
        {{-- table content in ajax file --}}
    </div>
@endsection

@section('footer')
    <script>
        $(document).ready(function() {
            $('.dataTables-example').DataTable({
                pageLength: 10,
                responsive: true,
                dom: '<"html5buttons"B>lTfgitp',
                buttons: []
            });
        });

        $(".select2_demo_3").select2({
            placeholder: "Select a Route",
            allowClear: true
        });

        var mem = $('#data_1 .input-group.date').datepicker({
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: false,
            calendarWeeks: true,
            autoclose: true
        });

        // Date picker Date From Validation
        function checkDatePicker1() {
            var dateFrom = $("#dateFrom").val();
            var dateTo = $("#dateTo").val();
            if (Date.parse(dateFrom) > Date.parse(dateTo)) {
                swal("Invalid Date Range!", 'Please ensure that the "Date From" is less than or equal to the "Date To".', "warning");
                document.getElementById("dateFrom").value = "";
            }
        }
        // Date picker Date To Validation
        function checkDatePicker2() {
            var dateFrom = $("#dateFrom").val();
            var dateTo = $("#dateTo").val();
            if (Date.parse(dateFrom) > Date.parse(dateTo)) {
                swal("Invalid Date Range!", 'Please ensure that the "Date To" is greater than or equal to the "Date From".', "warning");
                document.getElementById("dateTo").value = "";
            }
        }

        // Funtion for getting Route Wise Sales Report Details
        function getRouteWiseSalesReport() {
            var csrf_token = $("#csrf_token").val();
            var dateFrom = $("#dateFrom").val();
            var dateTo = $("#dateTo").val();
            var route = $("#route").val();

            if (route == "0" || route == "" || route == null) {
                swal("", 'Please select a Route.', "warning");
            } else if (dateFrom == "" || dateFrom == null) {
                swal("", 'Please select a Date From.', "warning");
            } else if (dateTo == "" || dateTo == null) {
                swal("", 'Please select a Date To.', "warning");
            } else {
                jQuery.ajax({
                    url: "{{ url('/getRouteWiseSalesReport') }}",
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
                        $('#loadRouteWiseSalesReportDetails').html(data);
                        hideLder();
                    }
                });
            }
        }
    </script>
@endsection
