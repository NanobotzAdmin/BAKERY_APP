<div class="row">
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="">Product Name</label>
            <select class="form-control" id="productsReturn" onchange="loadProductId()">
                <option value="0">-- Select a product --</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}, {{ $product->retail_price }}, {{ $product->discountable_qty }}, {{ $product->discounted_price }}">
                        {{ $product->sub_category_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <input type="hidden" id="hiddenReturnUnitPrice" />
        <input type="hidden" id="hiddenDeliveryId" value="{{ $deliveryId }}" />
        <input type="hidden" id="returnProId" />

        <div class="form-group col-md-6">
            <label for="">Unit Price</label>
            {{--  <input type="text"  min=0 class="form-control allow_decimal" id="ReturnPrice2" placeholder="" oninput="validity.valid||(value='');" onkeyup="changeReturnQty(this.value)"  onmouseup="changeReturnQty(this.value)">  --}}
            <select class="form-control" id="returnPriceCombo" onchange="changeReturnQty(this.value)">
                <option value="select">-- Select a price --</option>
            </select>
        </div>
        <div class="form-group col-md-6">
            <label for="">Qty</label>
            <input type="text" min=0 class="form-control allow_decimal" id="ReturnQty" placeholder="" oninput="validity.valid||(value='');" onkeyup="changeReturnQty(this.value)" onmouseup="changeReturnQty(this.value)">
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $(".allow_decimal").on("input", function(evt) {
            var self = $(this);
            self.val(self.val().replace(/[^0-9\.]/g, ''));
            if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which >
                57)) {
                evt.preventDefault();
            }
        });
    });


    function changeReturnQty(input) {
        var productData = $("#productsReturn option:selected").val();
        var returnItem = $("#returnPriceCombo").val();
        if (returnItem == 'select') {
            swal("", "Please select a price.", "warning");
        } else {
            if (productData == 0) {
                swal("", " Please select a Product.", "warning");
            } else {
                var productArr = productData.split(',');
                var discountedQty = productArr[2];
                var discountedPrice = productArr[3];
                var retailPrice = productArr[1];

                if (parseFloat(input) >= parseFloat(discountedQty)) {
                    $("#hiddenReturnUnitPrice").val(returnItem);
                } else {
                    $("#hiddenReturnUnitPrice").val(returnItem);
                }
            }
        }
    }


    function loadProductId() {
        $('#returnPriceCombo').empty();
        var productData = $("#productsReturn option:selected").val();
        var productArr = productData.split(',');
        var proId = productArr[0];
        var retailPrice = productArr[1];
        var discounted = productArr[3];
        $("#returnProId").val(proId);
        $("#ReturnQty").val(0);
        $("#hiddenReturnUnitPrice").val(0);
        $("#ReturnPrice2").val(0);
        $('#returnPriceCombo').append(`
            <option value="select">-- Select a price --</option>
            <option value="${retailPrice}">${retailPrice} - Retail Price</option>
            `)
            // <option value="${discounted}">${discounted} - Discounted Price</option>
    }
</script>
