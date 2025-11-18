
<style>
    #loyalty, #display, #special {
        accent-color: #00A36C;
    }

    .small-text {
        /* font-weight: bold; */
        font-size: 0.6em;
        color: #8f4949;
    }
</style>

{{-- check is there any forbidden products added --}}
{{-- @if ($is_forbiddenItemsAdded == 'true') --}}
    {{-- <div class="col-md"> --}}
        {{-- <div class="form-check">
            <input type="checkbox" class="form-check-input" id="loyalty" onchange="changeSubTotal()" disabled>
            <label class="form-check-label" for="loyalty">Add Loyalty Discount</label>
            <br>
            <input type="checkbox" class="form-check-input" id="display" onchange="changeSubTotal()" disabled>
            <label class="form-check-label" for="display">Add Display Discount</label>
        </div> --}}
        {{-- <small style="color: red;">*There are no discount options available for selected product lits.</small><br> --}}
        {{-- <small style="color: red;">( Not available for:- Cakes, Noodles )</small> --}}
    {{-- </div> --}}
{{-- @else --}}


<div class="col-md billing-summary-container" style="font-family: 'Sawarabi Gothic', sans-serif;">

    <div>
        <span class="d-block" style="color: #c81bd8; font-size: 11px; font-weight: bold; letter-spacing: 1px;">APPLY DISCOUNT %</span>
        <div style="margin-top: 10px; max-width: 250px;">
            <select class="form-control select2" id='custom_discount' onchange="changeSubTotal()">
                <option value="0">No Discount</option>
                <option value="25">25% Off</option>
                <option value="27">27% Off</option>
            </select>
        </div>
    </div>

    <hr class="my-3">

    <div class="d-flex flex-column" style="gap: 8px;">

        <div class="d-flex justify-content-between align-items-center">
            <span style="font-size: 15px;">Product Total</span>
            <div>
                <span style="color: #1b1b1b; font-size: 11px;"></span>
                <span id="genProductTotal" style="font-size: 15px; color: #1b1b1b; letter-spacing: 1px;">
                    {{ number_format($totSubReal + $totReturn, 2) }}
                </span>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center pb-1" style="border-bottom: 1px solid #dee2e6;">
            <span style="font-size: 15px;">Return Total</span>
            <div>
                <span style="color: #1b1b1b; font-size: 11px;">( - )&nbsp;&nbsp;</span>
                <span id="genReturnTotal" style="font-size: 15px; color: #1b1b1b; letter-spacing: 1px;">
                    {{ number_format($totReturn, 2) }}
                </span>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-1" style="font-weight: bold;">
            <span style="font-size: 15px;">Subtotal</span>
            <div>
                <span style="color: #1b1b1b; font-size: 11px;"></span>
                <span id="genSubTotal" style="font-size: 15px; color: #1b1b1b; letter-spacing: 1px;">
                    {{ number_format($totSubReal, 2) }}
                </span>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center pb-1" style="border-bottom: 1px solid #dee2e6;">
            <span style="font-size: 15px; color: #c81bd8;">Discount</span>
            <div>
                <span style="font-size: 11px; color: #c81bd8;">( - )&nbsp;&nbsp;</span>
                <span id="genCustomDiscount" style="font-size: 15px; color: #c81bd8; letter-spacing: 1px;">0.00</span>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-2 pt-2">
            <span style="font-weight: bold; font-size: 16px; color: #000000;">GRAND TOTAL</span>
            <div style="border-bottom: 3px double #000;">
                <span style="font-weight: bold; color: #1b1b1b; font-size: 11px;">LKR&nbsp;</span>
                <label class="form-label mb-0" id="genTotal" style="font-size: 18px; font-weight: bold; color: #000000; letter-spacing: 1px;">
                    {{ number_format($totSubReal, 2) }}
                </label>
            </div>
        </div>

    </div>
</div>
