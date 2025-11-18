<style>
    /* table tbody td {
        border: 1px solid black;
    } */
</style>

<div class="modal-header">
    <h4 class="modal-title" id="exampleModalLabel">Delivery Vehicle - {{ $vehicleRegNo->reg_number }}</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>


<div class="modal-body">
    <h1>Update Unloadings</h1>
    <small>* Updated values are indicated by <span style="color: #41a5fd;">Blue color</span></small>
    <div class="table-responsive">
        <table class="table table-hover table-sm styled-table" id="updateUnloadings_TBL">
            <thead>
                <tr style="text-align: center;">
                    <th style=" vertical-align: middle;">#</th>
                    <th style="min-width: 150px; min-width: 152px; vertical-align: middle;">Product</th>
                    <th style="min-width: 70px; min-width: 75px;">Loading Qty</th>
                    <th style="min-width: 70px; min-width: 75px;">Unloading Qty <small>(System)</small></th>
                    <th style="min-width: 70px; min-width: 75px;">Unloading Qty <small>(Physical)</small></th>
                    <th style="min-width: 60px; min-width: 65px;">Sold Qty</th>
                    <th style="display: none;">Stock Batch ID</th>
                    <th style="min-width: 70px; min-width: 75px;">Qty Difference</th>
                    <th style="min-width: 70px; min-width: 75px;">Selling Price <small>(Rs)</small></th>
                    <th style="min-width: 80px; min-width: 85px;">Price Difference <small>(Rs)</small></th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_U = (float) 0;
                @endphp
                @foreach ($deliveryStock as $deliveryVehicleStock)
                    <tr>
                        @php
                            $stockBatch_Obj = App\StockBatch::find($deliveryVehicleStock->pm_stock_batch_id);
                            $productSubCategory_Obj = App\SubCategory::find($stockBatch_Obj->pm_product_sub_category_id);
                            // $deliveryInvoice2 = App\customerInvoiceHasStock::where([
                            //     ['dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id', $deliveryVehicleStock->pm_stock_batch_id],
                            //     ['dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id', $deliveryVehicleStock->dm_delivery_vehicle_id],
                            // ])->sum('quantity');

                            $deliveryInvoice2 = App\customerInvoiceHasStock::join('dm_customer_invoice', 'dm_customer_invoice.id', '=', 'dm_customer_invoice_has_stock_batch.dm_customer_invoice_id')
                            ->where([
                                ['dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id', $deliveryVehicleStock->pm_stock_batch_id],
                                ['dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id', $deliveryVehicleStock->dm_delivery_vehicle_id],
                                ['dm_customer_invoice.invoice_status', '!=', 3]
                            ])->sum('dm_customer_invoice_has_stock_batch.quantity');

                            if (empty($deliveryInvoice2)) {
                                $sellQty2 = 0;
                            } else {
                                $sellQty2 = $deliveryInvoice2;
                            }
                        @endphp
                        {{-- # --}}
                        <td>{{ $loop->iteration }}</td>
                        {{-- Product --}}
                        <td>{{ $productSubCategory_Obj->sub_category_name }}</td>
                        {{-- Loading Qty --}}
                        <td style="text-align: center;">{{ round($deliveryVehicleStock->loaded_qty, 3) }}</td>
                        {{-- Unloading Qty (System --}}
                        <td style="text-align: center;" id="unloadingQty{{ $loop->iteration }}">{{ round($deliveryVehicleStock->availbale_qty, 3) }}</td>
                        {{-- Physical Unloading Qty --}}
                        <td style="text-align: center; color: #000000;">
                            @if (!is_numeric($deliveryVehicleStock->physical_unloading_qty))
                                <input type="text" class="form-control col-sm" style="font-size: 13px;" id="physicalUnloadingQty{{ $loop->iteration }}" value="{{ $deliveryVehicleStock->availbale_qty }}" min="0" maxlength="7" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onkeyup="qtyDifferenceCal_Unloading('{{ $loop->iteration }}')" onblur="setZero_Unloading('{{ $loop->iteration }}')" autocomplete="off">
                            @else
                                <input type="text" class="form-control col-sm" style="font-size: 13px; border: 1px solid #41a5fd;" id="physicalUnloadingQty{{ $loop->iteration }}" value="{{ $deliveryVehicleStock->physical_unloading_qty }}" min="0" maxlength="7" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onkeyup="qtyDifferenceCal_Unloading('{{ $loop->iteration }}')" onblur="setZero_Unloading('{{ $loop->iteration }}')" autocomplete="off">
                            @endif
                        </td>
                        {{-- Sold Qty --}}
                        <td style="text-align: center;">{{ round($sellQty2, 3) }}</td>
                        {{-- Stock Batch ID --}}
                        <td style=" display: none;">{{ $deliveryVehicleStock->pm_stock_batch_id }}</td>
                        {{-- Qty Difference --}}
                        <td style="text-align: center; color: #971919;" id="unloading_qtyDifference{{ $loop->iteration }}">
                            @if (!is_numeric($deliveryVehicleStock->physical_unloading_qty))
                                {{ 0 }}
                            @else
                                {{ number_format($deliveryVehicleStock->physical_unloading_qty - $deliveryVehicleStock->availbale_qty, 3) }}
                            @endif
                        </td>
                        {{-- Selling Price --}}
                        <td style="text-align: center;" id="unloading_salePrice{{ $loop->iteration }}">{{ number_format($productSubCategory_Obj->selling_price, 2) }}</td>
                        {{-- Difference Price --}}
                        <td style="text-align: right;" id="unloading_priceDifference{{ $loop->iteration }}">
                            @if (!is_numeric($deliveryVehicleStock->physical_unloading_qty))
                                {{ number_format(($deliveryVehicleStock->availbale_qty - $deliveryVehicleStock->availbale_qty) * $productSubCategory_Obj->selling_price, 2) }}
                                @php
                                    $total_U += ($deliveryVehicleStock->availbale_qty - $deliveryVehicleStock->availbale_qty) * $productSubCategory_Obj->selling_price
                                @endphp
                            @else
                                {{ number_format(($deliveryVehicleStock->physical_unloading_qty - $deliveryVehicleStock->availbale_qty) * $productSubCategory_Obj->selling_price, 2) }}
                                @php
                                    $total_U += ($deliveryVehicleStock->physical_unloading_qty - $deliveryVehicleStock->availbale_qty) * $productSubCategory_Obj->selling_price
                                @endphp
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <td colspan="6"></td>
                <td style="text-align: right; font-weight: bold; border-bottom: 2px solid black;">TOTAL</td>
                <td style="text-align: right; font-weight: bold; border-bottom: 2px solid black;">Rs.</td>
                <td style="text-align: right; font-weight: bold; border-bottom: 2px solid black;" id="unloadings_total">{{ number_format($total_U, 2) }}</td>
            </tfoot>
        </table>
    </div>
    <div class="float-right">
        <button type="button" class="btn btn-warning btn-xs" onclick="updateUnloadings({{ $vehicle }})">Update Unloadings</button>
    </div>


    <br><br><hr>{{-- ****************************************************************************************************************************************************** --}}


    <h1>Update Returns</h1>
    <small>* Updated values are indicated by <span style="color: #41a5fd;">Blue color</span></small>
    <div class="table-responsive">
        <table class="table table-hover table-sm styled-table" id="updateReturns_TBL">
            <thead>
                <tr style="text-align: center;">
                    <th style=" vertical-align: middle;">#</th>
                    <th style="min-width: 150px; min-width: 152px; vertical-align: middle;">Product</th>
                    <th style="min-width: 70px; min-width: 75px;">Stock Batch ID</th>
                    <th style="min-width: 90px; min-width: 95px;">Return Qty <small>(System)</small></th>
                    <th style="min-width: 70px; min-width: 75px;">Return Qty <small>(Physical)</small></th>
                    <th style="min-width: 70px; min-width: 75px;">Qty Difference</th>
                    <th style="min-width: 85px; min-width: 90px;">Selling Price <small>(Rs)</small></th>
                    <th style="min-width: 105px; min-width: 110px;">Price Difference <small>(Rs)</small></th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_R = (float) 0;
                @endphp
                @foreach ($deliveryInvoice as $DeliveryInvoice)
                    <tr>
                        @php
                            $productSubCategory_Obj = App\SubCategory::find($DeliveryInvoice->pm_product_sub_category_id);
                            $stockBatch = App\StockBatch::find($DeliveryInvoice->stockBatchID);
                        @endphp
                        {{-- # --}}
                        <td>{{ $loop->iteration }}</td>
                        {{-- Product --}}
                        <td>{{ $productSubCategory_Obj->sub_category_name }}</td>
                        {{-- Stock Batch ID --}}
                        <td style="text-align: center;">{{ $DeliveryInvoice->stockBatchID }}</td>
                        {{-- System Return Qty --}}
                        <td style="text-align: center;" id="systemReturnQty{{ $loop->iteration }}">{{ $DeliveryInvoice->sumQty }}</td>
                        {{-- Physical Return Qty --}}
                        <td style="text-align: center; padding-left: 15px; color: #000000;">
                            @if (!is_numeric($DeliveryInvoice->physical_return_qty))
                                <input type="text" class="form-control col-sm" style="font-size: 13px;" id="physicalReturnQty{{ $loop->iteration }}" value="{{ $DeliveryInvoice->sumQty }}" min="0" maxlength="7" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onkeyup="qtyDifferenceCal('{{ $loop->iteration }}')" onblur="setZero('{{ $loop->iteration }}')" autocomplete="off">
                            @else
                                <input type="text" class="form-control col-sm" style="font-size: 13px; border: 1px solid #41a5fd;" id="physicalReturnQty{{ $loop->iteration }}" value="{{ $DeliveryInvoice->physical_return_qty }}" min="0" maxlength="7" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onkeyup="qtyDifferenceCal('{{ $loop->iteration }}')" onblur="setZero('{{ $loop->iteration }}')" autocomplete="off">
                            @endif
                        </td>
                        {{-- Qty Difference --}}
                        <td style="text-align: center; color: #971919;" id="difference{{ $loop->iteration }}">
                            @if (!is_numeric($DeliveryInvoice->physical_return_qty))
                                {{ 0 }}
                            @else
                                {{ number_format($DeliveryInvoice->physical_return_qty - $DeliveryInvoice->sumQty, 3)  }}
                            @endif
                        </td>
                        {{-- Selling Price --}}
                        <td style="text-align: center;" id="salePrice{{ $loop->iteration }}">{{ number_format($productSubCategory_Obj->selling_price, 2) }}</td>
                        {{-- Difference Price --}}
                        <td style="text-align: right;" id="priceDifference{{ $loop->iteration }}">
                            @if (!is_numeric($DeliveryInvoice->physical_return_qty))
                                {{ number_format(($DeliveryInvoice->sumQty - $DeliveryInvoice->sumQty) * $productSubCategory_Obj->selling_price, 2) }}
                                @php $total_R += number_format(($DeliveryInvoice->sumQty - $DeliveryInvoice->sumQty) * $productSubCategory_Obj->selling_price) @endphp
                            @else
                                {{ number_format(($DeliveryInvoice->physical_return_qty - $DeliveryInvoice->sumQty) * $productSubCategory_Obj->selling_price, 2) }}
                                @php $total_R += number_format(($DeliveryInvoice->physical_return_qty - $DeliveryInvoice->sumQty) * $productSubCategory_Obj->selling_price) @endphp
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <td colspan="5"></td>
                <td style="text-align: right; font-weight: bold; border-bottom: 2px solid black;">TOTAL</td>
                <td style="text-align: right; font-weight: bold; border-bottom: 2px solid black;">Rs.</td>
                <td style="text-align: right; font-weight: bold; border-bottom: 2px solid black;" id="total">{{ number_format($total_R, 2) }}</td>
            </tfoot>
        </table>
    </div>
    <div class="float-right">
        <button type="button" class="btn btn-warning btn-xs" onclick="updateReturns({{ $vehicle }})">Update Returns</button>
    </div>
</div>


<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
</div>



{{--  Unloadings Scripts -----------------------------------------------------------------------------------------------}}
<script>
    // set 0 for Physical Unloading Qty  if user leave it empty
    function setZero_Unloading(rowID) {
        var physicalUnloadingQty = document.getElementById('physicalUnloadingQty'+rowID).value;
        // set difference result
        if (physicalUnloadingQty == '') {
            document.getElementById('physicalUnloadingQty'+rowID).value = 0;
            qtyDifferenceCal_Unloading(rowID);
        }
    }

    // calculate Qty Difference
    function qtyDifferenceCal_Unloading(rowID) {
        var unloadingQty = document.getElementById('unloadingQty'+rowID).innerHTML;
        var physicalUnloadingQty = document.getElementById('physicalUnloadingQty'+rowID).value;
        var result = Number(physicalUnloadingQty) - Number(unloadingQty);

        // set difference result
        if (Number.isInteger(result)) {
            document.getElementById('unloading_qtyDifference'+rowID).innerHTML = result.toString();;
        } else {
            document.getElementById('unloading_qtyDifference'+rowID).innerHTML = Number(result).toFixed(3);
        }

        priceDifferenceCal_Unloading(rowID);
        totalCal_Unloading();
    }

    // calculate Price Difference
    function priceDifferenceCal_Unloading(rowID) {
        var unloading_qtyDifference = document.getElementById('unloading_qtyDifference'+rowID).innerHTML;
        var unloading_salePrice = document.getElementById('unloading_salePrice'+rowID).innerHTML;
        var result = Number(unloading_qtyDifference) * Number(unloading_salePrice);
        // set difference result
        document.getElementById('unloading_priceDifference'+rowID).innerHTML = Number(result).toLocaleString('en', {minimumFractionDigits: 2});
    }

    // calculate Unloadings Price Difference TOTAL
    function totalCal_Unloading() {
        // get table data
        var total = 0.0;
        var table = $("#updateUnloadings_TBL tbody");
        table.find('tr').each(function (i, el) {
            var $tds = $(this).find('td');
            var unloading_priceDifference = $tds.eq(9).html();
            var number = parseFloat(unloading_priceDifference.replace(/,/g, ""));
            total += Number(number);
        });
        // set Total
        document.getElementById('unloadings_total').innerHTML = Number(total).toLocaleString('en', {minimumFractionDigits: 2});
    }
</script>



{{--  Returns Scripts -------------------------------------------------------------------------------------------}}
<script>
    // set 0 for Physical Returns Qty  if user leave it empty
    function setZero(rowID) {
        var physicalReturnQty = document.getElementById('physicalReturnQty'+rowID).value;
        // set difference result
        if (physicalReturnQty == '') {
            document.getElementById('physicalReturnQty'+rowID).value = 0;
            qtyDifferenceCal(rowID)
        }
    }

    // calculate Qty Difference
    function qtyDifferenceCal(rowID) {
        var systemReturnQty = document.getElementById('systemReturnQty'+rowID).innerHTML;
        var physicalReturnQty = document.getElementById('physicalReturnQty'+rowID).value;
        var result = Number(physicalReturnQty) - Number(systemReturnQty);

        // set difference result
        if (Number.isInteger(result)) {
            document.getElementById('difference'+rowID).innerHTML = result.toString();
        } else {
            document.getElementById('difference'+rowID).innerHTML = Number(result).toFixed(3);
        }

        priceDifferenceCal(rowID);
        totalCal();
    }

    // calculate Price Difference
    function priceDifferenceCal(rowID) {
        var difference = document.getElementById('difference'+rowID).innerHTML;
        var salePrice = document.getElementById('salePrice'+rowID).innerHTML;
        var result = Number(difference) * Number(salePrice);
        // set difference result
        document.getElementById('priceDifference'+rowID).innerHTML = Number(result).toLocaleString('en', {minimumFractionDigits: 2});
    }

    // calculate Returns Price Difference TOTAL
    function totalCal() {
        // get table data
        var total = 0.0;
        var table = $("#updateReturns_TBL tbody");
        table.find('tr').each(function (i, el) {
            var $tds = $(this).find('td');
            var priceDifference = $tds.eq(7).html();
            var number = parseFloat(priceDifference.replace(/,/g, ""));
            total += Number(number);
        });
        // set Total
        document.getElementById('total').innerHTML = Number(total).toLocaleString('en', {minimumFractionDigits: 2});
    }
</script>
