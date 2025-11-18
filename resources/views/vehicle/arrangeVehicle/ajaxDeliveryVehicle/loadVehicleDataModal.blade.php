<div class="modal-header">
    <h4 class="modal-title" id="exampleModalLabel">Delivery Vehicle - {{ $vehicleRegNo->reg_number }}</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <div class="form-group row">
        <div class="form-group col-md-6">
            <label for="">Main Category</label>
            <select class="select2_demo_3 form-control" onchange="loadSubCategories()" name="MainCategory" id="MainCategory">
                <option value="0">-- Select One --</option>
                @foreach ($category as $categories)
                    <option value="{{ $categories->id }}">{{ $categories->main_category_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-6">
            <label for="">Sub-Category</label>
            <select class="select2_demo_3 form-control " name="subCategory" id="subCategory" onchange="loadBatchDetails()">
                <option value="0">-- Select One --</option>
            </select>
        </div>

        <div class="form-group col-md-6">
            <label for="">Batch</label>
            <select class="select2_demo_3 form-control" id="batchCombo" name="batchCombo" onchange="loadAvailableQty()">
                <option value="0">-- Select One --</option>
            </select>
        </div>
        <div class="" id="loadAvailableQty"></div>
    </div>

    <input type="hidden" value="{{ $vehicle }}" id="vehicleId"/>

    <div class="form-group row">
        <div class="form-group col-md-3">
            <label for="">Loading Qty</label>
            <input type="text" class="form-control form-control-sm allow_decimal" id="MODAL_QTY" onkeyup="checkAvailable(this.value)" maxlength="10" autocomplete="off">
        </div>
        <div class="form-group col-md-3">
            <label for="">Racks Count</label>
            <input type="text" class="form-control form-control-sm allow_decimal" id="rackQty" maxlength="10" autocomplete="off">
        </div>
        <div class="form-group col-md-2">
            <label for="">&nbsp;</label>
            <button type="button" class="btn btn-success btn-sm" style="margin-top: 4%" onclick="addProductTODelivery(this.value)"><i class="fa fa-plus-square" aria-hidden="true"></i> Add to Vehicle</button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-sm styled-table2" id="TblAddProductTODelivery">
            <thead>
                <tr>
                    <th style="display: none">Product Id</th>
                    <th style="min-width: 165px; width: 170px; text-align: center; vertical-align: middle;">Product</th>
                    <th style="display: none">Batch Id</th>
                    <th style="min-width: 80px; width: 90px; text-align: center; vertical-align: middle;">Rack Count</th>
                    <th style="display: none">Vehicle Id</th>
                    <th style="min-width: 50px; width: 51px; text-align: center; vertical-align: middle;">Quantity</th>
                    <th style="display: none">Available Qty (Stock)</th>
                    <th style="min-width: 110px; width: 115px; text-align: center; vertical-align: middle;"><small>(Vehicle)</small><br> Available Qty</th>
                    {{-- <th>Updated Rack Count</th> --}}
                    <th style="min-width: 80px; width: 90px; text-align: center; vertical-align: middle;">Loaded Qty</th>
                    <th style="min-width: 50px; width: 60px; text-align: center; vertical-align: middle;">Action</th>
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
                        <td style="display: none">{{ $vehicle }}</td>
                        <td style="text-align: center;">{{ $stock->racks_count }} </td>
                        <td style="text-align: center;">{{ round($stock->availbale_qty, 3) }}</td>
                        {{-- <td><input type="text" class="allow_decimal" value="0" id="{{ $stock->pm_stock_batch_id }}" onkeyup='changeSubTotal({{ $batchStock->available_quantity }},this.value,{{ $stock->pm_stock_batch_id }},{{ $stock->loaded_qty }})' onmouseup='changeSubTotal({{ $batchStock->available_quantity }},this.value,{{ $stock->pm_stock_batch_id }},{{ $stock->loaded_qty }})'/></td> --}}
                        <td style="display: none">{{ $stock->availbale_qty }}</td>
                        <td style="text-align: center;">{{ round($stock->availbale_qty, 3) }}</td>
                        {{-- <td><input type="text" class="allow_decimal" value="0" min=0/></td> --}}
                        <td style="text-align: center;">{{ $stock->loaded_qty }}</td>
                        <td style="text-align: center;"><button type='button' class='btn btn-xs btn-danger' onclick='removeDeliveryProducts({{ $stock->pm_stock_batch_id }},{{ $vehicle }})' value='Remove'><i class="fa fa-trash" aria-hidden="true"></i> Remove</button></td>
                        <td style="display: none">0</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-primary" onclick="saveDeliveryData({{ $vehicle }})">Add Product</button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
</div>

<script>
    $(document).ready(function() {
        $(".allow_decimal").on("input", function(evt) {
            var self = $(this);
            self.val(self.val().replace(/[^0-9\.]/g, ''));
            if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
                evt.preventDefault();
            }
        });
    });
</script>
