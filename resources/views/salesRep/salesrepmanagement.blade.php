@php
    $privilageId = \DB::table('pm_interfaces')
    ->select('pm_interfaces.id AS pageId','pm_interface_topic.id AS grupId')
    ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
    ->where('pm_interfaces.path','adminSalesRepManagement')
    ->first();
@endphp

@extends('layout', ['pageId' => $privilageId->pageId ,'grupId' => $privilageId->grupId ])

@section('content')

<style>
    span.activeStatusDot{
        display: inline-block; /* or block */
        height: 8px;
        width: 8px;
        vertical-align: 0px;
        background: #00e01e;
        box-shadow: 0 0 6px #00dd1d;
        border-radius: 50%;
    }
    span.deactiveStatusDot{
        display: inline-block; /* or block */
        height: 8px;
        width: 8px;
        vertical-align: 0px;
        background: #e70000;
        box-shadow: 0 0 5px #ff0000;
        border-radius: 50%;
    }

    .table-hover tbody tr:hover {
        background-color: #faf6ec;
        color: #000;
        /* Light blue color - adjust as needed */
        transition: background-color 0.2s;
        /* Add a smooth transition effect */
    }

    .table th {
        text-align: center; /* Horizontally center the text */
        vertical-align: middle !important; /* Vertically center the text */
    }
</style>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2><b>Sales Rep Management</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="/admindashboard">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a>People Management</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Sales Rep Management</strong>
            </li>
        </ol>
    </div>
</div>


<div class="row">
    <div class="col-sm-12">
        <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">

        @include('include.flash')
        @include('include.errors')

        <div class="row justify-content-start">
            <div class="form-group col-md-2">
                <span for=""></span>
                {{-- <button type="button" class="btn btn-success" data-toggle="modal" data-target="#createSaleRep">Create
                    New Sales Rep
                </button> --}}
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="createSaleRep" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel">Create New Sales Rep</h4>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="saveSaleRep" method="POST">
                            {{ csrf_field() }}
                    <div class="modal-body">

                            <div class="form-group">
                                <label for="srname"> Sales First Name</label>
                                <input type="text" class="form-control" id="srname" name="firstName" value="{{old('firstName')}}">
                            </div>
                            <div class="form-group">
                                <label for="srname"> Sales Last Name</label>
                                <input type="text" class="form-control" id="srname" name="lastName" value="{{old('lastName')}}">
                            </div>
                            <div class="form-group">
                                <label for="nic">NIC Number</label>
                                <input type="text" class="form-control" id="nic" name="repNic" value="{{old('repNic')}}">
                            </div>
                            <div class="form-group">
                                <label for="cNo">Contact Number</label>
                                <input type="text" class="form-control" id="cNo" name="repContact" value="{{old('repContact')}}">
                            </div>
                            <div class="form-group">
                                <label for="uname">User Name</label>
                                <input type="text" class="form-control" id="uname" name="uname" value="{{old('uname')}}">
                            </div>
                            <div class="form-group">
                                <label for="pass">Password</label>
                                <input type="password" class="form-control" id="pass" name="pass" value="{{old('pass')}}">
                            </div>
                            <div class="form-group">
                                <label for="cpass">Confirm Password</label>
                                <input type="password" class="form-control" id="cpass" name="confirmPass" value="{{old('confirmPass')}}">
                            </div>


                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <div class="ibox-title">
                <h5>Manage Sales Rep</h5>
            </div>
            <div class="ibox-content">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover dataTables-example" style="font-family: 'Lato', sans-serif;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>NIC</th>
                                <th>Contact Number</th>
                                <th>Username</th>
                                <th>Password</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $id = 0;?>
                            @foreach ($saleRepList as $saleRep)
                            <?php $id++;
                            $user = App\User::find($saleRep->um_user_id);
                            $login = App\UserLogin::find($user->um_user_login_id);
                            ?>
                            <tr>
                                <td>{{ $id }}</td>
                                <td>{{ $saleRep->sales_rep_name }}</td>
                                <td>{{ $saleRep->nic_no }}</td>
                                <td>{{ $saleRep->contact_no }}</td>
                                <td>{{ $login->user_name }}</td>
                                <td>{{ $login->password }}</td>
                                @if ($saleRep->is_active == 1)
                                    <td style="min-width: 90px; color: #1ab394; text-align: center;"><span class="badge" style="color: #28a745; background-color: #e2f5e6;">Active</span></td>
                                @else
                                    <td style="min-width: 90px; color: #e70000; text-align: center;"><span class="badge" style="color: #dc3545; background-color: #fceff0;">Inactive</span></td>
                                @endif
                                <td>
                                    <button type="button" class="btn btn-outline-warning btn-xs" data-toggle="modal" data-target="#updateSalesRep" onclick="showSaleEditModal({{ $saleRep->id  }})"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp; Update</button>

                                    @if($saleRep->is_active == App\STATIC_DATA_MODEL::$Active)
                                        <a href="/deleteSalesRep/{{  $saleRep->id }}"> <button type="button" class="btn btn-outline-danger btn-xs">Deactivate</button></a>
                                    @else
                                        <a href="/deleteSalesRep/{{  $saleRep->id }}"> <button type="button" class="btn btn-outline-success btn-xs">&nbsp;&nbsp; Activate &nbsp;&nbsp;</button></a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="updateSalesRep" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content" id="modelContentSaleRep">

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


    function showSaleEditModal(RepId){
        var csrf_token = $("#csrf_token").val();
        jQuery.ajax({
            url: "{{ url('/loadSaleRepDataToModal') }}",
            type: "POST",
            data: {
                "_token": csrf_token,
                "RepId": RepId,
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
                $('#modelContentSaleRep').html(data);
            }
        });
    }
</script>
@endsection
