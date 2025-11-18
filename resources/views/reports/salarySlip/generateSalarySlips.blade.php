@php
    $privilageId = \DB::table('pm_interfaces')
        ->select('pm_interfaces.id AS pageId', 'pm_interface_topic.id AS grupId')
        ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
        ->where('pm_interfaces.path', 'adminGenerateSalarySlips')
        ->first();
@endphp

@extends('layout', ['pageId' => $privilageId->pageId, 'grupId' => $privilageId->grupId])
@section('content')

    <style>
        #dateFrom,
        #dateTo {
            text-align: center;
            letter-spacing: 2px;
        }

        /*placeholder css*/
        #dateFrom::placeholder,
        #dateTo::placeholder {
            font-size: 11px;
            color: #bfbfbf;
            opacity: 0.7;
            text-align: center;
            letter-spacing: 2px;
        }

        /* checkbox css */
        .c1 {
            filter: hue-rotate(0deg)
        }

        .c2 {
            filter: hue-rotate(30deg)
        }

        .c3 {
            filter: hue-rotate(60deg)
        }

        .c4 {
            filter: hue-rotate(90deg)
        }

        .c5 {
            filter: hue-rotate(120deg)
        }

        .c6 {
            filter: hue-rotate(150deg)
        }

        .c7 {
            filter: hue-rotate(180deg)
        }

        .c8 {
            filter: hue-rotate(210deg)
        }

        .c9 {
            filter: hue-rotate(240deg)
        }

        input[type=checkbox] {
            transform: scale(2);
            /* margin: 10px; */
            cursor: pointer;
            height: 8px;
        }
    </style>

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2><b>Generate Salary Slips</b></h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/admindashboard">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a>Reports</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Generate Salary Slips</strong>
                </li>
            </ol>
        </div>
    </div>
    <br>

    <div class="row">
        <div class="col-sm-12">
            <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">

            <div class="ibox">
                <div class="ibox-title">
                    <h5>Salary Generation Criteria</h5>
                </div>
                <div class="ibox-content">
                    <div class="row mt-4">
                        <div class="col-lg-3">
                            <div class="form-group" id="data_1">
                                <label>Date From <small style="color: #ff0000">*</small></label>
                                <div class="input-group date">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input
                                        type="text" class="form-control form-control-sm" id="dateFrom" maxlength="10"
                                        oninput="this.value = this.value.replace(/[^0-9/]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                        placeholder="MM/DD/YYYY" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group" id="data_1">
                                <label>Date To <small style="color: #ff0000">*</small></label>
                                <div class="input-group date">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input
                                        type="text" class="form-control form-control-sm" id="dateTo" maxlength="10"
                                        oninput="this.value = this.value.replace(/[^0-9/]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                        placeholder="MM/DD/YYYY" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Sales Rep</label>
                                <select class="select2_demo_3 form-control" id="sales">
                                    <option value="0">-- Select One --</option>
                                    @foreach ($salesRep as $salesReps)
                                        <option value="{{ $salesReps->id }}">{{ $salesReps->sales_rep_name }} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Driver</label>
                                <select class="select2_demo_3 form-control" id="driver">
                                    <option value="0">-- Select One --</option>
                                    @foreach ($drivers as $driversList)
                                        <option value="{{ $driversList->id }}">{{ $driversList->driver_name }} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-3">
                            <div class="form-group">
                                <div class="form-check" style="padding-top: 7px; left: 5px;">
                                    <input class="form-check-input c7" type="checkbox" id="deductCreditBills_check"
                                        name="deductCreditBills" value="true">
                                    <label class="form-check-label" for="deductCreditBills_check">Apply salary deductions
                                        for pending credit bills </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <button type="button" class="btn btn-info btn-sm w-100" onclick="generateSalarySlip()"><i
                                        class="fa fa-bolt" aria-hidden="true"></i> &nbsp; Generate Salary Slip</button>
                            </div>
                        </div>
                    </div>
                    <br>
                </div>
            </div>

            <div class="ibox" id="loadReportDetails">

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
                buttons: []
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


        function generateSalarySlip() {
            var csrf_token = $("#csrf_token").val();
            var dateFrom = $("#dateFrom").val();
            var dateTo = $("#dateTo").val();
            var sales = $("#sales").val();
            var driver = $("#driver").val();
            var deductCreditBills_check = $("#deductCreditBills_check").is(":checked"); // Returns true or false

            if (dateFrom == "") {
                swal("", "Please select a Date From.", "warning");
            } else if (dateTo == "") {
                swal("", "Please select a Date To.", "warning");
            } else if (driver == "0" && sales == "0") {
                swal("", "Please select a Sales Rep.", "warning");
            } else if (driver != "0" && sales != "0") {
                swal("", "Please select a Sales Rep or Driver not both.", "warning");
            } else {
                jQuery.ajax({
                    url: "{{ url('/generateSalarySlip') }}",
                    type: "POST",
                    data: {
                        "_token": csrf_token,
                        "dateFrom": dateFrom,
                        "dateTo": dateTo,
                        "sales": sales,
                        "driver": driver,
                        "deductCreditBills_check": deductCreditBills_check
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
        }
    </script>
@endsection
