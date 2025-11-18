@extends('layout')
@section('content')

<div class="row">
    <div class="col-sm-12">
        <h2 class="font-bold">Drivers Management</h2>
        <div class="row justify-content-start">
            <div class="form-group col-md-2">
                <span for=""><br></span>
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#createDriver">Create
                    New Driver
                </button>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="createDriver" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel">Create New Driver</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group">
                                <label for="dname"> Driver Name</label>
                                <input type="text" class="form-control" id="dname">
                            </div>
                            <div class="form-group">
                                <label for="licence">Licence Number</label>
                                <input type="text" class="form-control" id="licence">
                            </div>
                            <div class="form-group">
                                <label for="ldate">Licence Expiration Date</label>
                                <input type="date" class="form-control" id="ldate">
                            </div>
                            <div class="form-group">
                                <label for="cNo">Contact Number</label>
                                <input type="text" class="form-control" id="cNo">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
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
                <h5>Manage Drivers</h5>
            </div>
            <div class="ibox-content">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Licence Number</th>
                                <th>Licence Expiration Date</th>
                                <th>Contact Number</th>
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
                                <td></td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                        data-target="#updateDriver">Update</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="updateDriver" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="exampleModalLabel">Update Driver</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="dname"> Driver Name</label>
                                    <input type="text" class="form-control" id="dname">
                                </div>
                                <div class="form-group">
                                    <label for="licence">Licence Number</label>
                                    <input type="text" class="form-control" id="licence">
                                </div>
                                <div class="form-group">
                                    <label for="ldate">Licence Expiration Date</label>
                                    <input type="date" class="form-control" id="ldate">
                                </div>
                                <div class="form-group">
                                    <label for="cNo">Contact Number</label>
                                    <input type="text" class="form-control" id="cNo">
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
