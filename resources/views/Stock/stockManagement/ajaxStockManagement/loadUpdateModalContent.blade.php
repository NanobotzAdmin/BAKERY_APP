{{--  <div class="form-group">
    <input type="hidden" value="{{ $batchId }}" id="batchIdPro"/>
        <label for="">Available Qty</label>
        <input type="number" class="form-control form-control-sm" id="updatedQty">
    </div>  --}}

@php
    $stockAvailable = App\StockBatch::find($batchId);
    $productSubCategory = App\SubCategory::find($stockAvailable->pm_product_sub_category_id);
    $productItemState_list = App\ProductItemState::get();
@endphp

<div class="form-group row">
    <div class="col-sm-2">
        <label for="">Product</label>
    </div>
    <label for="" class="col-sm-12 col-form-label" style="color: #ff9100; font-size: 16px; font-weight: bold; padding-top: 0; margin-top: -10px;">
        {{ $productSubCategory->sub_category_name }}
    </label>
</div>

<div class="form-group row">
    <div class="form-group col-sm-5">
        <label for="">Product Code</label>
        <input type="text" class="form-control" style="font-weight: bold; background-color: #f8f6f2; color: #000;" value="{{ $productSubCategory->product_code }}" disabled>
    </div>
    <div class="col-sm-1"></div>
    <div class="form-group col-sm-5">
        <label for="">Batch Code</label>
        <input type="text" class="form-control" style="font-weight: bold; background-color: #f8f6f2; color: #000;" value="{{ $stockAvailable->batch_code }}" disabled>
    </div>
</div>

<div class="form-group row">
    <label for="" class="col-sm-6 col-form-label" style="">Product Item State</label>
    <div class="col-sm-5 d-flex align-items-center">
        <select class="selec2 form-control" id="updateProductItemState">
            @foreach ($productItemState_list as $productItemState)
                <option value="{{ $productItemState->id }}" {{ $productItemState->id == $stockAvailable->pm_product_item_state_id ? 'selected' : ''}}>{{ $productItemState->item_name }}</option>
            @endforeach
        </select>
    </div>
</div>

<hr>{{-------------------------------------------------------------------------------------------------------------------------------------------------------}}

<div class="form-group row">
    <input type="hidden" value="{{ $batchId }}" id="batchIdPro"/>
    <label for="" class="col-sm-6 col-form-label">Available Stock Quantity</label>
    {{-- <label for="" class="col-form-label">:</label> --}}
    <div class="col-sm-5 d-flex align-items-center">
        <span class="badge" style="background-color: #e2f5e6; padding-left: 14px; padding-right: 14px;">
            <label class="col-form-label" id="stockAvailableToCalculate" style="font-size: 15px; font-weight: bold; letter-spacing: 1px; color: #05bd24; text-align: center; display: inline-block;">{{ $stockAvailable->available_quantity }}</label>
        </span>
    </div>
</div>
<div class="row">
    <div class="col-sm-5" style="display: block">
        <label for="" class="col-form-label">Quantity Update Action</label>
        <select class="selec2 form-control" id="stockAction" onchange="calculateStock()">
            <option value="0">-- select action --</option>
            <option value="1">Set zero &nbsp; <span style="">( 0 )</span></option>
            <option value="2">Add &nbsp; <span style="font-weight: bold;">( + )</span></option>
            <option value="3">Remove &nbsp; <span style="font-weight: bold;">( - )</span></option>
        </select>
    </div>
    <div class="col-sm-1"></div>
    <div class="col-sm-4" id="qtyAddingDiv" style="display: none">
        <label class="col-form-label" style="color: #bd0505">Enter Quantity</label>
        <input type="text" class="form-control form-control-sm" id="addingNewQty" style="color: #bd0505;" maxlength="7" onkeyup="calculateTot()" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" autocomplete="off">
    </div>
</div>
<br>
<div class="form-group row">
    <label for="" class="col-sm-6 col-form-label" style="">New Stock Quantity Will Be</label>
    {{-- <label for="" class="col-form-label">:</label> --}}
    <div class="col-sm-5 d-flex align-items-center">
        <span class="badge" style="background-color: #e2e9f5; padding-left: 14px; padding-right: 14px;">
            <label class="col-form-label" id="totStock" style="font-size: 15px; font-weight: bold; letter-spacing: 1px; color: #001b75; text-align: center; display: inline-block;">{{ $stockAvailable->available_quantity }}</label>
        </span>
    </div>
</div>

<hr>{{-------------------------------------------------------------------------------------------------------------------------------------------------------}}

<div class="form-group row">
    <label for="" class="col-sm-6 col-form-label" style="padding-right: 0;">Retail Price &nbsp; </label>
    {{-- <label for="" class="col-form-label">:</label> --}}
    <div class="input-group col-sm-5">
        <div class="input-group-prepend">
            <span class="input-group-text form-control-sm" id="basic-addon1" style="color: #000; background-color: #f8f6f2; font-size: 12px; font-weight: bold;">LKR</span>
        </div>
        <input type="text" class="form-control form-control-sm" id="updateRetailPrice" value="{{ $stockAvailable->retail_price }}" maxlength="10" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" autocomplete="off">
    </div>
</div>

<div class="form-group row">
    <label for="" class="col-sm-6 col-form-label" style="padding-right: 0;">Selling Price &nbsp; </label>
    {{-- <label for="" class="col-form-label">:</label> --}}
    <div class="input-group col-sm-5">
        <div class="input-group-prepend">
            <span class="input-group-text form-control-sm" id="basic-addon1" style="color: #000; background-color: #f8f6f2; font-size: 12px; font-weight: bold;">LKR</span>
        </div>
        <input type="text" class="form-control form-control-sm" id="updateSellingPrice" value="{{ $stockAvailable->selling_price }}" maxlength="10" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" autocomplete="off">
    </div>
</div>

<div class="form-group row">
    <label for="" class="col-sm-6 col-form-label" style="padding-right: 0;">Actual Cost &nbsp; </label>
    {{-- <label for="" class="col-form-label">:</label> --}}
    <div class="input-group col-sm-5">
        <div class="input-group-prepend">
            <span class="input-group-text form-control-sm" id="basic-addon1" style="color: #000; background-color: #f8f6f2; font-size: 12px; font-weight: bold;">LKR</span>
        </div>
        <input type="text" class="form-control form-control-sm" id="updateActualCost" value="{{ $stockAvailable->actual_cost }}" maxlength="10" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" autocomplete="off">
    </div>
</div>

<div class="form-group row">
    <label for="" class="col-sm-6 col-form-label" style="padding-right: 0;">Discounted Price &nbsp; </label>
    {{-- <label for="" class="col-form-label">:</label> --}}
    <div class="input-group col-sm-5">
        <div class="input-group-prepend">
            <span class="input-group-text form-control-sm" id="basic-addon1" style="color: #000; background-color: #f8f6f2; font-size: 12px; font-weight: bold;">LKR</span>
        </div>
        <input type="text" class="form-control form-control-sm" id="updateDiscountedPrice" value="{{ $stockAvailable->discounted_price }}" maxlength="10" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" autocomplete="off">
    </div>
</div>


<script>
    function calculateStock() {
        var stockAction = $("#stockAction").val();
        var stockAvailableToCalculate = $("#stockAvailableToCalculate").text();
        var stockAddingQty = $("#addingNewQty").val();

        if (stockAction == 0) {
            swal("Sorry!", "Select Valid method!", "warning");
        } else if (stockAction == 1) {
            $("#qtyAddingDiv").css("display", "none");
            $("#addingNewQty").val(0);
            $("#totStock").text('0');
        } else if (stockAction == 2) {
            calculateTot();
            $("#qtyAddingDiv").css("display", "block");
        } else if (stockAction == 3) {
            calculateTot();
            $("#qtyAddingDiv").css("display", "block");
        }
    }


    function calculateTot() {
        var stockAction = $("#stockAction").val();
        var stockAvailableToCalculate = $("#stockAvailableToCalculate").text();
        var stockAddingQty = $("#addingNewQty").val();
        var stockTotQty = $("#totStock").text();

        if (stockAction == 2) {
            var tot = parseFloat(stockAvailableToCalculate) + parseFloat(stockAddingQty);
            if (isNaN(tot)) {
                $("#totStock").text(stockAvailableToCalculate);
            } else {
                $("#totStock").text(tot);
            }
        } else if (stockAction == 3) {
            var tot = parseFloat(stockAvailableToCalculate) - parseFloat(stockAddingQty);
            if (parseFloat(tot) < 0) {
                swal("Sorry", "Entered removing qunatity is higher than Available Stock! Please enter a valid amount.", "warning");
                $("#totStock").text(stockAvailableToCalculate);
                $("#addingNewQty").val(0);
            } else {
                if (isNaN(tot)) {
                    $("#totStock").text(stockAvailableToCalculate);
                } else {
                    $("#totStock").text(tot);
                }
            }
        }
    }
</script>
