@php

    $privilageId = \DB::table('pm_interfaces')
        ->select('pm_interfaces.id AS pageId', 'pm_interface_topic.id AS grupId')
        ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
        ->where('pm_interfaces.path', 'adminUserManagement')
        ->first();
@endphp

@extends('layout', ['pageId' => $privilageId->pageId, 'grupId' => $privilageId->grupId])

@section('content')

    <style>
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



        span.activeStatusDot {
            display: inline-block;
            /* or block */
            height: 8px;
            width: 8px;
            vertical-align: 0px;
            background: #00e01e;
            box-shadow: 0 0 6px #00dd1d;
            border-radius: 50%;
        }

        span.deactiveStatusDot {
            display: inline-block;
            /* or block */
            height: 8px;
            width: 8px;
            vertical-align: 0px;
            background: #e70000;
            box-shadow: 0 0 5px #ff0000;
            border-radius: 50%;
        }

        /* checkbox css */
        .c1 { filter: hue-rotate(0deg)   }
        .c2 { filter: hue-rotate(30deg)  }
        .c3 { filter: hue-rotate(60deg)  }
        .c4 { filter: hue-rotate(90deg)  }
        .c5 { filter: hue-rotate(120deg) }
        .c6 { filter: hue-rotate(150deg) }
        .c7 { filter: hue-rotate(180deg) }
        .c8 { filter: hue-rotate(210deg) }
        .c9 { filter: hue-rotate(240deg) }

        input[type=checkbox] {
              transform: scale(2);
              /* margin: 10px; */
              cursor: pointer;
              height: 8px;
        }
    </style>

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2><b>User Management</b></h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/admindashboard">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a>People Management</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>User Management</strong>
                </li>
            </ol>
        </div>
    </div>
    <br>

    <div class="row">
        <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">

        <div class="col-sm-12">
            @include('include.flash')
            @include('include.errors')

            <!-- Modal -->
            <div class="modal fade" id="createUser" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="exampleModalLabel">Registration</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="saveUser">
                                {{ csrf_field() }}
                                <input type="hidden" id="hid_va_tbl" name="hid_va_tbl" />
                                <div class="form-group">
                                    <label for="fname">First Name</label>
                                    <input type="text" class="form-control" id="fname" name="fname"
                                        value="{{ old('fname') }}" autocomplete="off"/>
                                </div>
                                <div class="form-group">
                                    <label for="lname">Last Name</label>
                                    <input type="text" class="form-control" id="lname" name="lname"
                                        value="{{ old('lname') }}" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label for="userRole">User Role</label>
                                    <select class="select2_demo_1 form-control" name="userRole"
                                        value="{{ old('userRole') }}">
                                        @foreach ($userRoles as $userRole)
                                            <option value="{{ $userRole->id }}">{{ $userRole->user_role_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="uname">User Name</label>
                                    <input type="text" class="form-control" id="uname" name="uname"
                                        value="{{ old('uname') }}" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label for="pass">Password</label>
                                    <input type="password" class="form-control" id="pass" name="pass"
                                        value="{{ old('pass') }}" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label for="cpass">Confirm Password</label>
                                    <input type="password" class="form-control" id="cpass" name="confirmPass"
                                        value="{{ old('confirmPass') }}" autocomplete="off">
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input chckboxSelectContact c7" type="checkbox" value="2"
                                        id="defaultCheck1" checked>
                                    <label class="form-check-label" for="defaultCheck1">
                                        Cash Invoice Allowed
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input chckboxSelectContact c7" type="checkbox" value="1"
                                        id="defaultCheck2" checked>
                                    <label class="form-check-label" for="defaultCheck2">
                                        Credit Invoice Allowed
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input chckboxSelectContact c7" type="checkbox" value="3"
                                        id="defaultCheck3" checked>
                                    <label class="form-check-label" for="defaultCheck3">
                                        Check Invoice Allowed
                                    </label>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Register</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
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
                    <h5>Manage Users</h5>
                </div>
                <div class="ibox-content">
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#createUser">Create New User</button>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover dataTables-example" style="font-family: 'Lato', sans-serif;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>User Role</th>
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <?php
                                    $userRole = App\UserRole::find($user->pm_user_role_id);
                                    $userName = App\UserLogin::find($user->um_user_login_id);
                                    ?>
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $user->first_name }}</td>
                                        <td>{{ $user->last_name }}</td>
                                        <td>{{ $userRole->user_role_name }}</td>
                                        <td>{{ $userName->user_name }}</td>
                                        <td>{{ $userName->password }}</td>
                                        @if ($user->is_active == 1)
                                            <td style="min-width: 90px; color: #1ab394; text-align: center;"><span class="badge" style="color: #28a745; background-color: #e2f5e6;">Active</span></td>
                                        @elseif($user->is_active == 0)
                                            <td style="min-width: 90px; color: #e70000; text-align: center;"><span class="badge" style="color: #dc3545; background-color: #fceff0;">Inactive</span></td>
                                        @endif
                                        <td>
                                            <button type="button" class="btn btn-outline-warning btn-xs" data-toggle="modal" data-target="#updateUser" onclick='showuserDataModel({{ $user->id }})'><i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp; Update</button>
                                            &nbsp;
                                            @if ($user->is_active == App\STATIC_DATA_MODEL::$Active)
                                                <a href="/deleteUser/{{ $user->id }}"> <button type="button" class="btn btn-outline-danger btn-xs">Deactivate</button></a>
                                            @else
                                                <a href="/deleteUser/{{ $user->id }}"> <button type="button" class="btn btn-outline-success btn-xs">&nbsp; Activate &nbsp;&nbsp;</button></a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="updateUser" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content" id="userRegistrationLoadDataModal">

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
                pageLength: 25,
                responsive: true,
                dom: '<"html5buttons"B>lTfgitp',
                buttons: []

            });

            loadinvoiceValues();
        });


        function loadinvoiceValues() {
            var data = [];
            $(".chckboxSelectContact").each(function(i, v) {
                if ($(v).prop('checked')) {
                    data.push($(v).val());
                }
            });
            $("#hid_va_tbl").val(JSON.stringify(data));
        }


        $(".chckboxSelectContact").click(function() {
            $("#hid_va_tbl").val("");
            var data = [];
            $(".chckboxSelectContact").each(function(i, v) {
                if ($(v).prop('checked')) {
                    data.push($(v).val());
                }
            });
            $("#hid_va_tbl").val(JSON.stringify(data));
        });


        function showuserDataModel(userId) {
            var csrf_token = $("#csrf_token").val();
            jQuery.ajax({
                url: "{{ url('/viewUserDataToModal') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "userId": userId,
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
                    $('#userRegistrationLoadDataModal').html(data);
                }
            });
        }
    </script>
@endsection
