<style>
    /* Accent Checkbox */
    .discount-item input[type="checkbox"] {
        accent-color: #00A36C;
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    /* Card Styling */
    .billing-card {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        font-family: 'Sawarabi Gothic', sans-serif;
    }

    .section-title {
        font-size: 15px;
        font-weight: 600;
        color: #333;
        letter-spacing: 1px;
    }

    .discount-container {
        background: #f9f9f9;
        border: 1px solid #e4e4e4;
        border-radius: 8px;
        padding: 14px;
        margin-bottom: 18px;
    }

    .discount-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .discount-item:last-child {
        margin-bottom: 0;
    }

    .discount-label {
        margin-left: 10px;
        font-size: 16px;
        color: #333;
        cursor: pointer;
        padding-top: 5px;
        padding-left: 5px;
    }

    /* Summary Lines */
    .summary-line, .discount-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 15px;
        padding: 6px 0;
    }

    .summary-line.border-bottom, .discount-line.border-bottom {
        border-bottom: 1px solid #dee2e6;
    }

    .discount-line {
        color: #00A36C;
    }

    .value {
        display: flex;
        align-items: center;
        gap: 4px; /* Small space between minus sign and value */
        color: #000;
        font-size: 17px;
    }

    .value .minus {
        color: #dc3545;
        font-weight: bold;
    }

    .minus {
        font-size: 8px;
        margin-right: 5px;
    }

    .grand-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 16px;
        font-weight: bold;
        font-size: 16px;
        color: #000;
    }

    .grand-total .amount {
        font-size: 18px;
        border-bottom: 3px double #000;
        padding-bottom: 2px;
        color: #000;
    }

    .small-text {
        font-size: 12px;
        color: #738d70;
        letter-spacing: 1px;
    }

    #genSubTotal {
        font-weight: bold;
    }
    .amount {
        font-size: 18px;
        font-weight: bold;
        color: #000000;
        border-bottom: 3px double #000;
        padding-bottom: 2px;
        display: flex;
        align-items: baseline;
        gap: 2px; /* Small space between LKR and value */
    }
    .currency {
        font-size: 12px;
        color: #666;
        font-weight: normal;
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


<div class="billing-card">

    <!-- Discount Section -->
    <div class="mb-3">
        <span class="section-title">DISCOUNT OPTIONS</span>
        <br><br>
        <div class="discount-container">
            <div class="discount-item">
                <input type="checkbox" id="loyalty" onchange="changeSubTotal()">
                <label class="discount-label" for="loyalty">Loyalty Discount</label>
            </div>
            <div class="discount-item">
                <input type="checkbox" id="display" onchange="changeSubTotal()">
                <label class="discount-label" for="display">Display Discount</label>
            </div>
            <div class="discount-item">
                <input type="checkbox" id="special" onchange="changeSubTotal()">
                <label class="discount-label" for="special">Special Discount</label>
            </div>
        </div>
    </div>
    <br>

    <!-- Billing Summary -->
    <div>
        <span class="section-title">BILLING SUMMARY</span>
        <br>
        <br>

        {{-- <span class="small-text" title="These totals are included in Subtotal and are shown for reference only.">
            Breakdown (Product Total)
        </span> --}}
        <!-- Non-Discountable Item Total Price -->
        <div class="summary-line fw-bold" style="color: #b63b47; font-size: 14px;">
            <span >Non-Discountable Items</span>
            <span class="" id="nonDiscountableItemTotalPrice" style="font-size: 17px;">0.00</span>
            {{-- <span class="value"><span class="minus">( - )</span> <span id="nonDiscountableItemTotalPrice">0.00</span></span> --}}
        </div>
        <!-- Discountable Item Total Price -->
        <div class="summary-line fw-bold border-bottom" style="color: #365099; font-size: 14px;">
            <span >Discountable Items</span>
            <span class="" id="discountableItemTotalPrice" style="font-size: 17px;">0.00</span>
        </div>

        <!-- Product Total -->
        <div class="summary-line">
            <span style="color: #353535;">Product Total</span>
            <span class="value" id="genProductTotal">{{ number_format($totSubReal + $totReturn, 2) }}</span>
        </div>

        <!-- Return Total -->
        <div class="summary-line border-bottom">
            <span style="color: #353535;">Return Total</span>
            <span class="value"><span class="minus">( - )</span> <span id="genReturnTotal">{{ number_format($totReturn, 2) }}</span></span>
        </div>

        <!-- Subtotal -->
        <div class="summary-line fw-bold">
            <span style="font-weight: bold; color: #353535; letter-spacing: 1px;">Subtotal</span>
            <span class="" id="genSubTotal" style="font-size: 17px; font-weight: bold; color: #353535;">{{ number_format($totSubReal, 2) }}</span>
        </div>

        <br>
        <!-- Discounts -->
        <div class="discount-line">
            <span>Loyalty Discount <span class="small-text">(2%)</span></span>
            <span class="value"><span class="minus">( - )</span> <span id="genLoyaltyDiscount">0.00</span></span>
        </div>

        <div class="discount-line">
            <span>Display Discount <span class="small-text">(2%)</span></span>
            <span class="value"><span class="minus">( - )</span> <span id="genDisplayDiscount">0.00</span></span>
        </div>

        <div class="discount-line border-bottom">
            <span>Special Discount <span class="small-text">(2%)</span></span>
            <span class="value"><span class="minus">( - )</span> <span id="genSpecialDiscount">0.00</span></span>
        </div>

        <!-- Grand Total -->
        <div class="grand-total">
            <span style="letter-spacing: 1px; font-size: 20px;">Grand Total</span>
            <div class="amount">
                <span class="currency">LKR&nbsp;</span>
                <span id="genTotal" style="font-size: 20px;">{{ number_format($totSubReal, 2) }}</span>
            </div>
        </div>

    </div>
</div>

<script>
    $(document).ready(function() {
        calculateTotal();
    });
</script>
