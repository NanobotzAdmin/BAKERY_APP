<form action="updateDriver" method="POST">
        {{ csrf_field() }}
<div class="modal-header">
    <?php

        $newDate = date("m/d/Y", strtotime($driverList->licence_expireration));
        ?>
        <h4 class="modal-title" id="exampleModalLabel">Update Driver</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <input type="hidden" value="{{ $driverList->id }}" name="MODAL_DRIVER_UPDATE_ID"/>
            <label for="dname"> Driver Name</label>
            <input type="text" class="form-control" id="dname" value="{{ $driverList->driver_name }}" name="MODAL_DRIVER_NAME">
        </div>
        <div class="form-group">
            <label for="licence">Licence Number</label>
            <input type="text" class="form-control" id="licence" value="{{ $driverList->licence_no }}" name="MODAL_DRIVER_LICENCE_NO">
        </div>
        <div class="form-group">
            <label for="ldate">Licence Expiration Date</label>
            <input type="date" class="form-control" id="ldate" value="{{ \Carbon\Carbon::createFromDate($driverList->licence_expireration)->format('Y-m-d')}}" name="MODAL_DRIVER_EXPIRY_DATE">
        </div>
        <div class="form-group">
            <label for="cNo">Contact Number</label>
            <input type="text" class="form-control" id="cNo" value="{{ $driverList->contact_number }}" name="MODAL_DRIVER_CONTACT">
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-warning">Update Driver</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
    </div>
</form>
