@php
    $privilageId = \DB::table('pm_interfaces')
        ->select('pm_interfaces.id AS pageId', 'pm_interface_topic.id AS grupId')
        ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
        ->where('pm_interfaces.path', 'adminOrderTakeFormDetails')
        ->first();
@endphp

@extends('layout', ['pageId' => $privilageId->pageId, 'grupId' => $privilageId->grupId])

@section('content')
{{-- SweetAlert2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2><b>Order Take Form Details</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="/admindashboard">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a>Distribution</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Order Take Form Details</strong>
            </li>
        </ol>
    </div>
</div>
<br>

<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <div class="ibox-title">
                <h5>Select Vehicle and Date</h5>
            </div>
            <div class="ibox-content">
                <div class="form-group row">
                    <div class="form-group col-md-4">
                        <label for="vehicle">Vehicle <sup class="st-icon-pandora" style="color: #ff001e">*</sup></label><br>
                        <select class="select2_demo_3 form-control" id="vehicle" onchange="loadOrderDetails()">
                            <option value="0">-- All Vehicles --</option>
                            @foreach ($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">🚚 {{ $vehicle->reg_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="orderDate">Order Date <sup class="st-icon-pandora" style="color: #ff001e">*</sup></label><br>
                        <input type="date" class="form-control" id="orderDate" onchange="loadOrderDetails()">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row" id="orderDetailsTable" style="display: none;">
    <div class="col-sm-12">
        <div class="ibox">
            <div class="ibox-title">
                <h5>Order Details</h5>
            </div>
            <div class="ibox-content">
                <div class="table-responsive" id="orderDetailsContent">
                    <!-- Content will be loaded here via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')
<script>
$(document).ready(function() {
    $(".select2_demo_3").select2({
        placeholder: "-- Select One --",
        allowClear: true
    });

    // Set minimum date to today
});

function loadOrderDetails() {
    var orderDate = $('#orderDate').val();
    var vehicle = $('#vehicle').val();

    if (orderDate) {
        // Show loading state
        showLder();

        // Determine which AJAX endpoint to use based on vehicle selection
        var url = vehicle === '0'
            ? "{{ route('loadOrderDetailsAll') }}"
            : "{{ route('loadOrderDetailsSingle') }}";

        // Make AJAX request
        $.ajax({
            url: url,
            type: "POST",
            data: {
                "_token": "{{ csrf_token() }}",
                "order_date": orderDate,
                "vehicle": vehicle
            },
            success: function(response) {
                if (response.status === 'error') {
                    Swal.fire({
                        title: "Error",
                        text: response.message,
                        icon: "error"
                    });
                } else {
                    $('#orderDetailsContent').html(response);
                    $('#orderDetailsTable').show();
                }
            },
            error: function() {
                Swal.fire({
                    title: "Error",
                    text: "Error loading order details",
                    icon: "error"
                });
            },
            complete: function() {
                hideLder();
            }
        });
    } else {
        $('#orderDetailsTable').hide();
    }
}
</script>
@endsection
