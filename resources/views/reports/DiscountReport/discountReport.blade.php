@php
    $privilageId = \DB::table('pm_interfaces')
        ->select('pm_interfaces.id AS pageId', 'pm_interface_topic.id AS grupId')
        ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
        ->where('pm_interfaces.path', 'adminDiscountReport')
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

            /* checkbox css */
            .c1 { filter: hue-rotate(0deg)   }
            .c2 { filter: hue-rotate(30deg)  }
            .c3 { filter: hue-rotate(60deg)  }
            .c4 { filter: hue-rotate(90deg)  }
            .c5 { filter: hue-rotate(120deg) }
            .c6 { filter: hue-rotate(150deg) }
            .c7 { filter: hue-rotate(180deg) }
            .c8 { filter: hue-rotate(210deg) }
            .c9 { filter: hue-rotate(240deg) }

            input[type=checkbox] {
                transform: scale(2);
                /* margin: 10px; */
                cursor: pointer;
                height: 8px;
            }
        </Style>

        <div class="col-sm-12">
            <h2 class="font-bold">Discount Report</h2>
            <div class="ibox">
                <div class="ibox-content">
                    <div class="row mt-4">

                        <div class="col-lg-2">
                            <div class="form-group">
                                <label>Discount Type <small style="color: #ff0000">*</small></label>
                                <div class="form-check">
                                    <input class="form-check-input chckboxSelectDiscount c7" type="checkbox" id="loyaltyDiscount">
                                    <label class="form-check-label" for="loyaltyDiscount">Loyalty Discount</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input chckboxSelectDiscount c7" type="checkbox" id="displayDiscount">
                                    <label class="form-check-label" for="displayDiscount">Display Discount</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input chckboxSelectDiscount c7" type="checkbox" id="specialDiscount">
                                    <label class="form-check-label" for="specialDiscount">Special Discount</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input c7" type="checkbox" id="anyDiscount">
                                    <label class="form-check-label" for="anyDiscount">Any Discount</label>
                                </div>
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
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label>Vehicle</label>
                                <select class="select2_demo_3 form-control" id="vehicle">
                                    <option value="0" selected>-- Select One --</option>
                                    @if (!empty($vehicles))
                                        @foreach ($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}">{{ $vehicle->reg_number }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="button" class="btn btn-info btn-sm" onclick="getDiscountReport()"><i class="fa fa-search" aria-hidden="true"></i> &nbsp; Search</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row" id="loadDiscountReportDetails">
        {{-- table content in ajax file --}}
    </div>
@endsection


@section('footer')
    <script>
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


        // function for "Any Discount" checkbox behavior
        $(function() {
            enable_cb();
            $("#anyDiscount").click(enable_cb);
        });
        function enable_cb() {
            if (this.checked) {
                $("input.chckboxSelectDiscount").attr("disabled", true);
            } else {
                $("input.chckboxSelectDiscount").removeAttr("disabled");
            }
        }


        // date picker "Date From" Validator
        function checkDatePicker1() {
            var dateFrom = $("#dateFrom").val();
            var dateTo = $("#dateTo").val();
            if (Date.parse(dateFrom) > Date.parse(dateTo)) {
                swal("Invalid Date Range!", 'Please ensure that the "Date From" is less than or equal to the "Date To".', "warning");
                document.getElementById("dateFrom").value = "";
            }
        }
        // date picker "Date To" Validator
        function checkDatePicker2() {
            var dateFrom = $("#dateFrom").val();
            var dateTo = $("#dateTo").val();
            if (Date.parse(dateFrom) > Date.parse(dateTo)) {
                swal("Invalid Date Range!", 'Please ensure that the "Date To" is greater than or equal to the "Date From".', "warning");
                document.getElementById("dateTo").value = "";
            }
        }

        // funtion for Search "Discount Report"
        function getDiscountReport() {
            var csrf_token = $("#csrf_token").val();
            var dateFrom = $("#dateFrom").val();
            var dateTo = $("#dateTo").val();
            var vehicle = $("#vehicle").val();
            var displayDiscount = document.getElementById("displayDiscount").checked;
            var loyaltyDiscount = document.getElementById("loyaltyDiscount").checked;
            var specialDiscount = document.getElementById("specialDiscount").checked;
            var anyDiscount = document.getElementById("anyDiscount").checked;

            if (!document.getElementById("displayDiscount").checked && !document.getElementById("loyaltyDiscount").checked && !document.getElementById("specialDiscount").checked && !document.getElementById("anyDiscount").checked) {
                swal("", 'Atleast one discount type must be selected.', "warning");
            } else if (dateFrom == "" || dateFrom == null) {
                swal("", 'Please choose a Date From.', "warning");
            } else if (dateTo == "" || dateTo == null) {
                swal("", 'Please choose a Date To.', "warning");
            } else {
                jQuery.ajax({
                    url: "{{ url('/getDiscountReport') }}",
                    type: "POST",
                    data: {
                        "_token": csrf_token,
                        "dateFrom": dateFrom,
                        "dateTo": dateTo,
                        "vehicle": vehicle,
                        "displayDiscount": displayDiscount,
                        "loyaltyDiscount": loyaltyDiscount,
                        "specialDiscount": specialDiscount,
                        "anyDiscount": anyDiscount,
                    },
                    beforeSend: function() {
                        showLder();
                    },
                    complete: function() {
                    },
                    error: function(data) {
                    },
                    success: function(data) {
                        $('#loadDiscountReportDetails').html(data);
                        hideLder();
                    }
                });
            }
        }
    </script>
@endsection
