<div class="modal-header">
    <h4 class="modal-title" id="exampleModalLabel">Delivery Vehicle - {{ $vehicleRegNo->reg_number }}</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <input type="hidden" value="{{ $vehicle }}" id="vehicleId" />
    <div class="form-group row" id="loadAvailableQty">

    </div>
    {{-- <div class="form-group row">
                                            <div class="form-group col-md-6">
                                                <label for="">Racks Count</label>
                                                <input type="number" class="form-control form-control-sm" id="rackQty">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <span><br></span>
                                                <button type="button" class="btn btn-success btn-sm" style="margin-top: 4%" onclick="addProductTODelivery(this.value)">Add to
                                                    Vehicle</button>
                                            </div>
                                        </div> --}}
    <h1>Update Items</h1>
    <div class="table-responsive">
        <table class="table table-hover table-sm styled-table2" id="TblAddProductTODeliveryUpdate">
            <thead>
                <tr>
                    <th style="display: none">Product Id</th>
                    <th style="min-width: 190px; width: 200px; text-align: center; vertical-align: middle;">Product</th>
                    <th style="display: none">Batch Id</th>
                    <th style="min-width: 89px; width: 90px; text-align: center; vertical-align: middle;">Rack Count</th>
                    <th style="display: none">Vehicle Id</th>
                    <th style="min-width: 40px; width: 50px; text-align: center; vertical-align: middle;">Qty</th>
                    <th style="display: none">Available Qty (Stock)</th>
                    <th style="min-width: 110px; width: 115px; text-align: center; vertical-align: middle;"><small>(Vehicle)</small> Available Qty</th>
                    <th style="min-width: 98px; width: 100px; text-align: center; vertical-align: middle;"><small>(Updated)</small> Rack Count</th>
                    <th style="min-width: 80px; width: 85px; text-align: center; vertical-align: middle;">Loaded Qty</th>
                    <th style="text-align: center; vertical-align: middle;">Action</th>
                </tr>
            </thead>
            <tbody id="tblDeliveryBody">
                @foreach ($deliveryStock as $stock)
                    <tr>
                        <?php
                        $batchStock = App\StockBatch::find($stock->pm_stock_batch_id);
                        $product = App\SubCategory::find($batchStock->pm_product_sub_category_id);
                        ?>
                        <td style="display: none">{{ $product->id }}</td>
                        <td>{{ $product->sub_category_name }} ({{ $batchStock->batch_code }})</td>
                        <td style="display: none">{{ $stock->pm_stock_batch_id }}</td>
                        <td style="text-align: center;">{{ $stock->racks_count }}</td>
                        <td style="display: none">{{ $vehicle }}</td>
                        {{-- <td>{{ $stock->availbale_qty  }}</td> --}}
                        <td><input type="text" class="form-control col-sm allow_decimal" value="0"
                                id="{{ $stock->pm_stock_batch_id }}"
                                onkeyup='changeSubTotal({{ $batchStock->available_quantity }},this.value,{{ $stock->pm_stock_batch_id }},{{ $stock->loaded_qty }})'
                                onmouseup='changeSubTotal({{ $batchStock->available_quantity }},this.value,{{ $stock->pm_stock_batch_id }},{{ $stock->loaded_qty }})' />
                        </td>
                        <td style="display: none">{{ $stock->availbale_qty }}</td>
                        <td style="text-align: center;">{{ round($stock->availbale_qty, 3) }}</td>
                        <td><input type="text" class="form-control col-sm allow_decimal" value="0" min=0 /></td>
                        <td style="text-align: center;">{{ round($stock->loaded_qty, 3) }}</td>
                        <td><button type='button' class='btn btn-xs btn-danger' onclick='removeDeliveryProducts({{ $stock->pm_stock_batch_id }}, {{ $vehicle }})' value='Remove'><i class="fa fa-trash" aria-hidden="true"></i> Remove</button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
<div class="modal-footer">
    {{-- <button type="button" class="btn btn-primary" onclick="saveDeliveryData({{ $vehicle }})">Add Product</button> --}}
    <button type="button" class="btn btn-warning btn-sm" onclick="updateStockQuantities({{ $vehicle }})">Update Quantities</button>
    <button type="button" class="btn yellow-skin btn-sm" onclick="updaterackkQuantities({{ $vehicle }})">Update Rack Counts</button>
    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
</div>

<script>
    $(document).ready(function() {



        $(".allow_decimal").on("input", function(evt) {

            var self = $(this);
            self.val(self.val().replace(/[^0-9\.]/g, ''));
            if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which >
                    57)) {
                evt.preventDefault();
            }
        });


    });




    var json_delivery_details2 = {};

    function updateStockQuantities(deliveryId) {
        var deliveryRackTot = 0;
        var deliveryRackTot1 = 0;
        var deliveryRackTot2 = 0;
        var qtyAdd = 0;
        var rack2 = 0;
        var rowCount = document.getElementById("TblAddProductTODeliveryUpdate").rows.length;
        var tbodyCount = document.getElementById("tblDeliveryBody").rows.length;
        var csrf_token = $("#csrf_token").val();

        if (rowCount == 1) {
            swal("Sorry!", "Enter Products!", "warning");
        } else {
            var deliveryData = new Array();

            for (var x = 1; x < rowCount; x++) {
                var productId = document.getElementById("TblAddProductTODeliveryUpdate").rows[x].cells[0].innerHTML;
                var batchId = document.getElementById("TblAddProductTODeliveryUpdate").rows[x].cells[2].innerHTML;
                var qty = document.getElementById("TblAddProductTODeliveryUpdate").rows[x].cells[5].children[0].value;
                //    var rackQty = document.getElementById("TblAddProductTODelivery").rows[x].cells[3].innerHTML;
                var vehicle = document.getElementById("TblAddProductTODeliveryUpdate").rows[x].cells[4].innerHTML;
                //    var rackCount = document.getElementById("TblAddProductTODelivery").rows[x].cells[8].children[0].value;
                var stock = document.getElementById("TblAddProductTODeliveryUpdate").rows[x].cells[6].innerHTML;
                // var update  = document.getElementById("TblAddProductTODelivery").rows[x].cells[10].innerHTML;

                var rowOfData = {};
                rowOfData['productId'] = productId;
                rowOfData['batchId'] = batchId;
                rowOfData['qty'] = qty;
                // rowOfData['rackQty'] = rackQty;
                rowOfData['vehicle'] = vehicle;
                //   rowOfData['updateRack'] = rackCount;
                rowOfData['stock'] = stock;

                if (qty == '') {
                    qtyAdd = 0;
                } else {
                    qtyAdd = qty;
                }

                deliveryRackTot1 += parseFloat(qtyAdd);
                // deliveryRackTot2 += parseFloat(rack2);

                deliveryData.push(rowOfData);
            }

            deliveryRackTot = parseFloat(deliveryRackTot1);
            json_delivery_details2['deliveryDetails'] = deliveryData;
            if (deliveryRackTot1 == 0) {
                swal("Sorry!", "Add amounts to update!", "warning");
            } else {

                jQuery.ajax({
                    url: "{{ url('/updateStockQuantities') }}",
                    type: "POST",
                    data: {
                        "_token": csrf_token,
                        "deliveryData": json_delivery_details2,
                        "deliveryId": deliveryId,
                    },
                    beforeSend: function() {
                        showLder();
                    },
                    complete: function() {},
                    error: function(data) {},
                    success: function(data) {
                        hideLder();
                        if (data.msg == 'sucess') {
                            swal("Success", "Delivery Stock Save Success !", "success");
                            //window.location = "/adminDeliveryVehicleManagement";
                            loadItemsModalUpdate(deliveryId);
                        } else {
                            swal("Stop", data.msgDB, "danger");
                        }
                    }
                });
            }
        }
    }

    var json_delivery_details3 = {};


    function updaterackkQuantities(deliveryId) {

        var deliveryRackTot = 0;
        var deliveryRackTot1 = 0;
        var deliveryRackTot2 = 0;
        var qtyAdd = 0;
        var rack2 = 0;
        var rowCount = document.getElementById("TblAddProductTODeliveryUpdate").rows.length;
        var tbodyCount = document.getElementById("tblDeliveryBody").rows.length;
        var csrf_token = $("#csrf_token").val();

        if (rowCount == 1) {
            swal("Sorry!", "Enter Products!", "warning");
        } else {


            var deliveryData = new Array();

            for (var x = 1; x < rowCount; x++) {
                var productId = document.getElementById("TblAddProductTODeliveryUpdate").rows[x].cells[0].innerHTML;
                var batchId = document.getElementById("TblAddProductTODeliveryUpdate").rows[x].cells[2].innerHTML;
                // var qty = document.getElementById("TblAddProductTODeliveryUpdate").rows[x].cells[5].children[0].value;
                //    var rackQty = document.getElementById("TblAddProductTODelivery").rows[x].cells[3].innerHTML;
                var vehicle = document.getElementById("TblAddProductTODeliveryUpdate").rows[x].cells[4].innerHTML;
                var rackCount = document.getElementById("TblAddProductTODeliveryUpdate").rows[x].cells[8].children[0]
                    .value;
                var stock = document.getElementById("TblAddProductTODeliveryUpdate").rows[x].cells[6].innerHTML;
                // var update  = document.getElementById("TblAddProductTODelivery").rows[x].cells[10].innerHTML;

                var rowOfData = {};
                rowOfData['productId'] = productId;
                rowOfData['batchId'] = batchId;
                // rowOfData['qty'] = qty;
                // rowOfData['rackQty'] = rackQty;
                rowOfData['vehicle'] = vehicle;
                rowOfData['updateRack'] = rackCount;
                rowOfData['stock'] = stock;

                if (rackCount == '') {
                    qtyAdd = 0;
                } else {
                    qtyAdd = rackCount;
                }



                deliveryRackTot1 += parseFloat(qtyAdd);
                // deliveryRackTot2 += parseFloat(rack2);

                deliveryData.push(rowOfData);


            }

            deliveryRackTot = parseFloat(deliveryRackTot1);
            json_delivery_details2['deliveryDetails'] = deliveryData;
            if (deliveryRackTot1 == 0) {
                swal("Sorry!", "Add Rack counts to update!", "warning");
            } else {

                jQuery.ajax({
                    url: "{{ url('/updateStockRackQuantities') }}",
                    type: "POST",
                    data: {

                        "_token": csrf_token,
                        "deliveryData": json_delivery_details2,
                        "deliveryId": deliveryId,
                        "deliveryRackTot": deliveryRackTot




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
                        if (data.msg == 'sucess') {

                            swal("Success", "Delivery Stock Save Success !", "success");
                            loadItemsModalUpdate(deliveryId);
                            // window.location = "/adminDeliveryVehicleManagement";
                        } else if (data.msg == "rack") {
                            swal("Cannot Save Data", "Increase Store Rack", "warning");

                        } else {
                            swal("Stop", data.msgDB, "danger");

                        }




                    }
                });
            }
        }
    }
</script>
