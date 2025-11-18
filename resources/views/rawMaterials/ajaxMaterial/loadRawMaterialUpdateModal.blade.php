{{--  <div class="form-group">
    <input type="hidden" value="{{ $batchId }}" id="batchIdPro"/>
        <label for="">Available Qty</label>
        <input type="number" class="form-control form-control-sm" id="updatedQty">
    </div>  --}}


<div class="form-group row">
    <input type="hidden" value="{{ $materialID }}" id="batchIdPro" />
    <?php
    $stockAvailable = App\RawMaterials::find($materialID);

    ?>
    <label for="" class="col-sm-6 col-form-label" style="font-weight: bold;">Available Stock :</label>
    <div class="col-sm-6">
        <label id="stockAvailableToCalculate" style="font-weight: bold; font-size: 15px;">{{ number_format($stockAvailable->available_count) }}</label>
    </div>
</div>
<div class="form-group row">
    <label for="" class="col-sm-6 col-form-label" style="font-weight: bold;">Add / Remove :</label>
    <div class="col-sm-6">
        <select class="selec2 form-control" id="stockAction" onchange="calculateStock()">
            <option value="0">-- Select One --</option>
            <option value="1">Set zero</option>
            <option value="2">Add</option>
            <option value="3">Remove</option>
        </select>
    </div>
</div>
<div class="form-group row" id="qtyAddingDiv" style="display: none">
    <div class="row">
        <label for="" class="col-sm-6 col-form-label" style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;Quantity :</label>
        <div class="col-sm-5">
            <input type="text" class="form-control form-control-sm" id="addingNewQty" onkeyup="calculateTot()" maxlength="15" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" autocomplete="off">
        </div>
    </div>
</div>
<div class="form-group row">
    <label for="" class="col-sm-6 col-form-label" style="font-weight: bold;">Update Stock Will Be :</label>
    <div class="col-sm-6">
        <label id="totStock" style="font-weight: bold; font-size: 15px;">0.00</label>
    </div>
</div>


<script>
    function calculateStock() {
        var stockAction = $("#stockAction").val();
        var stockAvailableToCalculate = $("#stockAvailableToCalculate").text().replace(/,/g, '');
        var stockAddingQty = $("#addingNewQty").val().replace(/,/g, '');
        if (stockAction == 0) {
            swal("Sorry!", "Select Valid method!", "warning");
        } else if (stockAction == 1) {
            $("#qtyAddingDiv").css("display", "none");
            $("#addingNewQty").val(0);
            $("#totStock").text('0');
        } else if (stockAction == 2) {
            $("#qtyAddingDiv").css("display", "block");
        } else {
            $("#qtyAddingDiv").css("display", "block");
        }
    }


    function calculateTot() {
        var stockAction = $("#stockAction").val();
        var stockAvailableToCalculate = parseFloat($("#stockAvailableToCalculate").text().replace(/,/g, ''));
        var stockAddingQty = parseFloat($("#addingNewQty").val().replace(/,/g, ''));
        var stockTotQty = parseFloat($("#totStock").text().replace(/,/g, ''));

        if (stockAction == 2) {
            var tot = stockAvailableToCalculate + stockAddingQty;
            if (isNaN(tot)) {
                $("#totStock").text('0');
            } else {
                $("#totStock").text(tot.toLocaleString()); // Show tot with thousand separators
            }
        } else if (stockAction == 3) {
            var tot = stockAvailableToCalculate - stockAddingQty;
            if (parseFloat(tot) < 0) {
                swal("Sorry!", "Enter valid amount!", "warning");
                $("#totStock").text('0');
                $("#addingNewQty").val(0);
            } else {
                if (isNaN(tot)) {
                    $("#totStock").text('0');
                } else {
                    $("#totStock").text(tot.toLocaleString()); // Show tot with thousand separators
                }
            }
        }
    }

</script>
