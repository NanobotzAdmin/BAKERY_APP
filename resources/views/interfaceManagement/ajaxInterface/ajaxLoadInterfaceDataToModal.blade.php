<div class="modal-header">
    <h4 class="modal-title" id="exampleModalLabel">Update Interface</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <input type="hidden" class="form-control" value="{{ $interface_OBJ->id }}" id="updateInterface_interfaceID">
    <div class="form-group">
        <label>Topic</label>
        <select class="select2 form-control" id="updateInterface_topic">
            <option value="0">-- Select One --</option>
            @foreach ($interfaceTopic_list as $interfaceTopic)
                <option value="{{ $interfaceTopic->id }}" {{ $interfaceTopic->id == $interface_OBJ->pm_interface_topic_id ? 'selected' : ''}}>{{ $interfaceTopic->topic_name }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label>Interface Name</label>
        <input type="text" class="form-control" id="updateInterface_interface_name" value="{{ $interface_OBJ->interface_name }}" autocomplete="off">
    </div>
    <div class="form-group">
        <label>Interface URL</label>
        <input type="text" class="form-control" id="updateInterface_interface_URL" value="{{ $interface_OBJ->path }}" autocomplete="off">
    </div>
    <div class="form-group">
        <label>Icon Class</label>
        <input type="text" class="form-control" id="updateInterface_icon_class" value="{{ $interface_OBJ->icon_class }}" autocomplete="off">
    </div>
    <div class="form-group">
        <label>Title Class</label>
        <input type="text" class="form-control" id="updateInterface_title_class" value="{{ $interface_OBJ->tile_class }}" autocomplete="off">
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-xs btn-warning" onclick="updateInterfaceModalData()">Save Changes</button>
    <button type="button" class="btn btn-xs btn-secondary" data-dismiss="modal">Cancel</button>
</div>
