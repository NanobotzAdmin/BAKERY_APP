@extends('layout')
@section('content')

<div class="row">
    <div class="col-sm-12">
        <h2 class="font-bold">Add New Materials</h2>
        <div class="row justify-content-start">
            <div class="form-group col-md-2">
                <span for=""><br></span>
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#newMaterial">Add
                    New Raw Materials
                </button>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="newMaterial" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel">Add New Raw Materials</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <div class="form-group">
                            <label for="">Material Name</label>
                            <input type="text" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="">Product</label><br>
                            <select class="select2_demo_3 form-control">
                                    <option></option>
                                </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary">Save Material</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
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
                                <th>Material Name</th>
                                <th>Product</th>
                                <th>Available Quantity</th>
                                <th>Reorder Count</th>
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
                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal"
                                        data-target="#addStock">Add Stock</button>
                                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                        data-target="#updateStock">Update</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>



                <!-- Modal -->
                <div class="modal fade" id="addStock" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="exampleModalLabel">Add Stock to Materials</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">

                                <div class="form-group">
                                    <label for="">Material Name</label><br>
                                    <label>- Lable -</label>
                                </div>

                                <div class="form-group">
                                    <label for="">Product</label><br>
                                    <label>- Lable -</label>
                                </div>
                                <div class="form-group">
                                    <label for="">Adding Quantity</label>
                                    <input type="number" class="form-control">
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary">Add Quantity</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="updateStock" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="exampleModalLabel">Update Raw Material</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="">Material Name</label>
                                    <input type="text" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label for="">Product</label><br>
                                    <select class="select2_demo_3 form-control">
                                            <option></option>
                                        </select>
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
            $(".select2_demo_3").select2({
                placeholder: "Select a state",
                allowClear: true
            });


</script>

@endsection
