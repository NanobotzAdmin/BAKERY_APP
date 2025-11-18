@php

    $privilageId = \DB::table('pm_interfaces')
        ->select('pm_interfaces.id AS pageId', 'pm_interface_topic.id AS grupId')
        ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
        ->where('pm_interfaces.path', 'adminCollectionReport')
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
            <h2 class="font-bold">Collection Report</h2><br>
            <div class="row">
                <div class="col-md-12">
                    <div class="ibox">
                        <div class="ibox-content">
                            {{-- <div class="row"> --}}

                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <label>Date From <small style="color: #ff0000">*</small></label>
                                    <div class="form-group" id="data_1">
                                        <div class="input-group date">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input
                                                type="text" class="form-control form-control-sm" id="dateFrom" maxlength="10" oninput="this.value = this.value.replace(/[^0-9/]/g, '').replace(/(\..*?)\..*/g, '$1');" placeholder="MM/DD/YYYY" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div style="width: 30px;"></div>
                                <div class="form-group col-md-2">
                                    <label>Date To <small style="color: #ff0000">*</small></label>
                                    <div class="form-group" id="data_1">
                                        <div class="input-group date">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input
                                                type="text" class="form-control form-control-sm" id="dateTo" maxlength="10" oninput="this.value = this.value.replace(/[^0-9/]/g, '').replace(/(\..*?)\..*/g, '$1');" placeholder="MM/DD/YYYY" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div style="width: 30px;"></div>
                                <div class="form-group col-md-2">
                                    <label>Vehicle</label>
                                    <select class="select2_demo_3 form-control" id="vehicle">
                                        <option value="0">-- Select One --</option>
                                        @foreach ($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}">{{ $vehicle->reg_number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div style="width: 30px;"></div>
                                <div class="form-group col-md-2">
                                    <label>Sales Rep</label>
                                    <select class="select2_demo_3 form-control" id="salesRep">
                                        @if (session('user_type') == '3')
                                            <?php
                                                $salesRep2 = App\SaleRep::where('um_user_id', session('logged_user_id'))->first();
                                            ?>
                                            <option value="{{ $salesRep2->id }}">{{ $salesRep2->sales_rep_name }}</option>
                                        @else
                                            <option value="0">-- Select One --</option>
                                            @foreach ($salesRep as $salesRep1)
                                                <option value="{{ $salesRep1->id }}">{{ $salesRep1->sales_rep_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div style="width: 30px;"></div>
                                <div class="form-group col-md-1 mt-4">
                                    <button type="button" class="btn btn-info btn-sm" onclick="getCollectionReport()"><i class="fa fa-search" aria-hidden="true"></i> &nbsp; Search</button>
                                </div>
                            </div>


                            {{-- <div class="col-md-1">
                <div class="form-group">
                  <label>Select Date</label>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group" id="data_1">
                  <div class="input-group date">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text"
                      class="form-control form-control-sm" value="" id="dateSelect">
                  </div>
                </div>
              </div> --}}

                            {{-- <div class="col-md-1">
                <div class="form-group">
                <label>Select Vehicle</label>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                    <select class="select2_demo_3 form-control" id="vehicle">
                        <option value="0">Select One</option>
                        @foreach ($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}">{{ $vehicle->reg_number }}</option>
                        @endforeach
                    </select>
                </div>
              </div> --}}
                            {{-- <div class="col-md-1">
                <div class="form-group">
                <label>Select Sales Rep</label>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                    <select class="select2_demo_3 form-control" id="salesRep">
                        <option value="0">Select One</option>
                        @foreach ($salesRep as $salesRep1)
                        <option value="{{ $salesRep1->id }}">{{ $salesRep1->sales_rep_name }}</option>
                        @endforeach
                    </select>
                </div>
              </div> --}}
                            {{-- <div class="col-md-2">
                <div class="form-group">
                  <button type="button" class="btn btn-info" onclick="getCollectionReport()">Search</button>
                </div>
              </div> --}}


                            {{-- </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="collectionReportViewDiv">

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

        function getCollectionReport() {
            var csrf_token = $("#csrf_token").val();
            var dateFrom = $("#dateFrom").val();
            var dateTo = $("#dateTo").val();
            var vehicle = $("#vehicle").val();
            var salesRep = $("#salesRep").val();

            if (dateFrom == '') {
                swal("", "Please select a Date From.", "warning");
            } else if (dateTo == '') {
                swal("", "Please select a Date To.", "warning");
            } else {
                jQuery.ajax({
                    url: "{{ url('/getCollectionReport') }}",
                    type: "POST",
                    data: {
                        "_token": csrf_token,
                        "dateFrom": dateFrom,
                        "dateTo": dateTo,
                        "vehicle": vehicle,
                        "salesRep": salesRep
                    },
                    beforeSend: function() {
                        showLder();
                    },
                    complete: function() {

                    },
                    error: function(data) {

                    },
                    success: function(data) {
                        $('#collectionReportViewDiv').html(data);
                        hideLder();
                    }
                });
            }
        }
    </script>
@endsection
