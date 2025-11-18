
<form action="updateSaleRep" method="POST">
        {{ csrf_field() }}
<div class="modal-header">
    <?php
    // $login = App\UserLogin::find($user->um_user_login_id);
    ?>
        <h4 class="modal-title" id="exampleModalLabel">Update Sales Rep</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <input type="hidden" value="{{ $saleRep->id }}" name="MODAL_SALEREP_ID"/>
            <label for="srname"> Sales Rep Name</label>
            <input type="text" class="form-control" id="srname" value="{{ $saleRep->sales_rep_name }}" name="MODAL_SALEREP_NAME">
        </div>
        <div class="form-group">
            <label for="nic">NIC Number</label>
            <input type="text" class="form-control" id="nic" value="{{ $saleRep->nic_no }}" name="MODAL_SALEREP_NIC">
        </div>
        <div class="form-group">
            <label for="cNo">Contact Number</label>
            <input type="text" class="form-control" id="cNo" value="{{ $saleRep->contact_no }}" name="MODAL_SALEREP_CONTACT">
        </div>
        <div class="form-group">
            <label for="cNo">Password</label>
            <input type="password" class="form-control" id="password" value="{{ $login->password }}" name="MODAL_SALEREP_PASSWORD">
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-warning">Update Sales Rep</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
    </div>
</form>
