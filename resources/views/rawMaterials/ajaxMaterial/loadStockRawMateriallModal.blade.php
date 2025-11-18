<form method="POST" action="updateRawQuanity">
        {{ csrf_field() }}
<div class="modal-header">
    <h4 class="modal-title" id="exampleModalLabel">Add Stock to Materials</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">

    <div class="form-group">
        <label for="" style="font-weight: bold;">Material Name:</label><br>
        <label>{{ $rawMaterial->material_name }}</label>
    </div>

    <div class="form-group">
            <?php
            $product = App\SubCategory::find($rawMaterial->pm_product_sub_category_id);
            ?>
        <label for="" style="font-weight: bold;">Product:</label><br>
        <label>{{  $product->sub_category_name }}</label>
    </div>
    <input type="hidden" value="{{ $rawMaterial->id }}" name="materialModalId"/>
    <div class="form-group">
        <label for="" style="font-weight: bold;">Adding Quantity:</label>
        <input type="number" class="form-control" name="modalAvailableQty">
    </div>
</div>

<div class="modal-footer">
    <button type="submit" class="btn btn-primary">Add Quantity</button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
</div>
</form>
