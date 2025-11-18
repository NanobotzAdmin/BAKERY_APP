@extends('layout')
@section('content')

<div class="row">
    <div class="col-sm-12">
        <h2 class="font-bold">Vehical Management</h2>
        <div class="row justify-content-start">
            <div class="form-group col-md-2">
                <span for=""><br></span>
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#createVehical">Create
                    New Vehical
                </button>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="createVehical" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel">Create New Vehical</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group">
                                <label for="rname"> Registration Number</label>
                                <input type="text" class="form-control" id="rname">
                            </div>
                            <div class="form-group">
                                <label for="eNo">Engine Number</label>
                                <input type="text" class="form-control" id="eNo">
                            </div>
                            <div class="form-group">
                                <label for="chNo">Chassis Number</label>
                                <input type="text" class="form-control" id="chNo">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary">Save Vehicle</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
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
                <h5>Manage Vehical</h5>
            </div>
            <div class="ibox-content">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Registration Name</th>
                                <th>Engine Number</th>
                                <th>Chassis Number</th>
                                <th>Status</th>
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
                                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                        data-target="#updateVehical">Update</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="updateVehical" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="exampleModalLabel">Update Vehical</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="rname"> Registration Number</label>
                                    <input type="text" class="form-control" id="rname">
                                </div>
                                <div class="form-group">
                                    <label for="eNo">Engine Number</label>
                                    <input type="text" class="form-control" id="eNo">
                                </div>
                                <div class="form-group">
                                    <label for="chNo">Chassis Number</label>
                                    <input type="text" class="form-control" id="chNo">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-warning">Update</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
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

</script>

@endsection
