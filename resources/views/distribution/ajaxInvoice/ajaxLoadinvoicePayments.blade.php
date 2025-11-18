@php
    $netInvo = (float) $invoiceData->net_price - ((float) $invoiceData->discount + (float) $invoiceData->display_discount + (float) $invoiceData->special_discount);
@endphp
<div class="form-group col-md">
    <label style="">Invoice Net Amount</label><br>
    <label style="font-weight: bold; font-size: 11px; font-family:'Roboto Slab', serif; margin-left: 15px; margin-right: 5px;"> LKR </label> <label style="font-weight: bold; font-family:'Roboto Slab', serif; letter-spacing: 2px;"> {{ number_format($netInvo, 2) }}</label>
</div>

<div class="form-group col-md">
    <label style="">Paid Amount</label><br>
    <label style="font-weight: bold; font-size: 11px; font-family:'Roboto Slab', serif; margin-left: 15px; margin-right: 5px;"> LKR </label> <label style="font-weight: bold; color: #00a700; font-family:'Roboto Slab', serif; letter-spacing: 2px;"> {{ number_format($invoiceData->total_amout_paid, 2) }}</label>
</div>
@php
    $balance = (float) $netInvo - (float) $invoiceData->total_amout_paid;
@endphp
<div class="form-group col-md">
    <label style="">Balance to Pay</label><br>
    <label style="font-weight: bold; font-size: 11px; font-family:'Roboto Slab', serif; margin-left: 15px; margin-right: 5px;"> LKR </label> <label style="font-weight: bold; color: #b50505; font-family:'Roboto Slab', serif; letter-spacing: 2px;"> {{ number_format($balance, 2) }}</label>
    <input type="hidden" id="showindBalance" class="form-control" value="{{ number_format($balance, 2) }}">
</div>

<div class="form-group col-md">
    <label for="">Payment Amount</label>
    <div class="input-group mb-3">
        <div class="input-group-prepend">
            <span class="input-group-text form-control-sm" id="basic-addon1" style="background-color: #faf8e3; font-weight: bold; font-size: 12px; font-family:'Roboto Slab', serif; margin-left:">LKR</span>
        </div>
        <input type="text" class="form-control form-control-sm" id="paymentCredit" maxlength="20" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" autocomplete="off">
    </div>
</div>
