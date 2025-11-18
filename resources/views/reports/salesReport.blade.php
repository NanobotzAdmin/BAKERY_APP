@php


$privilageId = \DB::table('pm_interfaces')
->select('pm_interfaces.id AS pageId','pm_interface_topic.id AS grupId')
->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
->where('pm_interfaces.path','salesReport')
->first();


@endphp


@extends('layout', ['pageId' => $privilageId->pageId ,'grupId' => $privilageId->grupId ])

@section('content')

<div class="row">
    <div class="col-sm-12">
        <h2 class="font-bold">Sales Report</h2>

        <div class="row mt-4">
            <div class="col-lg-3">
                <div class="form-group">
                    <label>Select Customer</label>
                    <select class="select2_demo_3 form-control">
                        <option></option>
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <label>Invoice Type</label>
                    <select class="select2_demo_3 form-control">
                        <option></option>
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group"id="data_1">
                    <label>Date From</label>
                    <div class="input-group date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control form-control-sm" value="03/04/2014">
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group"id="data_1">
                    <label>Date To</label>
                    <div class="input-group date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control form-control-sm" value="03/04/2014">
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="form-group">
                    <label>Select Vehicle</label>
                    <select class="select2_demo_3 form-control">
                        <option></option>
                    </select>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label>Select Sales rep</label>
                    <select class="select2_demo_3 form-control">
                        <option></option>
                    </select>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label>Select Driver</label>
                    <select class="select2_demo_3 form-control">
                        <option></option>
                    </select>
                </div>
            </div>
        </div>

    </div>

</div>

<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <div class="ibox-title">
                <h5>Sales Report</h5>
            </div>
            <div class="ibox-content">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Customer Name</th>
                                <th>Invoice No</th>
                                <th> Date</th>
                                <th>Invoice Type</th>
                                <th>Invoice Amount</th>
                                <th>Paid Amount</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                            <tr style="font-weight: bold">
                               <td colspan="5" style="text-align: center">Total</td>
                                <td>Total</td>
                                <td>Total</td>
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
    $(document).ready(function(){
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
