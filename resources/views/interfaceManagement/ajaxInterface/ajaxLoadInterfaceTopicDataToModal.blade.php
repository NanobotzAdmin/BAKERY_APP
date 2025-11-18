<div class="modal-header">
    <h4 class="modal-title" id="exampleModalLabel">Update Interface Topic</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <input type="hidden" class="form-control" value="{{ $interfaceTopic_OBJ->id }}" id="updateInterfaceTopic_interfaceTopicID">
    <div class="form-group">
        <label>Topic Name</label>
        <input type="text" class="form-control" value="{{ $interfaceTopic_OBJ->topic_name }}" id="updateInterfaceTopic_topic_name" autocomplete="off">
    </div>
    <div class="form-group">
        <label>Menu Icon</label>
        <input type="text" class="form-control" value="{{ $interfaceTopic_OBJ->menu_icon }}" id="updateInterfaceTopic_menu_icon" autocomplete="off">
    </div>
    <div class="form-group">
        <label>Section Class</label>
        <input type="text" class="form-control" value="{{ $interfaceTopic_OBJ->section_class }}" id="updateInterfaceTopic_section_class" autocomplete="off">
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-xs btn-warning" onclick="updateInterfaceTopicModalData()">Save Changes</button>
    <button type="button" class="btn btn-xs btn-secondary" data-dismiss="modal">Cancel</button>
</div>
