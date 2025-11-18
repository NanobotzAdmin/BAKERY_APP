<div class="modal-header">
    <h4 class="modal-title" id="exampleModalLabel">Completion</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <div class="form-group">
        <label>End Milage</label><input class="form-control" type="text" id="endMilage" maxlength="15" placeholder="Enter end milage..." oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*?)\..*/g, '$1');" autocomplete="off"/>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-danger" onclick="completeDelivery({{ $deliveryId }})">Complete</button>

    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
</div>
