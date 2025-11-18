@php

    $privilageId = \DB::table('pm_interfaces')
        ->select('pm_interfaces.id AS pageId', 'pm_interface_topic.id AS grupId')
        ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
        ->where('pm_interfaces.path', 'adminCreateRoute')
        ->first();
@endphp

@extends('layout', ['pageId' => $privilageId->pageId, 'grupId' => $privilageId->grupId])

@section('content')

{{-- google fonts --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto&family=Roboto+Slab&display=swap" rel="stylesheet">

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

    /* --- Table CSS begins --- */
    .styled-table th:first-child {
        border-radius: 5px 0 0 0;
    }
    .styled-table th:last-child {
        border-radius: 0 5px 0 0;
    }
    .styled-table {
        border-collapse: collapse;
        margin: 25px 0;
        font-size: 14px;
        font-family: Sans-serif;
        min-width: 400px;
    }
    .styled-table thead tr {
        background-color: #846f5d;
        color: #ffffff;
        text-align: left;
        font-size: 13px;
        font-family: 'Roboto Slab', serif;
    }
    .styled-table th,
    .styled-table td {
        padding: 12px 15px;
    }
    .styled-table tbody tr {
        border-bottom: 1px solid #dddddd;
    }
    .styled-table tbody tr:nth-of-type(even) {
        background-color: #ffffff;
    }
    .styled-table tbody tr:last-of-type {
        border-bottom: 2px solid #846f5d;
    }
    .styled-table tbody tr:hover td {
        background-color: #faf6ec;
    }
</style>

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2><b>Create Route</b></h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/admindashboard">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a>Admin Settings</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Create Route</strong>
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
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Create New Route </h5>
                </div>
                <div class="ibox-content">
                    <form method="POST" action="saveRoute">
                        {{ csrf_field() }}
                        <div class="form-group row">
                            <div class="form-group col-md-6">
                                <label for="">Route Name <span style="color: red">*</span></label>
                                <input type="text" class="form-control" name="routeName" value="{{ old('routeName') }}" maxlength="150" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="form-group col-md-6">
                                <label for="">Description</label>
                                <textarea rows="5" class="form-control" name="routeDescription" value="{{ old('routeDescription') }}" maxlength="300" autocomplete="off"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="form-group col-md-6">
                                <button type="submit" class="btn btn-primary btn-sm pull-right">Create Route</button>
                            </div>
                        </div>

                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover styled-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Route Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $id = 0; ?>
                                @foreach ($routes as $route)
                                <?php $id++; ?>
                                    <tr>
                                        <td>{{ $id }}</td>
                                        <td>{{ $route->route_name }}</td>
                                        <td>
                                            @if ($route->route_description != null)
                                                {{ $route->route_description }}
                                            @endif
                                            @if ($route->route_description == null)
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; --
                                            @endif
                                        </td>
                                        @if ($route->is_active == 1)
                                            <td style="min-width: 90px; color: #1ab394;"><span class="badge" style="color: #28a745; background-color: #e2f5e6;">Active</span></td>
                                        @else
                                            <td style="min-width: 90px; color: #e70000;"><span class="badge" style="color: #dc3545; background-color: #fceff0;">Inactive</span></td>
                                        @endif
                                        <td>
                                            <button type="button" data-target="#update" data-toggle="modal" class="btn btn-outline-warning btn-xs" onclick="updateRoue({{ $route->id }})"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp; Update</button>
                                            @if ($route->is_active)
                                                <a href="/deleteRoute/{{ $route->id }}"> <button type="button" class="btn btn-outline-danger btn-xs">Deactivate</button></a>
                                            @else
                                                <a href="/deleteRoute/{{ $route->id }}"> <button type="button" class="btn btn-outline-success btn-xs">&nbsp; Activate &nbsp;</button></a>
                                            @endif
                                        </td>

                                        <!-- Modal -->
                                        <div class="modal fade" id="update" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content" id="loadRouteContent">

                                                </div>
                                            </div>
                                        </div>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Shop Details</h5>
                </div>
                <div class="ibox-content">
                    <div class="form-group row">
                        <div class="form-group col-md-5">
                            <label for="">Route</label>
                            <select class="select2_demo_3 form-control" id="searchRoutes">
                                <option value="0">-- Select One --</option>
                                @foreach ($ActiveRoutes as $route)
                                    <option value="{{ $route->id }}">{{ $route->route_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label style="color: white">btn</label><br>
                            <button class="btn btn-info btn-sm" onclick="searchShopsToRoute()"><i class="fa fa-search" aria-hidden="true"></i>&nbsp; Search</button>
                        </div>
                    </div>

                    <div class="table-responsive" id="loadShopsToRoute">

                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection


@section('footer')
    <script>
        $(document).ready(function(){
            // $('#routeTable').DataTable({
            //     pageLength: 10,
            //     responsive: true,
            //     dom: '<"html5buttons"B>lTfgitp',
            //     buttons: [
            //     {extend: 'excel', title: 'RichVill_Route_Customers'},
            //     {extend: 'pdf', title: 'RichVill_Route_Customers'},
            //     ]
            // });
                  $(".select2_demo_3").select2({
                      placeholder: "Select a state",
                      allowClear: true
                  });
              });

        function updateRoue(routeId) {
            var csrf_token = $("#csrf_token").val();

            jQuery.ajax({
                url: "{{ url('/loadRouteData') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "routeId": routeId,
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
                    $('#loadRouteContent').html(data);
                }
            });
        }

        function searchShopsToRoute() {
            var csrf_token = $("#csrf_token").val();
            var route = $("#searchRoutes").val();
            if (route == 0) {
                swal("Sorry!", "Select Route!", "warning");
            } else {
                jQuery.ajax({
                    url: "{{ url('/searchShopsToRoute') }}",
                    type: "POST",
                    data: {
                        "_token": csrf_token,
                        "route": route,
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
                        $('#loadShopsToRoute').html(data);
                    }
                });
            }
        }
    </script>
@endsection
