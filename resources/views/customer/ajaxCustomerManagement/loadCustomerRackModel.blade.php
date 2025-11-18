<div class="modal-header">
    <h4 class="modal-title" id="exampleModalLabel">Update Customer Rack</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

@php
    $rack = 0;

    if (empty($CustomerRackCount)) {
        $rack = 0;
    } else {
        $rack = $CustomerRackCount->rack_count;
    }
@endphp

<div class="modal-body">
    <div class="form-group row">
        <label for="" class="col-sm-6 col-form-label">Available Rack Count :</label>
        <div class="col-sm-6">
            <label class="col-form-label" id="stockAvailableToCalculate2">{{ $rack }}</label>
        </div>
    </div>
    <div class="form-group row">
        <label for="" class="col-sm-6 col-form-label">Add/Remove Rack Count :</label>
        <div class="col-sm-6">
            <select class="form-control form-control-sm" onchange="calculateStock2()" id="stockAction2">
                <option value="0">Select One</option>
                <option value="1">Set zero</option>
                <option value="2">Add</option>
                <option value="3">Remove</option>
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label for="" class="col-sm-6 col-form-label">Updating Count :</label>
        <div class="col-sm-6">
            <input type="number" class="form-control-sm form-control" id="addingNewQty2" onkeyup="calculateTot2()"
                value="0">
        </div>
    </div>
    <div class="form-group row">
        <label for="" class="col-sm-6 col-form-label">Updated Rack Count :</label>
        <div class="col-sm-6">
            <label class="col-form-label" id="totStock2">0</label>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-xs btn-warning" onclick="cutomerRackCountUpdate({{ $cusId }})">Update Rack Count</button>
    <button type="button" class="btn btn-xs btn-secondary" data-dismiss="modal">Close</button>
</div>

<script>
    function calculateStock2() {
        var stockAction = $("#stockAction2").val();
        var stockAvailableToCalculate = $("#stockAvailableToCalculate2").text();
        var stockAddingQty = $("#addingNewQty2").val();
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


    function calculateTot2() {
        var stockAction = $("#stockAction2").val();
        var stockAvailableToCalculate = $("#stockAvailableToCalculate2").text();
        var stockAddingQty = $("#addingNewQty2").val();
        var stockTotQty = $("#totStock2").text();

        if (stockAction == 2) {
            var tot = parseFloat(stockAvailableToCalculate) + parseFloat(stockAddingQty);
            if (isNaN(tot)) {
                $("#totStock2").text('0');
            } else {
                $("#totStock2").text(tot);
            }
        } else if (stockAction == 3) {
            var tot = parseFloat(stockAvailableToCalculate) - parseFloat(stockAddingQty);
            if (parseFloat(tot) < 0) {
                swal("Sorry!", "Enter valid amount!", "warning");
                $("#totStock2").text('0');
                $("#addingNewQty").val(0);
            } else {
                if (isNaN(tot)) {
                    $("#totStock2").text('0');
                } else {
                    $("#totStock2").text(tot);
                }
            }
        }
    }
</script>
