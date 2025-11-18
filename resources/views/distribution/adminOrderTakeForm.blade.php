@php
    $privilageId = \DB::table('pm_interfaces')
        ->select('pm_interfaces.id AS pageId', 'pm_interface_topic.id AS grupId')
        ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
        ->where('pm_interfaces.path', 'adminOrderTakeForm')
        ->first();
@endphp

@extends('layout', ['pageId' => $privilageId->pageId, 'grupId' => $privilageId->grupId])

@section('content')
{{-- SweetAlert2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2><b>Order Take Form</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="/admindashboard">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a>Distribution</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Order Take Form</strong>
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
                        <select class="select2_demo_3 form-control" id="vehicle" onchange="loadProducts()">
                            <option value="0">-- Select One --</option>
                            @foreach ($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">🚚 {{ $vehicle->reg_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="orderDate">Order Date <sup class="st-icon-pandora" style="color: #ff001e">*</sup></label><br>
                        <input type="date" class="form-control" id="orderDate" onchange="loadProducts()">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row" id="productsTable" style="display: none;">
    <div class="col-sm-12">
        <div class="ibox">
            <div class="ibox-title">
                <h5>Products Order</h5>
            </div>
            <div class="ibox-content">
                <form id="orderForm" method="POST">
                    @csrf
                    <input type="hidden" id="selectedVehicle" name="vehicle_id">
                    <input type="hidden" id="selectedDate" name="order_date">

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 15%; color: #000000; text-transform: uppercase;">
                                        Category / Product
                                    </th>
                                    <th style="width: 15%; color: #000000; text-transform: uppercase;">
                                        Quantity
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $category)
                                    <tr class="table-primary">
                                        <td colspan="2">
                                            <strong>{{ $category->main_category_name }}</strong>
                                        </td>
                                    </tr>
                                    @foreach($category->subCategories as $product)
                                        @if ($product->is_active == 1)
                                            <tr>
                                                <td style="padding-left: 30px;">{{ $product->sub_category_name }}</td>
                                                <td>
                                                    <input type="number" class="form-control" name="quantities[{{ $product->id }}]" min="0" value="0">
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-primary" onclick="saveOrderData()">Save Order</button>
                    </div>
                </form>
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
});

function saveOrderData() {
    var vehicleId = $('#selectedVehicle').val();
    var orderDate = $('#selectedDate').val();
    var quantities = {};

    // Collect all quantity inputs
    $('input[name^="quantities"]').each(function() {
        var productId = $(this).attr('name').match(/\[(\d+)\]/)[1];
        var quantity = $(this).val();
        if (quantity >= 0) {
            quantities[productId] = quantity;
        }
    });

    if (Object.keys(quantities).length === 0) {
        Swal.fire({
            title: "Stop",
            text: "Please add products and quantities before saving order.",
            icon: "warning"
        });
        return;
    }

    // Send Ajax request
    $.ajax({
        url: "{{ route('saveOrder') }}",
        type: "POST",
        data: {
            "_token": "{{ csrf_token() }}",
            "vehicle_id": vehicleId,
            "order_date": orderDate,
            "quantities": quantities
        },
        beforeSend: function() {
            showLder();
        },
        complete: function() {
            hideLder();
        },
        error: function(data) {
            hideLder();
            Swal.fire({
                title: "Error",
                text: "Something went wrong while saving the order.",
                icon: "error"
            });
        },
        success: function(data) {
            if (data.status === 'success') {
                Swal.fire({
                    title: "Success!",
                    text: "Order has been saved successfully.",
                    icon: "success",
                    confirmButtonText: "OK"
                }).then((result) => {
                    // Reset form and hide products table
                    $('#orderForm')[0].reset();
                    $('#vehicle').val('0').trigger('change');
                    $('#orderDate').val('');
                    $('#productsTable').hide();
                });
            } else {
                Swal.fire({
                    title: "Sorry!",
                    text: data.message || "Something went wrong while saving the order.",
                    icon: "warning"
                });
            }
        }
    });
}

function loadProducts() {
    var vehicleId = $('#vehicle').val();
    var orderDate = $('#orderDate').val();

    if (vehicleId != '0' && orderDate) {
        $('#selectedVehicle').val(vehicleId);
        $('#selectedDate').val(orderDate);

        // Show loading state
        showLder();

        // Check for existing order
        $.ajax({
            url: "{{ route('checkExistingOrder') }}",
            type: "POST",
            data: {
                "_token": "{{ csrf_token() }}",
                "vehicle_id": vehicleId,
                "order_date": orderDate
            },
            success: function(response) {
                if (response.status === 'success') {
                    // Reset all quantities to 0 first and make them editable
                    $('input[name^="quantities"]').val('0').prop('readonly', false);
                    $('.btn-primary').prop('disabled', false);

                    // If there's an existing order, set its quantities and make them read-only
                    if (response.exists && response.products) {
                        Object.keys(response.products).forEach(function(productId) {
                            $(`input[name="quantities[${productId}]"]`).val(response.products[productId]).prop('readonly', true);
                        });
                        // Disable save button if there's an existing order
                        $('.btn-primary').prop('disabled', true);
                    }

                    $('#productsTable').show();
                } else {
                    Swal.fire({
                        title: "Error",
                        text: "Error checking existing order",
                        icon: "error"
                    });
                }
            },
            error: function() {
                Swal.fire({
                    title: "Error",
                    text: "Error checking existing order",
                    icon: "error"
                });
            },
            complete: function() {
                hideLder();
            }
        });
    } else {
        $('#productsTable').hide();
        // Reset all quantities to 0 and make them editable when no vehicle/date selected
        $('input[name^="quantities"]').val('0').prop('readonly', false);
        $('.btn-primary').prop('disabled', false);
    }
}
</script>
@endsection
