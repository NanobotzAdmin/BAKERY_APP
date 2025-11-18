<form method="POST" action="updateRoute">
    {{ csrf_field() }}
    <div class="modal-header">

        <h5 class="modal-title" id="exampleModalLabel">Update Route</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <div class="form-group col-md-12">
                <label for="">Route Name</label>
                <input type="text" class="form-control" name="routeNameUpdate" value="{{ $routeData->route_name }}" maxlength="150" autocomplete="off">
            </div>
        </div>
        <input type="hidden" value="{{ $routeData->id }}" name="routeHiddenId" />
        <div class="form-group row">
            <div class="form-group col-md-12">
                <label for="">Description</label>
                <textarea rows="5" class="form-control" name="routeDescriptionUpdate" maxlength="300" autocomplete="off">{{ $routeData->route_description }}</textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-warning">Update</button>
    </div>
</form>
