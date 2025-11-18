<div class="form-group col-md">
    <label class="form-group">&nbsp;</label><br>
    <label for="" style="font-weight: bold; color: #12a465;"><i class="fa fa-cubes" aria-hidden="true"></i>&nbsp; Available Stock: </label> <label style="font-size: 13px; font-weight: bold; font-family:'Roboto Slab', serif; letter-spacing: 1px; color: #12a465;" id="MODAL_AVAILABLE_QTY">&nbsp; {{ round($stockDetails->available_quantity, 3) }}</label>
    <br>
    <input type="hidden" value="{{ $stockDetails->stock_in_quantity }}" id="stockQty" />
    <input type="hidden" value="{{ $stockDetails->batch_code }}" id="batchCodeStock" />
    {{-- <label id="batchCodeStock">{{ $stockDetails->batch_code }}</label> --}}
</div>
