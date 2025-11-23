@if ($categoryType == 'mainCategory')
    <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel">Update Main Category</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <form action="{{ url('/updateMainCategory') }}" method="POST">
        {{ csrf_field() }}
        <div class="modal-body">
            <input type="hidden" name="MODAL_MAIN_CATEGORY_ID" value="{{ $CategoryData->id }}">
            <div class="form-group">
                <label for="MODAL_MAIN_CATEGORY_NAME">Main Category Name</label>
                <input type="text" class="form-control" id="MODAL_MAIN_CATEGORY_NAME" name="MODAL_MAIN_CATEGORY_NAME"
                    value="{{ $CategoryData->main_category_name }}" autocomplete="off">
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Update</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
    </form>
@else
    <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel">Update Product</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <form action="{{ url('/updateSubCategory') }}" method="POST">
        {{ csrf_field() }}
        <div class="modal-body">
            <input type="hidden" name="MODAL_SUBCATEGORY_UPDATE_ID" value="{{ $CategoryData->id }}">
            <div class="form-group">
                <label for="MODAL_SUBCATEGORY_NAME">Product Name</label>
                <input type="text" class="form-control form-control-sm" id="MODAL_SUBCATEGORY_NAME"
                    name="MODAL_SUBCATEGORY_NAME" value="{{ $CategoryData->sub_category_name }}" autocomplete="off">
            </div>
            <div class="form-group">
                <label for="MODAL_PRODUCT_CODE">Product Code</label>
                <input type="text" class="form-control form-control-sm" id="MODAL_PRODUCT_CODE"
                    name="MODAL_PRODUCT_CODE" value="{{ $CategoryData->product_code }}" autocomplete="off">
            </div>
            <div class="form-group">
                <label for="MODAL_SUBCATEGORY_MAINCATEGORY_SELECT">Main Category</label>
                <select class="select2 form-control form-control-sm" id="MODAL_SUBCATEGORY_MAINCATEGORY_SELECT"
                    name="MODAL_SUBCATEGORY_MAINCATEGORY_SELECT">
                    <option value="0">-- Select One --</option>
                    @foreach ($MainCategory as $ActiveCategory)
                        <option value="{{ $ActiveCategory->id }}"
                            {{ $ActiveCategory->id == $CategoryData->pm_product_main_category_id ? 'selected' : '' }}>
                            {{ $ActiveCategory->main_category_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="MODAL_SUBCATEGORY_DURATION">Expire Duration</label>
                <input type="number" class="form-control form-control-sm" id="MODAL_SUBCATEGORY_DURATION"
                    name="MODAL_SUBCATEGORY_DURATION"
                    value="{{ isset($CategoryData->expire_in_days) ? $CategoryData->expire_in_days : '' }}"
                    maxlength="12" autocomplete="off">
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="MODAL_SELLING_PRICE">Selling Price</label>
                    <input type="text" class="form-control form-control-sm" id="MODAL_SELLING_PRICE"
                        name="MODAL_SELLING_PRICE" value="{{ $CategoryData->selling_price }}" maxlength="12"
                        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                        autocomplete="off">
                </div>
                <div class="form-group col-md-6">
                    <label for="MODAL_RETAIL_PRICE">Retail Price</label>
                    <input type="text" class="form-control form-control-sm" id="MODAL_RETAIL_PRICE"
                        name="MODAL_RETAIL_PRICE" value="{{ $CategoryData->retail_price }}" maxlength="12"
                        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                        autocomplete="off">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="MODAL_ACTUAL_COST">Actual Cost</label>
                    <input type="text" class="form-control form-control-sm" id="MODAL_ACTUAL_COST"
                        name="MODAL_ACTUAL_COST" value="{{ $CategoryData->actual_cost }}" maxlength="12"
                        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                        autocomplete="off">
                </div>
                <div class="form-group col-md-6">
                    <label for="MODAL_DISCOUNT_PRICE">Discounted Price</label>
                    <input type="text" class="form-control form-control-sm" id="MODAL_DISCOUNT_PRICE"
                        name="MODAL_DISCOUNT_PRICE" value="{{ $CategoryData->discounted_price }}" maxlength="12"
                        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                        autocomplete="off">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="MODAL_DISCOUNTED_QTY">Discountable Qty</label>
                    <input type="text" class="form-control form-control-sm" id="MODAL_DISCOUNTED_QTY"
                        name="MODAL_DISCOUNTED_QTY" value="{{ $CategoryData->discountable_qty }}" maxlength="12"
                        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                        autocomplete="off">
                </div>
                <div class="form-group col-md-6">
                    <label for="MODAL_SEQUENCE_NO">Sequence No</label>
                    <input type="text" class="form-control form-control-sm" id="MODAL_SEQUENCE_NO"
                        name="MODAL_SEQUENCE_NO" value="{{ $CategoryData->sequence_no }}" maxlength="2"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*?)\..*/g, '$1');"
                        autocomplete="off">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Update Product</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
    </form>
@endif