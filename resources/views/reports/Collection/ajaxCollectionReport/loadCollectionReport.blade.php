<style>
    .lbl-style {
        margin-left: 20px;
        font-size: 14px;
        font-weight: bold;
        color: black;
        font-family: Roboto, sans-serif;
        width: 280px;
    }
    .lbl-style1 {
        font-size: 14px;
        color: black;
        font-family: Roboto, sans-serif;
        width: 250px;
        cursor: auto;
        letter-spacing: 1.5px;
        text-align: right;
    }

    /* --- Table CSS begins --- */
    /* .styled-table th:first-child {
        border-radius: 5px 0 0 0;
    }
    .styled-table th:last-child {
        border-radius: 0 5px 0 0;
    }
    .styled-table {
        border-collapse: collapse;
        margin: 25px 0;
        font-size: 15px;
        font-family: Roboto, sans-serif;
        min-width: 400px;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        border-radius: 5px;
    }
    .styled-table thead tr {
        background-color: #5c3d23;
        color: #ffffff;
        text-align: left;
        font-size: 14px;
        font-weight: bold;
        font-family: Roboto, sans-serif;
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
        border-bottom: 2px solid #5c3d23;
    }
    .styled-table tbody tr:hover td {
        background-color: #faf6ec;
    } */
</style>

<div class="col-md-12">
    <div class="ibox">
        <div class="ibox-title">
            <h2>Summery</h2>
        </div>
        <div class="ibox-content">
            <div class="form-group row">
                <label class="lbl-style">Cash Sale</label>
                <label class="lbl-style1"><span style="color: #c81bd8; font-size: 12px;">({{ number_format($amountGainByAdding_customDiscounts_CASH, 2) }})</span> &nbsp; {{ number_format($cash_Sale, 2) }}</label>
            </div>
            <div class="form-group row">
                <label class="lbl-style">Credit Sale</label>
                <label class="lbl-style1" ><span style="color: #c81bd8; font-size: 12px;">({{ number_format($amountGainByAdding_customDiscounts_CREDIT, 2) }})</span> &nbsp; {{ number_format($credit_Sale, 2) }}</label>
            </div>
            <div class="form-group row">
                <label class="lbl-style">Cheque Sale</label>
                <label class="lbl-style1"><span style="color: #c81bd8; font-size: 12px;">({{ number_format($amountGainByAdding_customDiscounts_CHEQUE, 2) }})</span> &nbsp; {{ number_format($cheque_Sale, 2) }}</label>
            </div>
            <div class="form-group row">
                <label class="lbl-style">Market Return</label>
                <label class="lbl-style1">{{ number_format($total_Return, 2) }}</label>
            </div>
            <div class="form-group row">
                <label class="lbl-style">NET TOTAL</label>
                <label class="lbl-style1" style="border-top: 2px solid black; border-bottom: 4px double black;">
                    {{ number_format($netTotal, 2) }}
                </label>
            </div>
        </div>
    </div>
</div>


<div class="col-md-12">
    <div class="ibox">
        <div class="ibox-title">
            <h2>Collection Report <small>(credit collection)</small></h2>
        </div>
        <div class="ibox-content">
            <div class="row">
                <div class="col-sm-10">
                    <div class="table-responsive" style="width: 100%">
                        <table class="table table-hover material-table">
                            <thead>
                                <tr>
                                    <th style="color: #fff; background-color: #5c3d23; font-size: 16px;">#</th>
                                    <th style="color: #fff; background-color: #5c3d23; font-size: 16px; min-width: 120px;">Invoice Date</th>
                                    <th style="color: #fff; background-color: #5c3d23; font-size: 16px; min-width: 120px;">Invoice No</th>
                                    <th style="color: #fff; background-color: #5c3d23; font-size: 16px; min-width: 120px;">Vehicle No</th>
                                    <th style="color: #fff; background-color: #5c3d23; font-size: 16px; min-width: 220px;">Customer Name</th>
                                    <th style="color: #fff; background-color: #5c3d23; font-size: 16px; width: 140px;">Payment Date</th>
                                    <th style="color: #fff; background-color: #5c3d23; font-size: 16px; width: 170px; text-align: right; padding-right: 30px;">Amount (Rs)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $sn = 1;
                                    $tableTotal = 0;
                                @endphp
                                @foreach ($InvoiceData_creditPayments as $invoice)
                                    @if ($invoice->Invoice_Type == App\STATIC_DATA_MODEL::$credit)
                                        <tr>
                                            <td>{{ $sn++ }}</td>
                                            <td>{{ date('Y-m-d', strtotime($invoice->Invoice_Date)) }}</td>
                                            <td>{{ $invoice->Invoice_No }}</td>
                                            <td>{{ $invoice->Vehicle_No }}</td>
                                            <td>{{ $invoice->Customer_Name }}</td>
                                            <td>{{ date('Y-m-d', strtotime($invoice->Payment_Date)) }}</td>
                                            <td style="text-align: right; padding-right: 30px;">
                                                {{ number_format($invoice->Payment_Amount, 2) }}
                                            </td>
                                        </tr>
                                        @php
                                            $tableTotal += $invoice->Payment_Amount;
                                        @endphp
                                    @endif
                                @endforeach
                            </tbody>
                            <tfoot style="border-top: 2px solid #5c3d23;">
                                <tr>
                                    <td colspan="5"></td>
                                    <td colspan="" style="text-align: left; border-left: 10; font-family: Roboto, sans-serif; color: #000;"><b>TOTAL</b></td>
                                    <td style="text-align: right; padding-right: 30px; font-weight: bold; color: #000;" id="tot">Rs {{ number_format($tableTotal, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="form-group row">
                        <label class="lbl-style" style="color: #000ba5; font-size: 17px; font-weight: bold; font-family: Roboto, sans-serif;">
                            TOTAL COLLECTION <small>(Cash + Credit)</small>
                        </label>
                        <label class="lbl-style1" style="color: #000ba5; font-size: 17px; font-weight: bold; font-family: Roboto, sans-serif;">
                            <b>Rs</b> <span id="totCollection">{{ number_format($cash_Sale + $tableTotal, 2) }}</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

