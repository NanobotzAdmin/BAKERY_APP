<style>
    @page {
        /* size: 14.8cm 21cm; */
        /* margin: 5mm 5mm 5mm 5mm; */
        /* change the margins as you want them to be. */
    }

    .smallText {
        font-size: small;
    }

    body {
        background-color: #CCC;
        font-size: small;
    }

    /* .table-condensed>thead>tr>th,
    .table-condensed>tbody>tr>th,
    .table-condensed>tfoot>tr>th,
    .table-condensed>thead>tr>td,
    .table-condensed>tbody>tr>td,
    .table-condensed>tfoot>tr>td {
        padding: 3px;
    } */

    .sheet {
        padding: 5mm;
    }

    /* .A5 {
        width: 14.8cm;
        height: 21cm;
    } */

    @media print {
        .avoid {
            page-break-inside: avoid;
        }

        .sheet {
            width: 5.5cm;
            height: auto;
        }

        .img-fluid {
            width: 100%;
        }

        /* .A5{
            width:80%;
            height: auto;
        } */

        /* page[size="A5"][layout="portrait"] {
            width: 14.8cm;
            height: 21cm;
        } */
        .coloured {
            background-color: sienna !important;
            box-shadow: inset 0 0 0 1000px #a0522d !important;
            /* workaround for IE 11*/
        }

        .noPrint {
            display: none;
        }
    }

    /* .table {
            border: 2px solid sienna;
        }

        table.table-bordered {
            border: 1px solid sienna;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid sienna;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid sienna;
        } */
</style>

@php
    use App\STATIC_DATA_MODEL;
    $company_name = STATIC_DATA_MODEL::$company_name;
    $company_logo = STATIC_DATA_MODEL::$company_logo;
    $company_address = STATIC_DATA_MODEL::$company_address;
    $company_contacts= STATIC_DATA_MODEL::$company_contacts;
@endphp

{{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ INVOICE VIEW ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}

<body>
    <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">
    <section class="sheet">
        <div class="row noPrint">
            <div class="col-12">
                <a href="/loadInvoicePrintPage/{{ $invoiceId }}" class="btn-block btn btn-dark" style="font-size: 15px; color: #62d146; font-family: 'Roboto', sans-serif; letter-spacing: 1.5px;">
                    <i class="fa fa-print" aria-hidden="true"></i> &nbsp; Click Here To Print This Page
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <hr class="dots2">
                <center>
                    <div>
                        <h2><b>{{ $company_name }} (Private) Limited</b></h2>
                        <h5>{{ $company_address }}</h5>
                        <h5>Contact No - {{ $company_contacts}}</h5>
                        <h5>{{ now()->format('Y-m-d') }} - {{ now()->format('h:i A') }}</h5>
                        @php
                            $invoice_OBJ = App\customerInvoices::find($invoiceId);
                        @endphp
                        <h5>Invoice No : {{ $invoice_OBJ->invoice_number}}</h5>
                    </div>
                    <br>


                    {{-- ------------------------------------- P R O D U C T  T A B L E ---------------------------------------}}
                    <h2>Product Table</h2>
                    <table class="table" style="width: 60%">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col" style="min-width: 125px;">Product</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Unit Price</th>
                                <th scope="col" style="text-align: right;">Amount</th>
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
                                        <td class="val1">{{ $invoice->quantity }} </td>
                                        {{--  @if ((float) $invoice->quantity >= (float) $product->discountable_qty)
                                    <td class="val2">{{ $product->discounted_price }}</td>
                                    @else
                                    <td class="val2">{{ $product->selling_price }}</td>
                                    @endif  --}}
                                        <td class="val2">{{ number_format($invoice->unit_price, 2, '.', ',') }}</td>
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
                                <td></td>
                                <td>Product Total</td>
                                <td style="text-align: right;" id="grandTotal"></td>
                            </tr>

                            {{-- Display Discount --}}
                            @if ($invoiceSum->display_discount > 0)
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>Display Discount</td>
                                    <td style="text-align: right;">
                                        <label id="display">{{ number_format($invoiceSum->display_discount, 2, '.', ',') }}</label>
                                    </td>
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
                                    <td></td>
                                    <td>Loyalty Discount</td>
                                    <td style="text-align: right;">
                                        <label id="loyalty">{{ number_format($invoiceSum->discount, 2, '.', ',') }}</label>
                                    </td>
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
                                    <td></td>
                                    <td>Special Discount</td>
                                    <td style="text-align: right;">
                                        <label id="special">{{ number_format($invoiceSum->special_discount, 2, '.', ',') }}</label>
                                    </td>
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
                                    <td></td>
                                    <td>Discount</td>
                                    <td style="text-align: right;">
                                        <label id="custom_discount">{{ number_format($invoiceSum->custom_discount, 2, '.', ',') }}</label>
                                    </td>
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
                    <table class="table" style="width: 60%">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col" style="min-width: 130px;">Product</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Unit Price</th>
                                <th scope="col" style="text-align: right;">Amount</th>
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
                                        <td class="val3">{{ $invoice->return_qty }}</td>
                                        {{--  @if ((float) $invoice->quantity >= (float) $product->discountable_qty)
            <td class="val4">{{ $product->discounted_price }}</td>
            @else
            <td class="val4">{{ $product->selling_price }}</td>
            @endif  --}}
                                        <td class="val4">{{ number_format($invoice->return_price, 2, '.', ',') }}</td>
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
                                <td>Return Total</td>
                                <td id="grandTotal2" style="text-align: right;"></td>
                            </tr>
                        </tfoot>
                    </table>
                </center>
            </div>
        </div>


        <table class="table table-borderless text-center" style="width: 25%; margin-left: 55%;">
            <tr>
                {{-- <td></td> --}}
                {{-- <td></td> --}}
                {{-- <td></td> --}}
                <td colspan="4" style="text-align: left;">Product Total</td>
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
                      <td><label id="display">{{$invoiceSum->display_discount}}</label></td>
                   </tr>
                   @else
                   <label id="display">0</label>
                 @endif --}}
            <tr>
                {{-- <td></td> --}}
                {{-- <td></td> --}}
                {{-- <td></td> --}}
                <td colspan="4" style="text-align: left;">Return Total</td>
                <td style="text-align: right;">
                    <label id="returnTot"></label>
                </td>
            </tr>
            <tr>
                {{-- <td></td> --}}
                {{-- <td></td> --}}
                {{-- <td></td> --}}
                <td colspan="4" style="text-align: left;">
                    <h4 style="color: #000000;">TOTAL</h4>
                </td>
                <td style="text-align: right;">
                    <h4 id="TotSum" style="letter-spacing: 0.5px; color: #000000"></h4>
                </td>
            </tr>
        </table>
    </section>

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

</body>
