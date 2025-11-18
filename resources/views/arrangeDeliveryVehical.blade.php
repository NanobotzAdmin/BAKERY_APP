@php


$privilageId = \DB::table('pm_interfaces')
->select('pm_interfaces.id AS pageId','pm_interface_topic.id AS grupId')
->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
->where('pm_interfaces.path','adminDeliveryVehicleManagement')
->first();


@endphp


@extends('layout', ['pageId' => $privilageId->pageId ,'grupId' => $privilageId->grupId ])

@section('content')

<div class="row">
    <div class="col-sm-12">
        <h2 class="font-bold">Arrange Delivery Vehical</h2><br>

        <div class="ibox">
            <div class="ibox-title">
                <h5>Arrange Delivery Vehical</h5>
            </div>
            <div class="ibox-content">
                <div class="form-group">
                    <label>Select Vehical</label><br>
                    <select class="select2_demo_3 form-control col-md-6">
                        <option></option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Select Driver</label><br>
                    <select class="select2_demo_3 form-control col-md-6">
                        <option></option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Select Sales Rep</label><br>
                    <select class="select2_demo_3 form-control col-md-6">
                        <option></option>
                    </select>
                </div>
                <div class="form-group">
                    <div class="col-md-6">
                        <button class="btn btn-primary pull-right"> Set Delivery </button>
                    </div>
                </div><br>
            </div>
        </div>
    </div>
</div>

{{-- </div> --}}

<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <div class="ibox-title">
                <h5>Delivery Vehical</h5>
            </div>
            <div class="ibox-content">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Vehical</th>
                                <th>Driver</th>
                                <th>Sales Rep</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>
                                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                        data-target="#addItems">Add Items</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="addItems" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="exampleModalLabel">Add Items</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">

                                <div class="form-group">
                                    <label for="">Select Product</label><br>
                                    <select class="select2_demo_3 form-control">
                                        <option></option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">Racks Count</label>
                                    <input type="number" class="form-control">
                                </div>
                                <div class="form-group row">
                                    <div class="form-group col-md-6">
                                        <label>Loading Qty</label>
                                        <input type="text" class="form-control">
                                    </div>
                                    <div class="form-group col-md-6" style="margin-top: 6px;">
                                        <span><br></span>
                                        <button type="button" class="btn btn-primary">Add Table</button>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-sm">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Product</th>
                                                <th>Qty</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td><button type="button" class="btn btn-danger btn-sm">Remove</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-info">Load Product</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                            </div>
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

            $(document).ready(function() {

$('#addItems').on('show.bs.modal', function() {
  $('.select2_demo_3').select2();
})


});



</script>

@endsection
