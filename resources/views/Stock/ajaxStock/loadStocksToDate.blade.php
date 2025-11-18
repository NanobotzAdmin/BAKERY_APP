@foreach ($stockBatch as $batch)
    @php
        $subCategory = App\SubCategory::find($batch->pm_product_sub_category_id);
        $proStatus = App\ProductItemState::find($batch->pm_product_item_state_id);
        $mainCategory = App\MainCategory::find($subCategory->pm_product_main_category_id);
    @endphp
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $batch->batch_code }}</td>
        <td>{{ $subCategory->sub_category_name }}</td>
        <td>{{ $proStatus->item_name }}</td>
        <td>{{ $batch->available_quantity }}</td>
        <td>{{ $batch->selling_price }}</td>
        <td>
            <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#updateStock" onclick="loadstockUpdateModal({{ $batch->id }} ,{{ $mainCategory->id }})">Update</button>
        </td>
    </tr>
@endforeach
