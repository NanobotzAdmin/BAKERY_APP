<select class="select2_demo_3 form-control " id="proList">
    <option value="0">-- Select One --</option>
    @foreach ($productList as $product)
        <option value="{{ $product->id }}">{{ $product->sub_category_name }}</option>
    @endforeach
</select>

<script>
    $(".select2_demo_3").select2({
        placeholder: "Select a state",
        allowClear: true
    });
</script>
