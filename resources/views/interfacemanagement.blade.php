@extends('layout')
@section('content')

<div class="row">
    <div class="col-sm-12">
        <h2 class="font-bold">Interface Management</h2>
        <div class="row">
            <div class="col-lg-4">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Add Interface Topic</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group">
                            <label>Topic Name</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Topic Icon</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Section Class</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary btn-sm pull-right">Save</button>
                        </div>
                        <br>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Add Interface </h5>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group">
                            <label>Select Topic</label>
                            <select class="select2 form-control">
                                <option></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Interface Name</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Interface URL</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Icon Class</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Title Class</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary btn-sm pull-right">Save</button>
                        </div>
                        <br>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Add Interface Component</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group">
                            <label>Select Topic</label>
                            <select class="select2 form-control">
                                <option></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Select Interface</label>
                            <select class="select2 form-control">
                                <option></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Interface Component Name</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Interface Component ID</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary btn-sm pull-right">Save</button>
                        </div>
                        <br>
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
                <h5>Update Interface Topic</h5>
            </div>
            <div class="ibox-content">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                            <tr>
                                <th>Topic Name</th>
                                <th>Topic Icon</th>
                                <th>Section Class</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal"
                                        data-target="#interfaceTopic">View</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="interfaceTopic" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="exampleModalLabel">Update Interface Topic</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Topic Name</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Topic Icon</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Section Class</label>
                                    <input type="text" class="form-control">
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


<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <div class="ibox-title">
                <h5>Update Interfaces</h5>
            </div>
            <div class="ibox-content">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                            <tr>
                                <th>Select Topic</th>
                                <th>Interface Name</th>
                                <th>Interface URL</th>
                                <th>Icon Class</th>
                                <th>Title Class</th>
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
                                        data-target="#interface">View</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="interface" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="exampleModalLabel">Update Interface</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Select Topic</label>
                                    <select class="select2 form-control">
                                        <option></option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Interface Name</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Interface URL</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Icon Class</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Title Class</label>
                                    <input type="text" class="form-control">
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


<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <div class="ibox-title">
                <h5>Update Interface Component</h5>
            </div>
            <div class="ibox-content">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                            <tr>
                                <th>Select Topic</th>
                                <th>Select Interface</th>
                                <th>Interface Component Name</th>
                                <th>Interface Component ID</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal"
                                        data-target="#interfaceComponent">View</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="interfaceComponent" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="exampleModalLabel">Update Interface Component</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Select Topic</label>
                                    <select class="select2 form-control">
                                        <option></option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Select Interface</label>
                                    <select class="select2 form-control">
                                        <option></option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Interface Component Name</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Interface Component ID</label>
                                    <input type="text" class="form-control">
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
    
</script>

@endsection