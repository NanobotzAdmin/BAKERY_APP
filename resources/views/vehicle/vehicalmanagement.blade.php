@php


    $privilageId = \DB::table('pm_interfaces')
        ->select('pm_interfaces.id AS pageId', 'pm_interface_topic.id AS grupId')
        ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
        ->where('pm_interfaces.path', 'adminVehicleManagement')
        ->first();


@endphp


@extends('layout', ['pageId' => $privilageId->pageId, 'grupId' => $privilageId->grupId])

@section('content')

    <style>
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

        .table-hover tbody tr:hover {
            background-color: #faf6ec;
            color: #000;
            /* Light blue color - adjust as needed */
            transition: background-color 0.2s;
            /* Add a smooth transition effect */
        }

        .table th {
            text-align: center;
            /* Horizontally center the text */
            vertical-align: middle !important;
            /* Vertically center the text */
        }

        /* Toggle switch styles */
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #dc3545;
            -webkit-transition: .4s;
            transition: .4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #28a745;
        }

        input:checked+.slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Badge styles */
        .badge-success {
            color: #28a745;
            background-color: #e2f5e6;
        }

        .badge-danger {
            color: #dc3545;
            background-color: #fceff0;
        }
    </style>

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2><b>Vehicle Management</b></h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/admindashboard">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a>Admin Settings</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Vehicle Management</strong>
                </li>
            </ol>
        </div>
    </div>
    <br>

    <div class="row">
        <div class="col-sm-12">
            <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">
            @include('include.flash')
            @include('include.errors')
            <div class="row justify-content-start">

            </div>
            <!-- Modal -->
            <div class="modal fade" id="createVehical" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form method="POST" action="saveVehicle">
                            {{ csrf_field() }}
                            <div class="modal-header">
                                <h4 class="modal-title" id="exampleModalLabel">Create New Vehicle</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">

                                <div class="form-group">
                                    <label for="rname"> Registration Number</label>
                                    <input type="text" class="form-control" id="rname" name="RegiNo"
                                        value="{{old('RegiNo')}}">
                                </div>
                                <div class="form-group">
                                    <label for="eNo">Engine Number</label>
                                    <input type="text" class="form-control" id="eNo" value="{{old('EngineNo')}}"
                                        name="EngineNo">
                                </div>
                                <div class="form-group">
                                    <label for="chNo">Chassis Number</label>
                                    <input type="text" class="form-control" id="chNo" value="{{old('ChasiNo')}}"
                                        name="ChasiNo">
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save Vehicle</button>
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
                    <h5>Manage Vehicle</h5>
                </div>
                <div class="ibox-content">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                        data-target="#createVehical">Create New Vehicle</button>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover dataTables-example"
                            style="font-family: 'Lato', sans-serif;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Registration Number</th>
                                    <th>Engine Number</th>
                                    <th>Chassis Number</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($vehicleList as $vehicles)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $vehicles->reg_number }}</td>
                                        <td>{{ $vehicles->engine_number }}</td>
                                        <td>{{ $vehicles->chassis_number }}</td>
                                        <td style="text-align: center;">
                                            <label class="switch">
                                                <input type="checkbox" class="status-toggle"
                                                    data-vehicle-id="{{ $vehicles->id }}" {{ $vehicles->is_active == 1 ? 'checked' : '' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <span style="margin-left: 10px;">
                                                @if($vehicles->is_active == 1)
                                                    <span class="badge"
                                                        style="color: #28a745; background-color: #e2f5e6;">Active</span>
                                                @else
                                                    <span class="badge"
                                                        style="color: #dc3545; background-color: #fceff0;">Inactive</span>
                                                @endif
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-outline-warning btn-xs" data-toggle="modal"
                                                data-target="#updateVehical"
                                                onclick="showVehicleUpdateModal({{ $vehicles->id }})"><i
                                                    class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp; Update</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            $('.dataTables-example').DataTable({
                pageLength: 10,
                responsive: true,
                dom: '<"html5buttons"B>lTfgitp',
                buttons: []
            });

            // Handle status toggle change
            $('.status-toggle').change(function () {
                var vehicleId = $(this).data('vehicle-id');
                var status = $(this).is(':checked') ? 1 : 0;
                var csrfToken = $('#csrf_token').val();
                var toggleElement = $(this);
                var badgeElement = $(this).closest('td').find('.badge');

                // Update the badge text and color immediately for better UX
                if (status == 1) {
                    badgeElement.removeClass('badge-danger').addClass('badge-success');
                    badgeElement.css({ 'color': '#28a745', 'background-color': '#e2f5e6' });
                    badgeElement.text('Active');
                } else {
                    badgeElement.removeClass('badge-success').addClass('badge-danger');
                    badgeElement.css({ 'color': '#dc3545', 'background-color': '#fceff0' });
                    badgeElement.text('Inactive');
                }

                $.ajax({
                    url: "{{ url('/changeVehicleStatus') }}",
                    type: "POST",
                    data: {
                        "_token": csrfToken,
                        "vehicle_id": vehicleId,
                        "status": status
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonColor: '#28a745' // Optional: Custom button color
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error',
                                confirmButtonColor: '#dc3545' // Optional: Custom button color
                            });
                            // Revert the toggle and badge if the update failed
                            toggleElement.prop('checked', !status);
                            if (status == 1) {
                                badgeElement.removeClass('badge-success').addClass('badge-danger');
                                badgeElement.css({ 'color': '#dc3545', 'background-color': '#fceff0' });
                                badgeElement.text('Inactive');
                            } else {
                                badgeElement.removeClass('badge-danger').addClass('badge-success');
                                badgeElement.css({ 'color': '#28a745', 'background-color': '#e2f5e6' });
                                badgeElement.text('Active');
                            }
                        }
                    },
                    error: function () {
                        Swal.fire({
                            title: 'Error!',
                            text: "Failed to update status",
                            icon: 'error',
                            confirmButtonColor: '#dc3545' // Optional: Custom button color
                        });
                        // Revert the toggle and badge if the update failed
                        toggleElement.prop('checked', !status);
                        if (status == 1) {
                            badgeElement.removeClass('badge-success').addClass('badge-danger');
                            badgeElement.css({ 'color': '#dc3545', 'background-color': '#fceff0' });
                            badgeElement.text('Inactive');
                        } else {
                            badgeElement.removeClass('badge-danger').addClass('badge-success');
                            badgeElement.css({ 'color': '#28a745', 'background-color': '#e2f5e6' });
                            badgeElement.text('Active');
                        }
                    }
                });
            });
        });


        function showVehicleUpdateModal(vehicleID) {
            var csrf_token = $("#csrf_token").val();

            jQuery.ajax({
                url: "{{ url('/loadVehicleDataToModal') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "vehicleID": vehicleID,
                },
                beforeSend: function () {
                    showLder();
                },
                complete: function () {
                },
                error: function (data) {
                },
                success: function (data) {
                    hideLder();
                    $('#vehicleLoadDataModal').html(data);
                }
            });
        }
    </script>

@endsection