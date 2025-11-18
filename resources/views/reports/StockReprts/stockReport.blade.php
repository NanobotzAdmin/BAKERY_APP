@php


$privilageId = \DB::table('pm_interfaces')
->select('pm_interfaces.id AS pageId','pm_interface_topic.id AS grupId')
->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
->where('pm_interfaces.path','adminStockReport')
->first();


@endphp


@extends('layout', ['pageId' => $privilageId->pageId ,'grupId' => $privilageId->grupId ])

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
        <h2 class="font-bold">Stock Report</h2>

        <div class="row mt-4">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Product <small style="color: #ff0000">*</small></label>
                                    <select class="select2_demo_3 form-control" id='product'>
                                        <option value="0">-- Select One --</option>
                                        @foreach ($stock as $stocks)
                                        <option value="{{ $stocks->id }}">{{ $stocks->sub_category_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group" id="data_1">
                                    <label>Date From</label>
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" id="dateFrom" class="form-control form-control-sm" maxlength="10" oninput="this.value = this.value.replace(/[^0-9/]/g, '').replace(/(\..*?)\..*/g, '$1');" placeholder="MM/DD/YYYY" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group" id="data_1">
                                    <label>Date To</label>
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" id="dateTo" class="form-control form-control-sm" maxlength="10" oninput="this.value = this.value.replace(/[^0-9/]/g, '').replace(/(\..*?)\..*/g, '$1');" placeholder="MM/DD/YYYY" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>&nbsp;</label><br>
                                    <button type="button" class="btn btn-info btn-sm" onclick="getStockReport()"><i class="fa fa-search" aria-hidden="true"></i> &nbsp; Search</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
  </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <div class="ibox-title">
                <h5>Stock Report</h5>
            </div>
            <div class="ibox-content">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" style="font-family: Verdana, Geneva, sans-serif;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Batch Number</th>
                                <th>Added Date</th>
                                <th>Price</th>
                                <th>Available Qty</th>
                            </tr>
                        </thead>
                        <tbody id='StockReportLoadData'>

                        </tbody>
                        <tfoot>
                            <tr style="font-weight: bold">
                                <td colspan="4" style="text-align: center">Total</td>
                                <td id="sumPriceInput">0.0</td>
                                <td id="sumQtyInput">0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="updateVehical" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content" id="vehicleLoadDataModal">

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


        function getStockReport() {
            var csrf_token = $("#csrf_token").val();
            var product = $("#product").val();
            var dateFrom = $("#dateFrom").val();
            var dateTo = $("#dateTo").val();
            if (product == 0) {
                swal("", "Please select a Product.", "warning");
            } else {
                jQuery.ajax({
                    url: "{{ url('/getStockReport') }}",
                    type: "POST",
                    data: {
                        "_token": csrf_token,
                        "product": product,
                        "dateFrom": dateFrom,
                        "dateTo": dateTo,
                    },
                    beforeSend: function() {
                        showLder();
                    },
                    complete: function() {

                    },
                    error: function(data) {

                    },
                    success: function(data) {
                        hideLder();
                        $('#StockReportLoadData').html(data);
                    }
                });
            }
        }
    </script>
@endsection
