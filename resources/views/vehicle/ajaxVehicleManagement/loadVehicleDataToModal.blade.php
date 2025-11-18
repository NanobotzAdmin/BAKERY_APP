<form action="updateVehicle" method="POST">
        {{ csrf_field() }}
<div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel">Update Vehical</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <input type="hidden" value="{{ $vehicle->id }}" name="MODAL_VEHICLE_UPDATE_ID"/>
            <label for="rname"> Registration Number</label>
            <input type="text" class="form-control" id="rname" name="MODAL_REGI_NO" value="{{ $vehicle->reg_number }}">
        </div>
        <div class="form-group">
            <label for="eNo">Engine Number</label>
            <input type="text" class="form-control" id="eNo" name="MODAL_ENGINE_NO" value="{{ $vehicle->engine_number }}">
        </div>
        <div class="form-group">
            <label for="chNo">Chassis Number</label>
            <input type="text" class="form-control" id="chNo" name="MODAL_CHASI_NO" value="{{ $vehicle->chassis_number }}">
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-warning">Update Vehicle</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
    </div>
</form>
