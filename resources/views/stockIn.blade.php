@php


$privilageId = \DB::table('pm_interfaces')
->select('pm_interfaces.id AS pageId','pm_interface_topic.id AS grupId')
->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
->where('pm_interfaces.path','adminStockIn')
->first();


@endphp


@extends('layout', ['pageId' => $privilageId->pageId ,'grupId' => $privilageId->grupId ])

@section('content')

<div class="row">
    <div class="col-sm-12">
        <h2 class="font-bold">Stock In</h2>
        <div class="row justify-content-start">
            <div class="form-group col-md-2">
                <span for=""><br></span>
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#newStock">Add
                    New Stock
                </button>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="newStock" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel">Enter New Stock</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="">Select Main Category</label>
                                <select class="select2 form-control">
                                    <option></option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="">Select Sub Category</label>
                                <select class="selec2 form-control">
                                    <option></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="">Select State</label>
                                <select class="selec2 form-control">
                                    <option></option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="">Adding Quantity</label>
                                <input type="text" class="form-control">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="">Selling Price</label>
                                <input type="text" class="form-control">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="">Actual Cost</label>
                                <input type="text" class="form-control">
                            </div>
                        </div>
                        <div class="form-group" id="data_1">
                            <label for="">Expire Date</label>
                            <div class="input-group date">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text"
                                    class="form-control" value="03/04/2014">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary">Add Stock</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- </div> --}}

<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <div class="ibox-title">
                <h5>Stock In</h5>
            </div>
            <div class="ibox-content">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Batch Number</th>
                                <th>Sub Category</th>
                                <th>Product Status</th>
                                <th>Available Qty</th>
                                <th>Selling Price</th>
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
                                <td></td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                        data-target="#updateStock">Update</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="updateStock" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="exampleModalLabel">Update Stock</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="">Select Main Category</label>
                                        <select class="selec2 form-control">
                                            <option></option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Select Sub Category</label>
                                        <select class="selec2 form-control">
                                            <option></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="">Select State</label>
                                        <select class="selec2 form-control">
                                            <option></option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Adding Quantity</label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="">Selling Price</label>
                                        <input type="text" class="form-control">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Actual Cost</label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group" id="data_1">
                                    <label for="">Expire Date</label>
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input
                                            type="text" class="form-control" value="03/04/2014">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-warning">Update</button>
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



</script>

@endsection
