<div class="col-sm-12">
    <div class="ibox">
        <div class="ibox-title">
            <h5>Available Stock Batches</h5>
        </div>
        <div class="ibox-content">
            <div class="table-responsive">
                <table class="table table-bordered table-hover dataTables-example">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th style="min-width: 150px;">Product</th>
                            <th style="min-width: 100px;">Batch Code</th>
                            <th style="min-width: 80px;">Expire Date</th>
                            <th>Available Quantity</th>
                            <th style="min-width: 120px;">Product Item State</th>
                            <th>Retail Price <small>(LKR)</small></th>
                            <th>Selling Price <small>(LKR)</small></th>
                            <th>Actual Cost <small>(LKR)</small></th>
                            <th>Discounted Price <small>(LKR)</small></th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stockBatch_List as $stockBatch)
                            @php
                                $productSubCategory = App\SubCategory::find($stockBatch->pm_product_sub_category_id);
                                // dd($stockBatch);
                            @endphp

                            {{-- @if ($stockBatch->available_quantity > 0.0) --}}
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $productSubCategory->sub_category_name }}</td>
                                    <td>{{ $stockBatch->batch_code }}</td>
                                    <td>{{ substr($stockBatch->expire_date, 0, 10) }}</td>
                                    <td style="text-align: center; color: #06b900;">{{ $stockBatch->available_quantity }}</td>
                                    <td>{{ $stockBatch->pmProductItemState->item_name }}</td>
                                    <td class="text-right">{{ number_format($stockBatch->retail_price, 2) }}</td>
                                    <td class="text-right">{{ number_format($stockBatch->selling_price, 2) }}</td>
                                    <td class="text-right">{{ number_format($stockBatch->actual_cost, 2) }}</td>
                                    <td class="text-right">{{ number_format($stockBatch->discounted_price, 2) }}</td>
                                    <td>
                                        <button type="button" class="btn btn-outline-warning btn-xs" data-toggle="modal" data-target="#updateStock" onclick="loadQuantityUpdateModal({{ $stockBatch->SB_id }})"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Update</button>
                                    </td>
                                </tr>
                                {{-- @else
                                @if ($stockBatch->is_visible == 1)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $productSubCategory->sub_category_name }}</td>
                                        <td>{{ $stockBatch->batch_code }}</td>
                                        <td>{{ str_limit($stockBatch->expire_date, $limit = 10, $end = '') }}</td>
                                        <td style="text-align: center; color: #c40000;">{{ $stockBatch->available_quantity }}</td>
                                        <td class="text-right">{{ number_format($stockBatch->retail_price, 2) }}</td>
                                        <td class="text-right">{{ number_format($stockBatch->selling_price, 2) }}</td>
                                        <td class="text-right">{{ number_format($stockBatch->actual_cost, 2) }}</td>
                                        <td class="text-right">{{ number_format($stockBatch->discounted_price, 2) }}</td>
                                        <td>
                                            <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#updateStock" onclick="loadQuantityUpdateModal({{ $stockBatch->SB_id }})"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Update</button>
                                        </td>
                                    </tr>
                                @endif --}}
                            {{-- @endif --}}
                        @endforeach
                    </tbody>
                </table>
            </div>


            <!-- Modal -->
            <div class="modal fade" id="updateStock" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="exampleModalLabel">Update Stock</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" id="modelUpdateContent" style="font-family: 'Lato', sans-serif;">
                            {{-- Content in Ajax --}}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-warning" onclick="updateStockBatch()">Update Stock Batch</button>
                            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.dataTables-example').DataTable({
            pageLength: 50,
            responsive: true,
            dom: '<"html5buttons"B>lTfgitp',
            buttons: []
        });
    });


    function loadQuantityUpdateModal(batchId) {
        var csrf_token = $("#csrf_token").val();
        jQuery.ajax({
            url: "{{ url('/loadQuantityUpdateModal') }}",
            type: "POST",
            data: {
                "_token": csrf_token,
                "batchId": batchId,
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
                $('#modelUpdateContent').html(data);
            }
        });
    }


    function updateStockBatch() {
        var csrf_token = $("#csrf_token").val();
        var batchId = $("#batchIdPro").val();
        var action = $("#stockAction").val();
        var productItemState = $("#updateProductItemState").val();
        var qty = $("#totStock").text();
        var retailPrice = $("#updateRetailPrice").val();
        var sellingPrice = $("#updateSellingPrice").val();
        var actualCost = $("#updateActualCost").val();
        var discountedPrice = $("#updateDiscountedPrice").val();

        if (retailPrice === "") {
            swal("", "Retail Price cannot be empty.", "warning");
        } else if (parseFloat(retailPrice) === 0) {
            swal("", "Retail Price cannot be 0.", "warning");
        } else if (sellingPrice === "") {
            swal("", "Selling Price cannot be empty.", "warning");
        } else if (parseFloat(sellingPrice) === 0) {
            swal("", "Selling Price cannot be 0.", "warning");
        } else if (actualCost === "") {
            swal("", "Actual Cost cannot be empty.", "warning");
        } else if (parseFloat(actualCost) === 0) {
            swal("", "Actual Cost cannot be 0.", "warning");
        } else if (discountedPrice === "") {
            swal("", "Discounted Price cannot be empty.", "warning");
        } else if (parseFloat(discountedPrice) === 0) {
            swal("", "Discounted Price cannot be 0.", "warning");
        } else {
            jQuery.ajax({
                url: "{{ url('/updateStockBatch') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "batchId": batchId,
                    "action": action,
                    "productItemState": productItemState,
                    "qty": qty,
                    "retailPrice": retailPrice,
                    "sellingPrice": sellingPrice,
                    "actualCost": actualCost,
                    "discountedPrice": discountedPrice
                },
                beforeSend: function() {
                    showLder();
                },
                complete: function() {
                    hideLder();
                },
                success: function(data) {
                    hideLder();
                    if (data.msg == "success") {
                        $('#updateStock').modal('toggle');
                        $('.modal-backdrop').hide();
                        swal({
                            title: "",
                            text: "Stock Batch successfully updated.",
                            type: "success",
                            timer: 1500,
                            showConfirmButton: false
                        });
                        setTimeout(function() {
                            // location.reload();
                            loadStockBatchDetails(); // <-- call search btn funtion
                        }, 2000);
                    } else if (data.msg == "rawmaterialError") {
                        swal("", "Adding quantity cannot be grater than to material available quantity !", "warning");
                        // window.location = "/adminManageProducts";
                    } else {
                        swal("", data.msg, "error");
                        // window.location = "/adminManageProducts";
                    }
                },
                error: function(data) {
                    hideLoader();
                    if (data.responseJSON && data.responseJSON.msg) {
                        swal("", data.responseJSON.msg, "error");
                    } else {
                        swal("", "An unexpected error occurred.", "error");
                    }
                }
            });
        }
    }
</script>
