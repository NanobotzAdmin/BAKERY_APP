@foreach ($interfaceList as $interfaces)
    <div class="form-group row">
        <div class="col-sm-9" style="border-bottom: #cccccc; border-bottom-style: ridge">
            <label class=" control-label" style="margin-top: 10px;">{{ $interfaces->interface_name }}</label>
        </div>
        <div class="col-sm-1" style="align-content: center;">
            <button type="button" class="btn btn-info btn-xs interface-view-btn" id="intbtn{{ $interfaces->id }}" onclick="loadInterfaceComponents({{ $interfaces->id }},{{ $userRole }})">View</button>
        </div>
    </div>
@endforeach
