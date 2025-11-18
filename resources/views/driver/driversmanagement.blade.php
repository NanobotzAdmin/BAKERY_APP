@php
    $privilageId = \DB::table('pm_interfaces')
        ->select('pm_interfaces.id AS pageId', 'pm_interface_topic.id AS grupId')
        ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
        ->where('pm_interfaces.path', 'adminDriverManagement')
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
            text-align: center; /* Horizontally center the text */
            vertical-align: middle !important; /* Vertically center the text */
        }
    </style>

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2><b>Drivers Management</b></h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/admindashboard">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a>People Management</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Drivers Management</strong>
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
                        <form method="POST" action="saveDriver">
                            {{ csrf_field() }}
                            <div class="modal-body">

                                <div class="form-group">
                                    <label for="dname"> Driver Name</label>
                                    <input type="text" class="form-control" id="dname" name="driverName"
                                        value="{{ old('driverName') }}">
                                </div>
                                <div class="form-group">
                                    <label for="licence">Licence Number</label>
                                    <input type="text" class="form-control" id="licence" name="licenceNo"
                                        value="{{ old('licenceNo') }}">
                                </div>
                                <div class="form-group">
                                    <label for="ldate">Licence Expiration Date</label>
                                    <input type="date" class="form-control" id="ldate" name="expiryDate"
                                        value="{{ old('expiryDate') }}">
                                </div>
                                <div class="form-group">
                                    <label for="cNo">Contact Number</label>
                                    <input type="text" class="form-control" id="cNo" name="contact"
                                        value="{{ old('contact') }}">
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save Driver</button>
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
                    <h5>Manage Drivers</h5>
                </div>
                <div class="ibox-content">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createDriver">Create New Driver</button>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover dataTables-example" style="font-family: 'Lato', sans-serif;">
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
                                <?php $id = 0; ?>
                                @foreach ($driverList as $drivers)
                                    <?php $id++; ?>

                                    <tr>
                                        <td>{{ $id }}</td>
                                        <td>{{ $drivers->driver_name }}</td>
                                        <td>{{ $drivers->licence_no }}</td>
                                        <td>{{ date('Y-m-d', strtotime($drivers->licence_expireration)) }}</td>
                                        <td>{{ $drivers->contact_number }}</td>
                                        @if ($drivers->is_active == 1)
                                            <td style="min-width: 90px; color: #1ab394; text-align: center;"><span class="badge" style="color: #28a745; background-color: #e2f5e6;">Active</span></td>
                                        @else
                                            <td style="min-width: 90px; color: #e70000; text-align: center;"><span class="badge" style="color: #dc3545; background-color: #fceff0;">Inactive</span></td>
                                        @endif
                                        <td>
                                            <button type="button" class="btn btn-outline-warning btn-xs" data-toggle="modal"  data-target="#updateDriver" onclick="showDriverUpdateModal({{ $drivers->id }})"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp; Update</button>

                                            @if ($drivers->is_active == App\STATIC_DATA_MODEL::$Active)
                                                <a href="/deleteDriver/{{ $drivers->id }}"> <button type="button"
                                                        class="btn btn-outline-danger btn-xs">Deactivate</button></a>
                                            @else
                                                <a href="/deleteDriver/{{ $drivers->id }}"> <button type="button"
                                                        class="btn btn-outline-success btn-xs">&nbsp;&nbsp; Activate &nbsp;&nbsp;</button></a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="updateDriver" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content" id="modelContentDriverUpdate">

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
                pageLength: 10,
                responsive: true,
                dom: '<"html5buttons"B>lTfgitp',
                buttons: [
                ]
            });
        });


        function showDriverUpdateModal(driverId) {
            var csrf_token = $("#csrf_token").val();
            jQuery.ajax({
                url: "{{ url('/loadDriverDataToModal') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "driverId": driverId,
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
                    $('#modelContentDriverUpdate').html(data);
                }
            });
        }
    </script>
@endsection
