@php
    $netInvo = (float) $invoiceDetails->net_price - ((float) $invoiceDetails->discount + (float) $invoiceDetails->display_discount + (float) $invoiceDetails->special_discount);
    $dueAmount2 = (float) $netInvo - (float) $invoiceDetails->total_amout_paid;
@endphp

<style>
    /* --- Table CSS begins --- */
    .styled-table th:first-child {
        border-radius: 5px 0 0 0;
    }
    .styled-table th:last-child {
        border-radius: 0 5px 0 0;
    }
    .styled-table {
        border-collapse: collapse;
        margin: 25px 0;
        font-size: 14px;
        font-family:'Roboto Slab', serif;
        min-width: 400px;
    }
    .styled-table thead tr {
        background-color: #846f5d;
        color: #ffffff;
        text-align: left;
        font-size: 15px;
        font-family: 'Roboto Slab', serif;
        letter-spacing: 1px;
    }
    .styled-table th,
    .styled-table td {
        padding: 12px 15px;
    }
    .styled-table tbody tr {
        border-bottom: 1px solid #dddddd;
    }
    .styled-table tbody tr:nth-of-type(even) {
        background-color: #ffffff;
    }
    .styled-table tbody tr:last-of-type {
        border-bottom: 2px solid #846f5d;
    }
    .styled-table tbody tr:hover td {
        background-color: #faf6ec;
    }
</style>

<h4 style="font-family: Verdana, Geneva, sans-serif;"><label class="col-sm-3">Invoice No</label><label class="col-sm-9" style="color: #ff7300;">{{ $invoiceDetails->invoice_number }}</label></h4>
<h4 style="font-family: Verdana, Geneva, sans-serif;"><label class="col-sm-3">Invoice Type</label><label class="col-sm-9" style="color: #000000;">
    @if ($invoiceDetails->invoice_type == 1)
    Credit <span style="font-size: 22px;"> 💳</span>
    @elseif ($invoiceDetails->invoice_type == 2)
    Cash <span style="font-size: 20px;"> 💵</span>
    @elseif ($invoiceDetails->invoice_type == 3)
    Cheque <span style="font-size: 20px;"> 📒</span>
    @endif
    </label>
</h4>
<h4 style="font-family: Verdana, Geneva, sans-serif;"><label class="col-sm-3">Customer</label><label class="col-sm-9" style="color: #000000;">{{ $invoiceDetails->cmCustomer->customer_name }}</label></h4>

<div class="table-responsive" style="width: 100%;">
    <table class="table table-sm styled-table">
        <thead style="font-family: Verdana, Geneva, sans-serif;">
            <tr>
                <th style="width: 15px; padding-left: 30px;">#</th>
                <th style="min-width: 90px; padding-left: 30px;">Reciept No</th>
                <th style="min-width: 90px; padding-left: 30px;">Created By</th>
                <th style="min-width: 90px; padding-left: 30px;">Date</th>
                <th style="min-width: 90px; text-align: right; padding-right: 30px;">Amount</th>
            </tr>
        </thead>
        <tbody style="font-family: Verdana, Geneva, sans-serif;">
            @if ($invoicePayment->isNotEmpty())
                @foreach ($invoicePayment as $payementDetails)
                    <tr>
                        <td style="padding-left: 30px;">{{ $loop->iteration }}</td>
                        <td style="padding-left: 30px;">{{ $payementDetails->receipt_no }}</td>
                        <td style="padding-left: 30px;">{{ $payementDetails->umUserCreatedBy->first_name }} {{ $payementDetails->umUserCreatedBy->last_name }}</td>
                        <td style="padding-left: 30px;">{{ date('Y-m-d', strtotime($payementDetails->payment_date)) }}</td>
                        <td style="text-align: right; padding-right: 30px;">LKR &nbsp;&nbsp; {{ number_format($payementDetails->amount, 2) }}</td>
                    </tr>
                @endforeach
            @else
            <tr>
                <th colspan="2" style="font-size: 12px; text-align: center;">- No payment records found -</th>
            </tr>
            @endif
        </tbody>
    </table>
</div>
<br>
<label class="col-sm-3" style="font-weight: 800; font-family: Verdana, Geneva, sans-serif;">Due Amount</label><label class="col-sm-3" style="color: #ff0000; font-weight: bold;"> LKR &nbsp;&nbsp; {{ number_format($dueAmount2, 2) }}</label>
