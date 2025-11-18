<!DOCTYPE html>
<html lang="en">

@php
    use App\STATIC_DATA_MODEL;
    $company_name = STATIC_DATA_MODEL::$company_name;
    $company_logo = STATIC_DATA_MODEL::$company_logo;
    $company_address = STATIC_DATA_MODEL::$company_address;
    $company_contacts= STATIC_DATA_MODEL::$company_contacts;
@endphp

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $company_name }}</title>
    <style>
        @media print {
            page {
                width: 5.8cm;
                height: 100%;
                padding-top: 0%;
                margin-top: 0%;
                padding-bot: 0%;
                margin-bot: 0%;
            }
            .noPrint {
                display: none;
            }
        }
    </style>
</head>

{{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ INVOICE PRINT ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}

<body style="width:5.8cm; font-size: small;">
    <input type="button" onclick="window.print();" class="btn-block btn btn-success noPrint" value="Click Here To Print This Page" style='height: 80px;width:100%;'>
    <center>
        <label style="font-size: 28px; font-weight: bold;">
            {{ $company_name }}
        </label>
        <br>
        <label style="font-size: 18px; font-weight: bold;">(Private) Limited</label>
        <hr style="border-top: 2px dashed black;">
        <div>
            <h5>{{ $company_address }}</h5>
            <h5>Contact NO - {{ $company_contacts}}</h5>
            <h5>{{ now()->format('Y-m-d') }} - {{ now()->format('h:i A') }}</h5>
        </div>

        @php
            $invoiceType = $invoiceCustomer->invoice_type;
            $customer = App\Customer::find($invoiceCustomer->cm_customers_id);
            $invoiceTypeName = '';

            if ($invoiceType == 1) {
                $invoiceTypeName = 'Credit';
            } elseif ($invoiceType == 2) {
                $invoiceTypeName = 'Cash';
            } else {
                $invoiceTypeName = 'Cheque';
            }
        @endphp

        <h3>Customer : {{ $customer->customer_name }}</h3><br>
        <label>Customer Address : {{ $customer->address }}</label><br>
        <label>Invoice Date : {{ $invoiceCustomer->created_at }}</label><br>
        <label>Invoice No : {{ $invoiceCustomer->invoice_number }} ({{ $invoiceTypeName }})</label><br>
        <label>Payment Type : {{ $invoiceTypeName }}</label>
        <hr style="border-top: 2px dashed black">


        {{-- ------------------------------------- P R O D U C T  T A B L E ---------------------------------------}}
        <h2>Product Table</h2>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Product</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Unit Price</th>
                    <th scope="col">Amount</th>
                    <th style="display: none">ActualTotal</th>
                    <th style="display: none">ReturnTotal</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $sn = 1;
                @endphp
                @foreach ($invoiceData as $invoice)
                    @php
                        $product = App\SubCategory::find($invoice->pm_product_sub_category_id);
                        $invoiceTotal = $invoice->total_price;
                        $totalActualQtyPrice = ((float) $invoice->quantity) * ((float) $invoice->unit_price);
                        $totalReturnQtyPrice = ((float) $invoice->return_qty) * ((float) $invoice->unit_price);
                        $invoiceId = $invoice->dm_customer_invoice_id;
                    @endphp

                    @if ($invoice->quantity != 0 || ($invoice->quantity != 0 && $invoice->return_qty != 0))
                        <tr class="txtMult">
                            <td>{{ $sn++ }}</td>
                            <td>{{ $product->sub_category_name }}</td>
                            <td class="val1" style="text-align: center;">{{ $invoice->quantity }}</td>
                            <td class="val2" style="text-align: center;">{{ number_format($invoice->unit_price, 2, '.', ',') }}</td>
                            {{-- @if ((float) $invoice->quantity >= (float) $product->discountable_qty)
                                <td class="val2">{{ $product->discounted_price }}</td>
                            @else
                                <td class="val2">{{ $product->selling_price }}</td>
                            @endif  --}}
                            <td class="multTotal" style="text-align: right;"></td>
                        </tr>
                    @endif
                @endforeach
            </tbody>

            <tfoot>
                @php
                    $invoiceSum = App\customerInvoices::find($invoiceId);
                @endphp
                <tr>
                    <td></td>
                    <td></td>
                    <td colspan="2" style="font-size: 10px;">Product Total :</td>
                    <td id="grandTotal" style="text-align: right;"></td>
                </tr>

                {{-- Display Discount --}}
                @if ($invoiceSum->display_discount > 0)
                    <tr>
                        <td></td>
                        <td></td>
                        <td colspan="2" style="font-size: 10px;">Display Discount :</td>
                        <td style="text-align: right;"><label id="display">{{ number_format($invoiceSum->display_discount, 2, '.', ',') }}</label></td>
                    </tr>
                @else
                    <div style="display: none;">
                        <label id="display">0.00</label>
                    </div>
                @endif

                {{-- Loyalty Discount --}}
                @if ($invoiceSum->discount > 0)
                    <tr>
                        <td></td>
                        <td></td>
                        <td colspan="2" style="font-size: 10px;">Loyalty Discount :</td>
                        <td style="text-align: right;"><label id="loyalty">{{ number_format($invoiceSum->discount, 2, '.', ',') }}</label></td>
                    </tr>
                @else
                    <div style="display: none;">
                        <label id="loyalty">0.00</label>
                    </div>
                @endif

                {{-- Special Discount --}}
                @if ($invoiceSum->special_discount > 0)
                    <tr>
                        <td></td>
                        <td></td>
                        <td colspan="2" style="font-size: 10px;">Special Discount :</td>
                        <td style="text-align: right;"><label id="special">{{ number_format($invoiceSum->special_discount, 2, '.', ',') }}</label></td>
                    </tr>
                @else
                    <div style="display: none;">
                        <label id="special">0.00</label>
                    </div>
                @endif

                {{-- Custom Discount --}}
                @if ($invoiceSum->custom_discount != null && $invoiceSum->custom_discount > 0)
                    <tr>
                        <td></td>
                        <td></td>
                        <td colspan="2" style="font-size: 10px;">Discount :</td>
                        <td style="text-align: right;"><label id="custom_discount">{{ number_format($invoiceSum->custom_discount, 2, '.', ',') }}</label></td>
                    </tr>
                @else
                    <div style="display: none;">
                        <label id="custom_discount">0.00</label>
                    </div>
                @endif
            </tfoot>
        </table>


        {{-- ------------------------------------- R E T U T N  T A B L E ---------------------------------------}}
        <h2>Return Table</h2>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Product</th>
                    <th scope="col">Rtn</th>
                    <th scope="col">Unit Price</th>
                    <th scope="col">Amount</th>
                    <th style="display: none">ActualTotal</th>
                    <th style="display: none">ReturnTotal</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $sn = 1;
                @endphp
                @foreach ($invoiceData as $invoice)
                    @php
                        $product = App\SubCategory::find($invoice->pm_product_sub_category_id);
                        $invoiceTotal = $invoice->total_price;
                        $totalActualQtyPrice = ((float) $invoice->quantity) * ((float) $invoice->unit_price);
                        $totalReturnQtyPrice = ((float) $invoice->return_qty) * ((float) $invoice->return_price);
                        $invoiceId = $invoice->dm_customer_invoice_id;
                    @endphp

                    @if ($invoice->return_qty != 0 || ($invoice->quantity != 0 && $invoice->return_qty != 0))
                        <tr class="txtMult2">
                            <td>{{ $sn++ }}</td>
                            <td>{{ $product->sub_category_name }}</td>
                            <td class="val3" style="text-align: center;">{{ $invoice->return_qty }}</td>
                            <td class="val4" style="text-align: center;">{{ number_format($invoice->return_price, 2, '.', ',') }}</td>
                            {{-- @if ((float) $invoice->quantity >= (float) $product->discountable_qty)
                                <td class="val4">{{ $product->discounted_price }}</td>
                            @else
                                <td class="val4">{{ $product->selling_price }}</td>
                            @endif  --}}
                            <td class="multTotal2" style="text-align: right;"></td>
                        </tr>
                    @endif
                @endforeach
            </tbody>

            <tfoot>
                @php
                    $invoiceSum = App\customerInvoices::find($invoiceId);
                @endphp
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="font-size: 10px;">Return Total :</td>
                    <td id="grandTotal2" style="text-align: right;"></td>
                </tr>
            </tfoot>
        </table>
    </center>
    </div>
    </div>

    <br><br>
    {{-- <div>

<label>Product Total</label>: <label id="proTot"></label>
<label>Return Total</label>: <label id="returnTot"></label>
<label> Total</label>: <label id="TotSum"></label>


</div> --}}

    <table class="table table-borderless text-center" style="margin-left: 45%">
        <tr>
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            <td colspan="4" style="font-size: 10px;">Product Total :</td>
            <td style="text-align: right;">
                <label id="proTot"></label>
            </td>
        </tr>
        {{-- @if ($invoiceSum->discount > 0)
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td>Loyalty Total :</td>
                <td><label id="loyalty">{{ $invoiceSum->discount }}</label></td>
            </tr>
        @else
            <label id="loyalty">0</label>
        @endif --}}

        {{-- @if ($invoiceSum->display_discount > 0)
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td>Display discount Total :</td>
                <td><label id="display">{{ $invoiceSum->display_discount }}</label></td>
            </tr>
        @else
            <label id="display">0</label>
        @endif --}}
        <tr>
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            <td colspan="4" style="font-size: 10px;">Return Total :</td>
            <td style="text-align: right;">
                <label id="returnTot"></label>
            </td>
        </tr>
        <tr>
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            <td colspan="4">
                <h4  style="font-size: 10px;">TOTAL</h4>
            </td>
            <td style="text-align: right;">
                <h4 id="TotSum" style="letter-spacing: 0.5px;"></h4>
            </td>
        </tr>
    </table>
    {{-- @if ($invoiceSum->given_rack_count == null || $invoiceSum->given_rack_count == 'NULL')
        <p>ලබාගත් Rack ගණන : 0</p>
    @else
        <p>ලබාගත් Rack ගණන : {{ $invoiceSum->given_rack_count }}</p>
    @endif

    @if ($invoiceSum->taken_rack_count == null || $invoiceSum->taken_rack_count == 'NULL')
        <p>ආපසු ලබාදුන් Rack ගණන : 0</p>
    @else
        <p>ආපසු ලබාදුන් Rack ගණන : {{ $invoiceSum->taken_rack_count }} </p>
    @endif --}}

    <hr style="border-top: 2px dashed black">
    <h5>භාණ්ඩ පරික්ෂා කර බලා ලබාගන්න.</h5>
    <h5>Recieved by . . . . . . . . . . . . . . . . . . . . . </h5>
    <center>
    <h3>~~ Thank You ~~</h3>
    <hr style="border-top: 2px dashed black">
    <h6>Powerd by Nanobotz.lk</h6>
    </center>
</body>

<script>
    $(document).ready(function() {

        function formatNumber(num) {
            return new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(num);
        }


        var mult = 0;
        // for each row:
        $("tr.txtMult").each(function() {
            // get the values from this row:
            var $val1 = $('.val1', this).html().replace(/,/g, '');
            var $val2 = $('.val2', this).html().replace(/,/g, '');
            var $total = parseFloat($val1) * parseFloat($val2); // Product Amount (Quantity x Unit price)
            $('.multTotal', this).html(formatNumber($total)); // set Product Amount
            mult += $total;
        });

        $("#grandTotal").html(formatNumber(mult)); // Product Total in table
        $("#proTot").text(formatNumber(mult)); // Product Total in Summary



        var mult2 = 0;
        // for each row:
        $("tr.txtMult2").each(function() {
            // get the values from this row:
            var $val2 = $('.val3', this).html().replace(/,/g, '');
            var $val3 = $('.val4', this).html().replace(/,/g, '');
            var $total2 = parseFloat($val2) * parseFloat($val3); // Return Amount (Quantity x Unit price)
            $('.multTotal2', this).html(formatNumber($total2)); // set Return Amount
            mult2 += $total2;
        });

        $("#grandTotal2").html(formatNumber(mult2)); // Return Total in table
        $("#returnTot").text(formatNumber(mult2)); // Return Total in Summary



        var sumPro = Number($("#proTot").text().replace(/,/g, ''));
        var sumReturn = Number($("#returnTot").text().replace(/,/g, ''));
        // var totSum = parseFloat(sumPro) - parseFloat(sumReturn);
        var discountTot = Number($("#loyalty").text().replace(/,/g, ''));
        var displayDiscount = Number($("#display").text().replace(/,/g, ''));
        var specialDiscount = Number($("#special").text().replace(/,/g, ''));
        var customDiscount = Number($("#custom_discount").text().replace(/,/g, ''));
        var totSum = sumPro - (sumReturn + discountTot + displayDiscount + specialDiscount + customDiscount);

        $("#TotSum").text(formatNumber(totSum)); // TOTAL in summary



        var sumQty = 0;
        $('.sumQty').each(function() {
            var value = $(this).text();
            // add only if the value is number
            if (!isNaN(value) && value.length != 0) {
                sumQty += parseFloat(value);
            }
        });
        $("#sumQtyInput").html(sumQty.toFixed(2));


        var sumReturnQty = 0;
        $('.sumReturnQty').each(function() {
            var value = $(this).text();
            // add only if the value is number
            if (!isNaN(value) && value.length != 0) {
                sumReturnQty += parseFloat(value);
            }
        });
        $("#sumReturnQtyInput").html(sumReturnQty.toFixed(2));


        // var sumTotal = 0;
        //     $('.sumTotal').each(function() {

        //         var value = $(this).text();
        //     // add only if the value is number
        //     if(!isNaN(value) && value.length != 0) {
        //         sumTotal += parseFloat(value);
        //     }
        //     });
        // $("#sumTotalInput").html(sumTotal.toFixed(2));
    });
</script>

</html>
