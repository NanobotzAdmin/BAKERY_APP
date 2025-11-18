<form action="updateVehicle" method="POST">
    {{ csrf_field() }}
    <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel">Update Configuration</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <input type="hidden" value="{{ $commissionSettings_OBJ->id }}" id="update_commissionSettings_id" name="update_commissionSettings_id" />
            <label for="min_sales_amount">Minimum Sales Amount</label>
            <input type="text" class="form-control" id="update_min_sales_amount" name="update_min_sales_amount" value="{{ number_format($commissionSettings_OBJ->min_sales_amount, 2, '.', ',') }}" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" autocomplete="off" required>
        </div>
        <div class="form-group">
            <label for="max_sales_amount">Maximum Sales Amount</label>
            <input type="text" class="form-control" id="update_max_sales_amount" name="update_max_sales_amount" value="{{ number_format($commissionSettings_OBJ->max_sales_amount, 2, '.', ',') }}" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" autocomplete="off" required>
        </div>
        <div class="form-group">
            <label for="commission_rate">Commission Rate</label>
            <div class="input-group mb-3">
                <input type="text" class="form-control form-control-sm" id="update_commission_rate" name="update_commission_rate" value="{{ $commissionSettings_OBJ->commission_rate }}" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" autocomplete="off" required>
                <div class="input-group-prepend">
                    <span class="input-group-text form-control-sm" id="basic-addon1" style="background-color: #fff0c0"><b>%</b></span>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-warning btn-sm" onclick="updateCommissionSettings()">Update Configuration</button>
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
    </div>
</form>
