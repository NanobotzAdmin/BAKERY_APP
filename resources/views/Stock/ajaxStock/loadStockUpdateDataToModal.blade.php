<form action="updateStockData" method="POST">
    {{ csrf_field() }}
    <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel">Update Stock</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="">Main Category</label>
                <select class="selec2 form-control" name="MODAL_MAIN_CATEGORY" id="MODAL_MAIN_CATEGORY" onchange="loadSubCategories('updatePage')">
                    <option value="0">-- Select One --</option>
                    @foreach ($mainCategory as $mainCategories)
                        <option value="{{ $mainCategories->id }}"
                            {{ $maincategoryId == $mainCategories->id ? 'selected' : '' }}>
                            {{ $mainCategories->main_category_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-6">
                <label for="">Sub Category</label>
                <select class="selec2 form-control" name="MODAL_SUB_CATEGORY" id="MODAL_SUB_CATEGORY" onchange="loadProductDetails('updatePage')">
                    <option value="0">-- Select One --</option>
                    @foreach ($subCategory as $subCategories)
                        <option value="{{ $subCategories->id }}"{{ $stockDetails->pm_product_sub_category_id == $subCategories->id ? 'selected' : '' }}>{{ $subCategories->sub_category_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div id="MODAL_productDetailsLoadingDiv" class="form-row">

        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="">State</label>
                <select class="selec2 form-control" name="MODAL_ITEM_STATUS" id="MODAL_ITEM_STATUS">
                    @foreach ($productListstatus as $status)
                        <option value="{{ $status->id }}" {{ $stockDetails->pm_product_item_state_id == $status->id ? 'selected' : '' }}>{{ $status->item_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="">Adding Quantity</label>
                <input type="text" class="form-control" value="{{ $stockDetails->stock_in_quantity }}" name="MODAL_STOCK_IN_QTY" id="MODAL_STOCK_IN_QTY">
            </div>
        </div>
        <input type="hidden" value="{{ $stockDetails->id }}" name="STOCK_IN_UPDATE_ID" />

        {{-- <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="">Selling Price</label>
                    <input type="text" class="form-control" value="{{ $stockDetails->selling_price }}" name="MODAL_SELLING_PRICE" id="MODAL_SELLING_PRICE">
                </div>
                <div class="form-group col-md-6">
                    <label for="">Actual Cost</label>
                    <input type="text" class="form-control" value="{{ $stockDetails->actual_cost }}" name="MODAL_ACTUAL_COST" id="MODAL_ACTUAL_COST">
                </div>
            </div>
            <div class="form-group" id="data_1">
                <label for="">Retailing Price</label>
                <input type="text" class="form-control" value="{{ $stockDetails->retail_price }}" name="MODAL_RETAILING_PRICE" id="MODAL_RETAILING_PRICE">
            </div> --}}


        <div class="form-group" id="MODAL_productExpiryDateLoadingDiv">
            <label for="">Expire Date</label>
            <?php
            $stockExpiry = Carbon\Carbon::parse($stockDetails->expire_date)->format('Y-m-d');
            //   $dateExpiryUpdate = $stockExpiry;
            ?>
            <div class="input-group date">
                <input type="date" class="form-control" value="{{ $stockExpiry }}" name="expiryDate"
                    id="expiryDate">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-warning">Update</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
    </div>
</form>
