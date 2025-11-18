@php


$privilageId = \DB::table('pm_interfaces')
->select('pm_interfaces.id AS pageId','pm_interface_topic.id AS grupId')
->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
->where('pm_interfaces.path','adminDeliveryVehicle')
->first();


@endphp


@extends('layout', ['pageId' => $privilageId->pageId ,'grupId' => $privilageId->grupId ])

@section('content')

<div class="row">
    <div class="col-sm-12">
        <h2 class="font-bold">Add Item to Delivery Vehicle</h2>
    </div>
</div>


<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <div class="ibox-title">
                <h5>Add Items</h5>
            </div>
            <div class="ibox-content">

                <div class="form-group row">
                    <div class="form-group col-md-6">
                        <label for="">Select Main Category</label>
                        <select class="select2_demo_3 form-control">
                            <option></option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="">Select Sub Category</label>
                        <select class="select2_demo_3 form-control">
                            <option></option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="form-group col-md-6">
                        <label for="">Select Batch</label>
                        <select class="select2_demo_3 form-control">
                            <option></option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="">Qty</label>
                        <input type="text" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="form-group col-md-4">
                        <label for="">Racks Count</label>
                        <input type="number" class="form-control form-control-sm">
                    </div>
                    <div class="form-group col-md-4">
                        <span><br></span>
                        <button type="button" class="btn btn-success btn-sm" style="margin-top: 2%">Add to
                            Vehicle</button>
                    </div>
                </div>

                <hr>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Batch No</th>
                                <th>Product</th>
                                <th> Quantity</th>
                                <th>Price</th>
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
                                    <button type="button" class="btn btn-danger btn-sm">Remove</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <br>
                <div class="pull-right">
                    <button type="button" class="btn btn-primary">Load Vehicle</button>
                </div>

                <br><br>
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
         
    
</script>

@endsection