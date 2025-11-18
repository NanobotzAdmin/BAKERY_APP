<div class="form-row">
    <div class="form-group col-md-6">
        <label for="">Product Code</label>
        <input type="text" class="form-control" style="font-weight: bold;" value="{{ $proDetails->product_code }}" disabled id="productCode">
    </div>
    <div class="form-group col-md-6">
        <label for="">Batch Code</label>
        <input type="text" class="form-control" style="font-weight: bold;" value="{{ $proDetails->product_code }} - {{ $proCount }}" name="batchCode" id="batchCode" disabled>
        <input type="hidden" class="form-control" value="{{ $proDetails->product_code }} - {{ $proCount }}" name="batchCodeHidden" id="batchCodeHidden">
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label for="">Retail Price (Rs.)</label>
        <input type="text" class="form-control" style="color: #00b800;" value="{{ $proDetails->retail_price }}" name="retailPrice" maxlength="15" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" autocomplete="off">
    </div>
    <div class="form-group col-md-6">
        <label for="">Selling Price (Rs.)</label>
        <input type="text" class="form-control" style="color: #00b800;" value="{{ $proDetails->selling_price }}" name="sellingPrice" maxlength="15" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" autocomplete="off">
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label for="">Actual Cost (Rs.)</label>
        <input type="text" class="form-control" style="color: #00b800;" value="{{ $proDetails->actual_cost }}" name="actualCost" maxlength="15" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" autocomplete="off">
    </div>
    <div class="form-group col-md-6">
        <label for="">Discounted Price (Rs.)</label>
        <input type="text" class="form-control" style="color: #00b800;" value="{{ $proDetails->discounted_price }}" name="discountedPrice" maxlength="15" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" autocomplete="off">
    </div>
</div>
