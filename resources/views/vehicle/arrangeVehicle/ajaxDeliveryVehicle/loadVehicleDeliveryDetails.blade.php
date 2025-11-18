<div class="modal-header">
    <h4 class="modal-title" id="exampleModalLabel">Delivery Vehicle - {{ $vehicleRegNo->reg_number }}</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<label></label>
<div class="modal-body">
    <div class="row">
        <?php
        $endMile = '';
        if ($delivery->end_milage === null) {
            $endMile = 0;
        } else {
            $endMile = $delivery->end_milage;
        }

        $total = (float) $endMile - (float) $delivery->start_milage;
        ?>

        <div class="col-md-2">
            <label style="font-weight: bold; font-family: 'Roboto Slab', serif;">Delivery Route </label>:
        </div>
        <div class="col-md-5">
            <label style="font-family: 'Roboto Slab', serif;">{{ $route->route_name }}</label>
        </div>
        <div class="col-md-2">
            <label style="font-weight: bold; font-family: 'Roboto Slab', serif;">Total Distance </label>:
        </div>
        <div class="col-md-2">
            <label style="font-family: 'Roboto Slab', serif;">{{ $total }}</label>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-2">
            <label style="font-weight: bold; font-family: 'Roboto Slab', serif;">Start Milage </label>:
        </div>
        <div class="col-md-5">
            <label style="font-family: 'Roboto Slab', serif;">{{ $delivery->start_milage }}</label>
        </div>
        <div class="col-md-2">
            <label style="font-weight: bold; font-family: 'Roboto Slab', serif;">End Milage </label>:
        </div>
        <div class="col-md-2">
            <label style="font-family: 'Roboto Slab', serif;">{{ $endMile }}</label>
        </div>
    </div>

    {{-- <label>Delivery Route </label>: <label>{{ $delivery->delivery_route }}</label> --}}
    {{-- <label>Start Milage </label>: <label>{{ $delivery->start_milage }}</label> --}}



    {{-- <label>End Milage </label>: <label>{{ $endMile }}</label> --}}

    {{-- <label>Total Distance </label>: <label>{{ $total }}</label> --}}





    <div class="table-responsive">
        <table class="table table-hover table-sm styled-table" id="TblAddProductTODelivery">
            <thead>
                <tr>
                    <th style="text-align: center; min-width: 195px; width: 200px;">Product</th>
                    <th style="text-align: center; min-width: 95px; width: 100px;">Rack Count</th>
                    <th style="text-align: center; min-width: 105px; width: 115px;">Loading Qty</th>
                    <th style="text-align: center; min-width: 120px; width: 125px;">Unloading Qty <small>(System)</small></th>
                    <th style="text-align: center; min-width: 120px; width: 125px;">Unloading Qty <small>(Physical)</small></th>
                    <th style="text-align: center; min-width: 70px; width: 80px;">Sell Qty</th>
                    {{-- <th>Action</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($deliveryStock as $stock)
                    <tr>
                        <?php
                        $batchStock = App\StockBatch::find($stock->pm_stock_batch_id);
                        $product = App\SubCategory::find($batchStock->pm_product_sub_category_id);
                        // $deliveryInvoice2 =  \DB::select("select SUM(dm_customer_invoice_has_stock_batch.quantity) as sellQty
                        // FROM richvil.dm_customer_invoice_has_stock_batch where (dm_customer_invoice_has_stock_batch.dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id='".$stock->dm_delivery_vehicle_id."' ) && dm_customer_invoice_has_stock_batch.dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id='".$stock->pm_stock_batch_id."'");

                        // $deliveryInvoice2 = App\customerInvoiceHasStock::where([
                        //     ['dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id', $stock->pm_stock_batch_id],
                        //     ['dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id', $stock->dm_delivery_vehicle_id]
                        // ])->sum('quantity');

                        $deliveryInvoice2 = App\customerInvoiceHasStock::join('dm_customer_invoice', 'dm_customer_invoice.id', '=', 'dm_customer_invoice_has_stock_batch.dm_customer_invoice_id')
                        ->where([
                            ['dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id', $stock->pm_stock_batch_id],
                            ['dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id', $stock->dm_delivery_vehicle_id],
                            ['dm_customer_invoice.invoice_status', '!=', 3]
                        ])->sum('dm_customer_invoice_has_stock_batch.quantity');

                        if (empty($deliveryInvoice2)) {
                            $sellQty2 = 0;
                        } else {
                            $sellQty2 = $deliveryInvoice2;
                        }
                        ?>
                        <td>{{ $product->sub_category_name }}</td>
                        <td style="text-align: center; padding-right: 20px;">{{ $stock->racks_count }}</td>
                        <td style="text-align: center;">{{ round($stock->loaded_qty, 3) }}</td>
                        <td style="text-align: center;">{{ round($stock->availbale_qty, 3) }}</td>
                        <td style="text-align: center;">{{ round($stock->physical_unloading_qty, 3) }}</td>
                        <td style="text-align: center;">{{ round($sellQty2, 3) }}</td>
                        {{-- <td><button onclick='$(this).parent().parent().remove();' type='button' class='btn btn-sm btn-danger' value='Remove'><span class='fa fa-remove'>Remove</span></button></td> --}}
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <h1>Returns</h1>
    <div class="table-responsive">
        <table class="table table-hover table-sm styled-table" id="TblAddProductTODelivery" style="width: 420px;">
            <thead style="font-family: 'Roboto Slab', serif;">
                <tr>
                    <th style="width: 20px;">#</th>
                    <th style="width: 200px;">Product</th>
                    <th style="width: 95px; text-align: right;">Return Qty</th>
                    {{-- <th>Action</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($deliveryInvoice as $deliveryInvoice4)
                    <tr>
                        <?php
                        $product = App\SubCategory::find($deliveryInvoice4->pm_product_sub_category_id);
                        ?>
                        <td >{{ $loop->iteration }}.</td>
                        <td >{{ $product->sub_category_name }}</td>
                        <td style="text-align: right; padding-right: 20px;">{{ round($deliveryInvoice4->sumQty, 3) }}</td>
                        {{-- <td><button onclick='$(this).parent().parent().remove();' type='button' class='btn btn-sm btn-danger' value='Remove'><span class='fa fa-remove'>Remove</span></button></td> --}}
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <br>

    <div class="row">
        <div class="col-md-6">
            <h3>Billed Customer</h3>
            <div class="table-responsive">
                <table class="table table-striped table-hover list-tbl">
                    <thead style="background-color: #846f5d; color: #ffffff;">
                        <tr>
                            <th>Customer Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($deliveryInvoices as $invoices)
                            <tr>
                                <td>{{ $invoices->customer_name }}</td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <h3>Unbilled Customer</h3>
            <div class="table-responsive">
                <table class="table table-striped table-hover list-tbl">
                    <thead style="background-color: #846f5d; color: #ffffff;">
                        <tr>
                            <th>Customer Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($deliveryCustomers as $customers)
                            <tr>
                                <td>{{ $customers->customer_name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    @if ($delivery->status == 1)
        <button type="button" class="btn btn-danger" onclick="completeDelivery({{ $vehicle }})">Completed</button>
    @endif
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
</div>
