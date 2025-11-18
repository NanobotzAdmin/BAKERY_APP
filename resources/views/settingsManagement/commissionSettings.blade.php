@php
    $privilageId = \DB::table('pm_interfaces')
    ->select('pm_interfaces.id AS pageId','pm_interface_topic.id AS grupId')
    ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
    ->where('pm_interfaces.path','commission-settings')
    ->first();
@endphp

@extends('layout', ['pageId' => $privilageId->pageId ,'grupId' => $privilageId->grupId ])

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
        <h2><b>Commission Settings</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="/admindashboard">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a>Admin Settings</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Commission Settings</strong>
            </li>
        </ol>
    </div>
</div>
<br>

<div class="row">
    <div class="col-sm-12">
        <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">
        {{-- @include('include.flash')
        @include('include.errors') --}}
        <div class="row justify-content-start">

        </div>
        <!-- Modal -->
        <div class="modal fade" id="createCommissionConfiguration" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <form method="POST" action="">
                        {{ csrf_field() }}
                        <div class="modal-header">
                            <h4 class="modal-title" id="exampleModalLabel">Create Configuration</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="min_sales_amount">Minimum Sales Amount</label>
                                <input type="text" class="form-control" id="min_sales_amount" name="min_sales_amount" value="{{ old('min_sales_amount') }}" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" autocomplete="off" required>
                            </div>

                            <div class="form-group">
                                <label for="max_sales_amount">Maximum Sales Amount</label>
                                <input type="text" class="form-control" id="max_sales_amount" name="max_sales_amount" value="{{ old('max_sales_amount') }}" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" autocomplete="off" required>
                            </div>

                            <div class="form-group">
                                <label for="commission_rate">Commission Rate</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control form-control-sm" id="commission_rate" name="commission_rate" value="{{ old('commission_rate') }}" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" autocomplete="off" required>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text form-control-sm" id="basic-addon1" style="background-color: #fff0c0"><b>%</b></span>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-sm" onclick="saveCommissionSettings()">Save Configuration</button>
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
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
                <h5>Manage Commission Settings</h5>
            </div>
            <div class="ibox-content">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createCommissionConfiguration">Create New Configuration</button>
            </div>
            <div class="ibox-content">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover dataTables-example" style="font-family: 'Lato', sans-serif;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Minimum Sales Amount</th>
                                <th>Maximum Sales Amount</th>
                                <th>Commission Rate</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($commissionSettings_list as $commissionSettings)
                                <tr>
                                    <td style="text-align: right;">{{ $loop->iteration }}</td>
                                    <td style="text-align: center;">{{ number_format($commissionSettings->min_sales_amount, 2, '.', ',') }}</td>
                                    <td style="text-align: center;">{{ number_format($commissionSettings->max_sales_amount, 2, '.', ',') }}</td>
                                    <td style="text-align: center;">{{ $commissionSettings->commission_rate }} %</td>
                                    @if($commissionSettings->is_active == 1)
                                        <td style="min-width: 90px; color: #1ab394; text-align: center;"><span class="badge" style="color: #28a745; background-color: #e2f5e6;">Active</span></td>
                                    @else
                                        <td style="min-width: 90px; color: #e70000; text-align: center;"><span class="badge" style="color: #dc3545; background-color: #fceff0;">Inactive</span></td>
                                    @endif
                                    <td>
                                        <button type="button" class="btn btn-outline-warning btn-xs" data-toggle="modal" data-target="#updateConfiguration" onclick="loadCommissionSettingsToUpdateModal({{ $commissionSettings->id }})"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp; Update</button>
                                        @if ($commissionSettings->is_active)
                                            <button type="button" class="btn btn-outline-danger btn-xs" onclick="statusChangeCommissionSettings({{ $commissionSettings->id }})">Deactivate</button>
                                        @else
                                            <button type="button" class="btn btn-outline-success btn-xs" onclick="statusChangeCommissionSettings({{ $commissionSettings->id }})">&nbsp; Activate &nbsp;</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="updateConfiguration" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content" id="configurationLoadDataModal">

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
            buttons: []
        });
    });


    function saveCommissionSettings() {
        var csrf_token = $("#csrf_token").val();
        var min_sales_amount = $("#min_sales_amount").val();
        var max_sales_amount = $("#max_sales_amount").val();
        var commission_rate = $("#commission_rate").val();

        jQuery.ajax({
            url: "{{ url('/commission-settings/saveCommissionSettings') }}",
            type: "POST",
            data: {
                "_token": csrf_token,
                "min_sales_amount": min_sales_amount,
                "max_sales_amount": max_sales_amount,
                "commission_rate": commission_rate
            },
            beforeSend: function () {
                showLder();
            },
            complete: function () {
            },
            error: function (response) {
                hideLder();
                let errorMessage = 'Something went wrong.';

                if (response.responseJSON && response.responseJSON.message) {
                    errorMessage = response.responseJSON.message;
                }
                // Validation Error
                if (response.responseJSON && response.responseJSON.type === 'validation') {
                    swal("Validation Failed!", errorMessage, "warning");
                } else {
                    swal("Error", errorMessage, "error");
                }
            },
            success: function (response) {
                hideLder();
                if (response.status === 'success') {
                    // Close the modal
                    $('#createCommissionConfiguration').modal('hide');
                    swal({
                        title: "Save Success",
                        text: response.message,
                        type: "success"
                    }, function() {
                        location.reload();  // Reload the page after "OK" is pressed
                    });
                }
            }
        });
    }


    function loadCommissionSettingsToUpdateModal(ConfigID) {
        var csrf_token = $("#csrf_token").val();

        jQuery.ajax({
            url: "{{ url('/commission-settings/loadCommissionSettingsToUpdateModal') }}",
            type: "POST",
            data: {
                "_token": csrf_token,
                "ConfigID": ConfigID,
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
                $('#configurationLoadDataModal').html(data);
            }
        });
    }


    function updateCommissionSettings() {
        var csrf_token = $("#csrf_token").val();
        var ConfigID = $("#update_commissionSettings_id").val();
        var min_sales_amount = $("#update_min_sales_amount").val().replace(/,/g, '');
        var max_sales_amount = $("#update_max_sales_amount").val().replace(/,/g, '');
        var commission_rate = $("#update_commission_rate").val().replace(/,/g, '');

        jQuery.ajax({
            url: "{{ url('/commission-settings/updateCommissionSettings') }}",
            type: "POST",
            data: {
                "_token": csrf_token,
                "ConfigID": ConfigID,
                "min_sales_amount": min_sales_amount,
                "max_sales_amount": max_sales_amount,
                "commission_rate": commission_rate
            },
            beforeSend: function () {
                showLder();
            },
            complete: function () {
            },
            error: function (response) {
                hideLder();
                let errorMessage = 'Something went wrong.';

                if (response.responseJSON && response.responseJSON.message) {
                    errorMessage = response.responseJSON.message;
                }
                // Validation Error
                if (response.responseJSON && response.responseJSON.type === 'validation') {
                    swal("Validation Failed!", errorMessage, "warning");
                } else {
                    swal("Error", errorMessage, "error");
                }
            },
            success: function (response) {
                hideLder();
                if (response.status === 'success') {
                    // Close the modal
                    $('#createCommissionConfiguration').modal('hide');
                    swal({
                        title: "Update Success",
                        text: response.message,
                        type: "success"
                    }, function() {
                        location.reload();  // Reload the page after "OK" is pressed
                    });
                }
            }
        });
    }


    function statusChangeCommissionSettings(ConfigID) {
        var csrf_token = $("#csrf_token").val();
        jQuery.ajax({
            url: "{{ url('/commission-settings/statusChangeCommissionSettings') }}",
            type: "POST",
            data: {
                "_token": csrf_token,
                "ConfigID": ConfigID
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
                if (data.msg == 'Deactivated') {
                    setTimeout(function () {
                        swal({
                            title: "Deactivated",
                            text: 'Commission Setting Deactivated Successfully.',
                            type: "success",
                            showConfirmButton: true
                        },
                        function () {
                            window.location.reload();
                        });
                    }, 500);
                } else if (data.msg == 'Activated') {
                    setTimeout(function () {
                        swal({
                            title: "Activated",
                            text: 'Commission Setting Activated Successfully.',
                            type: "success",
                            showConfirmButton: true
                        },
                        function () {
                            window.location.reload();
                        });
                    }, 500);
                }
            }
        });
    }
</script>

@endsection
