

 <form action="updateMaterial" method="POST">
{{ csrf_field() }}
<div class="modal-header">
    <h4 class="modal-title" id="exampleModalLabel">Update Raw Material</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <div class="form-group">
        <label for="" style="font-weight: bold;">Material Name:</label>
        <input type="text" class="form-control" name="Modal_Material_Name" value="{{ $rawMaterial->material_name }}">
    </div>
    <input type="hidden" value="{{ $rawMaterial->id }}" name="Modal_Material_Id"/>
    <div class="form-group">
        <label for="" style="font-weight: bold;">Product:</label><br>
        <?php
         $product = App\SubCategory::find($rawMaterial->pm_product_sub_category_id);
         ?>
       <label> {{ $product->sub_category_name }}</label>
        {{-- <select class="select2_demo_3 form-control" name="Modal_product" >
                @foreach ($subCategory as $subCategories)
                <option value="{{ $subCategories->id }}" {{$rawMaterial->pm_product_sub_category_id == $subCategories->id  ? 'selected' : ''}}>{{ $subCategories->sub_category_name }}</option>
          @endforeach
            </select> --}}
    </div>
    <div class="form-group">
        <label for="" style="font-weight: bold;">Available Count:</label>
        <input type="number" class="form-control" name="Modal_Available_count" value="{{ $rawMaterial->available_count }}" readonly>
    </div>
    <div class="form-group">
        <label for="" style="font-weight: bold;">Reorder Level:</label>
        <input type="number" class="form-control" name="Modal_Reorder_Count" value="{{ $rawMaterial->reorder_count }}">
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-warning">Update</button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
</div>
 </form>
