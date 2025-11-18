 <div class="modal-header">
     <h4 class="modal-title" id="exampleModalLabel">Add Store Rack Count</h4>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
     </button>
 </div>

 @php
    $rack = 0;

    if (empty($rackCount)) {
        $rack = 0;
    } else {
        $rack = $rackCount->rack_count;
    }
 @endphp

 <div class="modal-body">
     <div class="form-group row">
         <label for="" class="col-sm-6 col-form-label">Available Rack Count :</label>
         <div class="col-sm-6">
             <label class="col-form-label" id="stockAvailableToCalculate">{{ $rack }}</label>
         </div>
     </div>
     <div class="form-group row">
         <label for="" class="col-sm-6 col-form-label">Add/Remove Rack Count :</label>
         <div class="col-sm-6">
             <select class="form-control form-control-sm" onchange="calculateStock()" id="stockAction">
                 <option value="0">Select One</option>
                 <option value="1">Set zero</option>
                 <option value="2">Add</option>
                 <option value="3">Remove</option>
             </select>
         </div>
     </div>
     <div class="form-group row">
         <label for="" class="col-sm-6 col-form-label">Updating Count :</label>
         <div class="col-sm-6">
             <input type="number" class="form-control-sm form-control" id="addingNewQty" onkeyup="calculateTot()"
                 value="0">
         </div>
     </div>
     <div class="form-group row">
         <label for="" class="col-sm-6 col-form-label">Updated Rack Count :</label>
         <div class="col-sm-6">
             <label class="col-form-label" id="totStock">0</label>
         </div>
     </div>
 </div>
 <div class="modal-footer">
     <button type="button" class="btn btn-xs btn-warning" onclick="updateStockRackCount(1)">Update Rack Count</button>
     <button type="button" class="btn btn-xs btn-secondary" data-dismiss="modal">Close</button>
 </div>
