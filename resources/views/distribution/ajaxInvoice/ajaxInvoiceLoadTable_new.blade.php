<style>
    .availableQty12 {
        text-align: right !important;
        background: transparent !important;
        border: none !important;
        width: 100px !important;
        margin-right: -200px !important;
    }
    .sellingPriceCss {
        text-align: right !important;
        background: transparent !important;
        border: none !important;
        width: 100px !important;
        margin-right: -120px !important;
    }

    .mainTbl table {
        border: 1px solid #846f5d;
        /* table-layout: fixed; */
        /* width: 200px; */
    }
    .mainTbl th,td {
        border: 1px solid #846f5d;
        /* width: 100px; */
        /* overflow: hidden; */
    }
    .mainTbl tfoot .footTD {
        border-right: 1px solid #ffffff;
        border-left: 1px solid #ffffff;
    }
</style>


<div class="ibox-title">
    <h5>Add Products To Invoice</h5>
</div>
<div class="ibox-content">
    <br>
    <div class="row">
        <div class=" col-md-3">
            <div class="form-group">
                <label for="">ආපසු ලබාදුන් Rack ගණන</label>
                <input type="text" class="form-control form-control-sm allow_decimal" id="takenRackCount" value="0" onkeyup="changeTakenValue(this.value)">
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label for="">ලබාගත් Rack ගණන</label>
                <input type="text" class="form-control form-control-sm allow_decimal" id="givenRackCount" value="0" onkeyup="changeGivenValue(this.value)">
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <label style="color: #fff">&nbsp;</label><br>
                <button type="button" class="btn btn-secondary btn-sm" onclick="viewReturnModal()"><i class="fa fa-plus-circle" aria-hidden="true"></i> Add Return</button>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-check">

            </div>
        </div>

        {{-- <div class="col-md-4">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="loyalty">
                <label class="form-check-label" for="loyalty" style="font-weight: bold">Add Loyalty Discount</label>
                <br>
                <input type="checkbox" class="form-check-input" id="display">
                <label class="form-check-label" for="display" style="font-weight: bold">Add Display Discount</label>
            </div>
        </div> --}}
    </div>
    <br>

    <div class="table-responsive mainTbl">
        <table class="table sticky table-hover styled-table" style="width: 1300px;" id="TblAddReturnToTBL">
            <thead>
                <tr style="text-align: center;">
                    <th style="display: none">#</th>
                    <th style="min-width: 200px; width: 250px;">Product</th>
                    <th style="display: none">Batch</th>
                    <th style="min-width: 120px; width: 150px;">Quantity</th>
                    <th style="min-width: 220px; width: 250px;">Return Quantity</th>
                    <th style="min-width: 160px; width: 180px;">Available Quantity</th>
                    <th style="min-width: 160px; width: 180px;">Unit Price <small>(Retail)</small></th>
                    <th style="display: none">batchId</th>
                    <th style="display: none">DeliveryVehicleId</th>
                    <th style="min-width: 80px width: 100px;">Action</th>
                    <th style="min-width: 170px">Total Price <small>(LKR)</small></th>
                    <th style="display: none">discountQty</th>
                    <th style="display: none">discountPrice</th>
                    <th style="display: none">hiddenSalePrice</th>
                    <th style="display: none">ProductId</th>
                    <th style="display: none">IssuePrice</th>
                    <th style="display: none">ReturnPrice</th>
                </tr>
            </thead>
            <tbody id="tableInvoiceData">
                @php
                    $id = 0;
                    // Define the restricted Product Sub Category IDs
                    $restrictedIds = [51, 57, 59, 60, 61, 63];
                @endphp
                @foreach ($deliveryProductData as $products)
                    @php
                        $stock = App\StockBatch::find($products->pm_stock_batch_id);
                        $productSubCategory = App\SubCategory::find($stock->pm_product_sub_category_id);

                        // Check if the current product's sub-category ID is in the restricted list.
                        if (in_array($productSubCategory->id, $restrictedIds)) {
                            // If it is, skip this product and move to the next one.
                            continue;
                        }

                        $id++; // Increment the ID only for products that are not skipped.
                    @endphp
                    <input type="hidden" value="{{ $products->dm_delivery_vehicle_id }}" id="deliveryIDToReturn" />

                    {{-- **** NOTE: Changed "Selling Price" to "Retail Price" in below code ******************************************** --}}
                    <tr class="product" product-main-category="{{ $productSubCategory->pm_product_main_category_id }}" product-sub-category="{{ $productSubCategory->id }}">
                        <td style="display: none">{{ $id }}</td>
                        <td style="vertical-align: middle;"><b>{{ $productSubCategory->sub_category_name }}</b></td>
                        <td style="display: none">{{ $stock->batch_code }}</td>
                        <td style="vertical-align: middle; text-align: center;" class="product-quantity">
                            {{-- add 'forbiddenProduct_input' class for Main Categories ==>  4, 6  and  Sub-Categories ==> 59, 60, 61 --}}
                            @if($productSubCategory->pm_product_main_category_id == 4 || $productSubCategory->pm_product_main_category_id == 6 || $productSubCategory->id == 59 || $productSubCategory->id == 60 || $productSubCategory->id == 61)
                                <input type="text" class="form-control form-control-sm allow_decimal forbiddenProduct_input" value="0" name="qtyActual" id="qtyActual{{ $id }}" style=" text-align: left;" min="0" oninput="validity.valid||(value='');" onkeyup="changeSubTotal({{ $id }});">
                            @else
                                <input type="text" class="form-control form-control-sm allow_decimal" value="0" name="qtyActual" id="qtyActual{{ $id }}" style=" text-align: left;" min="0" oninput="validity.valid||(value='');" onkeyup="changeSubTotal({{ $id }});">
                            @endif
                            {{-- <input type="text" style=" text-align: left;" min="0" oninput="validity.valid||(value='');" onkeyup="changeSubTotal({{ $id }})" onmouseup="changeSubTotal({{ $id }})" class="form-control form-control-sm allow_decimal" value="0" name="qtyActual" id="qtyActual{{ $id }}"> --}}
                        </td>
                        <td>
                            <label>Qty</label>
                            <input type="text" style=" text-align: left;" min="0" oninput="validity.valid||(value='');" onkeyup="validateReturnQty({{ $id }})" onmouseup="validateReturnQty({{ $id }})" class="form-control form-control-sm allow_decimal" value="0" name="returnQty" id="returnQty{{ $id }}">

                            <label>Price</label>
                            <input type="hidden" name="hideReturnPrice" id="hideReturnPrice{{ $id }}" value="{{ $stock->retail_price }}" />
                            {{-- <select class="form-control" onchange="validateReturn({{ $id }})" id="returnCombo{{ $id }}">
                                <option value={{ $productSubCategory->selling_price }} selected>{{ $productSubCategory->selling_price }} - Selling Price</option>
                                <option value={{ $productSubCategory->discounted_price }}>{{ $productSubCategory->discounted_price }} - Discounted Price</option>
                                <option value='custom'>Custom price</option>
                            </select> --}}
                            <select class="form-control" onchange="validateReturn({{ $id }})" id="returnCombo{{ $id }}">
                                <option value={{ $stock->retail_price }} selected>{{ $stock->retail_price }} - Retial Price</option>
                                {{-- <option value={{ $stock->discounted_price }}>{{ $stock->discounted_price }} - Discounted Price</option> --}}
                                {{-- <option value='custom'>Custom price</option> --}}
                            </select>
                            <div id='returnPriceDiv{{ $id }}'></div>
                        </td>

                        <td style="vertical-align: middle; text-align: center;" name="product_available"> <input type="text" class="form-control form-control-sm availableQty12" value="{{ round($products->availbale_qty, 3) }}" id="availableQty" name="availableQty" disabled /></td>
                        <td style="vertical-align: middle; text-align: center;"> <input type="text" class="form-control form-control-sm sellingPriceCss" value="{{ $stock->retail_price }}" name="sellingPrice" id="sellingPrice{{ $id }}" disabled /></td>

                        <td style="display: none">{{ $stock->id }}</td>
                        <td style="display: none">{{ $products->dm_delivery_vehicle_id }}</td>
                        <td></td>
                        <td style="vertical-align: middle; text-align: right; font-weight: bold; letter-spacing: 2px;" id="qtyTot{{ $id }}">0.00</td>

                        {{-- <td style="display: none"><input type="text" value="{{ $productSubCategory->discountable_qty }}" name="discountable_qty" disabled /></td> --}}
                        <td style="display: none"><input type="text" value="999999" name="discountable_qty" disabled /></td> {{-- <== BROKE THE DISCOUNTABLE PRICE ADDING LOGIC BY SETTING "999999" --}}
                        <td style="display: none"><input type="text" value="{{ $productSubCategory->discounted_price }}" name="discounted_price" disabled /></td>
                        <td style="display: none"><input type="text" value="{{ $stock->retail_price }}" name="sellingPriceNormal" id="sellingPriceNormal{{ $id }}" disabled /></td>

                        <td style="display: none">{{ $productSubCategory->id }}</th>
                        <td style="display: none" id="IssueTot{{ $id }}">0.0</td>
                        <td style="display: none" id="returnTot{{ $id }}">0.0</td>

                        {{-- <td>1</td>
                        <td>Fruit Cake</td>
                        <td><input type="text" class="form-control form-control-sm"></td>
                        <td><input type="text" class="form-control form-control-sm"></td>
                        <td><input type="text" class="form-control form-control-sm"></td> --}}
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td style="display: none"></td>
                    <td style="display: none"></td>
                    <td style="border-right: 1px solid #ffffff;"></td>
                    <td class="footTD"></td>
                    <td class="footTD"></td>
                    <td class="footTD"></td>
                    <td class="footTD"></td>
                    <td style="display: none"></td>
                    <td style="text-align: right; font-weight: bold; color: #000;">TOTAL</td>
                    <td style="display: none"><label id="totSub">0.00</label></td>
                    <td style="display: none"></td>

                    <td style="display: none"><input type="text" value="" id="totIssue" /></td>
                    <td style="display: none"><input type="text" value="" id="totReturn" /></td>
                    <td style="display: none"><input type="text" value="" id="totDiscount" /></td>
                    <td style="font-weight: bold; text-align: right; letter-spacing: 2px; color: #000;"><b>LKR &nbsp;&nbsp;</b> <label id="totSubReal">0.00</label></td>

                    <td style="display: none"><input type="text" value="" id="totDiscountDisplay" /></td>
                    <td style="display: none"></td>
                    <td style="display: none"></td>
                    <td style="display: none"></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <br>
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#generateInvoiceModal" data-backdrop="static" onclick="generateInvoiceModal()">Proceed To Checkout &nbsp;<i class="fa fa-arrow-circle-right" aria-hidden="true"></i></button>
                {{-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#generateInvoiceModal" data-backdrop="static" onclick="saveInvoiceData()">Genarate Invoice</button> --}}
            </div>
        </div>
    </div>
    <br>
    <br>
</div>


<script>
    $(document).ready(function() {
        // An array of the sub-category IDs you want to restrict.
        const restrictedSubCategoryIds = [51, 57, 59, 60, 61, 63];

        // Loop through each restricted ID.
        restrictedSubCategoryIds.forEach(function(id) {
            // Find the table row with the matching 'product-sub-category' attribute.
            const row = $(`tr[product-sub-category="${id}"]`);

            // Find all input and select elements within that row and disable them.
            row.find('input, select').prop('disabled', true);

            // (Optional but recommended) Add a visual style to indicate the row is not interactive.
            row.find('td').css({
                'background-color': '#f2d8d8', // A light gray background
                'cursor': 'not-allowed'        // Show a 'not-allowed' cursor on hover
            });
        });
    });


    $(document).ready(function() {
        $('#loyalty').change(function() {
            changeSubTotal();
        });

        $('#display').change(function() {
            changeSubTotal();
        });

        $('.availableQty12').width(30);
        $('.sellingPriceCss').width(30);

        $(".allow_decimal").on("input", function(evt) {
            var self = $(this);
            self.val(self.val().replace(/[^0-9\.]/g, ''));
            if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which >
                57)) {
                evt.preventDefault();
            }
        });
    });


    function viewReturnModal() {
        var deliverVehicle = $("#deliveryIDToReturn").val();
        var csrf_token = $("#csrf_token").val();
        jQuery.ajax({
            url: "{{ url('/viewReturnModal') }}",
            type: "POST",
            data: {
                "_token": csrf_token,
                "deliverVehicle": deliverVehicle,
                "url": "distribution.ajaxInvoice.ajaxLoadProductsToReturn_new",
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
                $('#returnModal').modal('show');
                $("#returnModalContent").html(data);
            }
        });
    }
</script>
