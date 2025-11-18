<div class="modal-header">
    <h4 class="modal-title" id="exampleModalLabel">Update Interface Component</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <input type="hidden" class="form-control" value="{{ $interfaceComponent_OBJ->id }}" id="updateInterfaceComponent_componentID">

    <div class="form-group">
        <label>Topic</label>
        <select class="select2 form-control" id="updateInterfaceComponent_topic" onchange="loadInterfaces_2()">
            <option value="0">-- Select One --</option>
            @foreach ($interfaceTopic_list as $interfaceTopic)
                <option value="{{ $interfaceTopic->id }}" {{ $interfaceTopic->id == $interfaceComponent_OBJ->pmInterface->pm_interface_topic_id ? 'selected' : ''}}>{{ $interfaceTopic->topic_name }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label>Interface</label>
        @php
            use App\Interfaces;
            $interface_list_by_topicID = Interfaces::where("pm_interface_topic_id", $interfaceComponent_OBJ->pmInterface->pm_interface_topic_id)->get();
        @endphp
        <select class="select2 form-control" id="updateInterfaceComponent_interface">
            <option value="0">-- Select One --</option>
            @foreach ($interface_list_by_topicID as $interface)
                <option value="{{ $interface->id }}" {{ $interface->id == $interfaceComponent_OBJ->pm_interfaces_id ? 'selected' : ''}}>{{ $interface->interface_name }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label>Interface Component Name</label>
        <input type="text" class="form-control" id="updateInterfaceComponent_interfaceComponentName" value="{{ $interfaceComponent_OBJ->components_name }}" autocomplete="off">
    </div>
    <div class="form-group">
        <label>Interface Component ID</label>
        <input type="text" class="form-control" id="updateInterfaceComponent_interfaceComponentID" value="{{ $interfaceComponent_OBJ->component_id }}" autocomplete="off">
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-xs btn-warning" onclick="updateInterfaceComponentModalData()">Save Changes</button>
    <button type="button" class="btn btn-xs btn-secondary" data-dismiss="modal">Cancel</button>
</div>
