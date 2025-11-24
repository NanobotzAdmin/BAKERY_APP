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
        <h4 class="modal-title" id="exampleModalLabel">Update Sub Category</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <form action="{{ url('/updateSubCategory') }}" method="POST">
        {{ csrf_field() }}
        <div class="modal-body">
            <input type="hidden" name="MODAL_SUBCATEGORY_UPDATE_ID" value="{{ $CategoryData->id }}">
            <div class="form-group">
                <label for="MODAL_SUBCATEGORY_NAME">Sub Category Name</label>
                <input type="text" class="form-control form-control-sm" id="MODAL_SUBCATEGORY_NAME"
                    name="MODAL_SUBCATEGORY_NAME" value="{{ $CategoryData->sub_category_name }}" autocomplete="off">
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
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Update Sub Category</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
    </form>
@endif