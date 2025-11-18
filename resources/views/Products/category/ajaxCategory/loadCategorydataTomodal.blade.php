@if ($categoryType == 'mainCategory')

    <form action="updateMainCategory" method="POST">
        {{ csrf_field() }}
        <div class="modal-header">
            <h4 class="modal-title" id="exampleModalLabel">Update Main Category</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="category"> Main Category Name</label>
                <input type="text" class="form-control" id="category" value="{{ $CategoryData->main_category_name }}" name="MODAL_MAIN_CATEGORY_NAME" autocomplete="off"/>
                <input type="hidden" value="{{ $CategoryData->id }}" name="MODAL_MAIN_CATEGORY_ID" />
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-warning">Update Category</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
    </form>

@else

    <?php
        $mainCategoryCombo = App\MainCategory::all();
    ?>
    <form action="updateSubCategory" method="POST">
        {{ csrf_field() }}
        <div class="modal-header">
            <h4 class="modal-title" id="exampleModalLabel">Update Sub Category</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <input type="hidden" value="{{ $CategoryData->id }}" name="MODAL_SUBCATEGORY_UPDATE_ID" />
                <label for="sCategory"> Sub Category Name</label>
                <input type="text" class="form-control" id="sCategory" value="{{ $CategoryData->sub_category_name }}"
                    name="MODAL_SUBCATEGORY_NAME">
            </div>
            <div class="form-group">
                <label for="category"> Select Main Category</label>
                <select class="select2 form-control" name="MODAL_SUBCATEGORY_MAINCATEGORY_SELECT">
                    @foreach ($mainCategoryCombo as $mainCategoryList)
                        <option value="{{ $mainCategoryList->id }}"
                            {{ $CategoryData->pm_product_main_category_id == $mainCategoryList->id ? 'selected' : '' }}>
                            {{ $mainCategoryList->main_category_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="productCode"> Product Code</label>
                <input type="text" class="form-control" id="MODAL_PRODUCT_CODE" name="MODAL_PRODUCT_CODE"
                    value="{{ $CategoryData->product_code }}">
            </div>
            <div class="form-group">
                <label for="duration"> Expire Duration</label>
                <input type="number" class="form-control" id="duration" value="<?php echo isset($CategoryData->expire_in_days) ? $CategoryData->expire_in_days : '0'; ?>" name="MODAL_SUBCATEGORY_DURATION" maxlength="12" oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" autocomplete="off">
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="">Selling Price</label>
                    <input type="text" class="form-control" name="MODAL_SELLING_PRICE" value="<?php echo isset($CategoryData->selling_price) ? $CategoryData->selling_price : '0'; ?>" maxlength="12" oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" autocomplete="off">
                </div>
                <div class="form-group col-md-6">
                    <label for="">Retail Price</label>
                    <input type="text" class="form-control" name="MODAL_RETAIL_PRICE" value="<?php echo isset($CategoryData->retail_price) ? $CategoryData->retail_price : '0'; ?>" maxlength="12" oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" autocomplete="off">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="">Actual Cost</label>
                    <input type="text" class="form-control" name="MODAL_ACTUAL_COST" value="<?php echo isset($CategoryData->actual_cost) ? $CategoryData->actual_cost : '0'; ?>" maxlength="12" oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" autocomplete="off">
                </div>
                <div class="form-group col-md-6">
                    <label for="">Discounted Price</label>
                    <input type="text" class="form-control" name="MODAL_DISCOUNT_PRICE" value="<?php echo isset($CategoryData->discounted_price) ? $CategoryData->discounted_price : '0'; ?>" maxlength="12" oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" autocomplete="off">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="">Discountable Qty</label>
                    <input type="text" class="form-control" name="MODAL_DISCOUNTED_QTY" value="<?php echo isset($CategoryData->discountable_qty) ? $CategoryData->discountable_qty : '0'; ?>" maxlength="12" oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" autocomplete="off">
                </div>
                <div class="form-group col-md-6">
                    <label for="">Sequence No</label>
                    <input type="text" class="form-control" name="MODAL_SEQUENCE_NO" value="<?php echo isset($CategoryData->sequence_no) ? $CategoryData->sequence_no : ''; ?>" maxlength="2" oninput="this.value = this.value.replace(/[^0-9]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" autocomplete="off">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-warning">Update Product</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
    </form>

@endif
