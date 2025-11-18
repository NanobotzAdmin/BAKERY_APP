
@php
    $customerInvoice = App\customerInvoices::find($invoiceId);
@endphp

{{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ INVOICE DETAILS (MODAL) ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}

<div class="row" style="font-family: Verdana, Geneva, sans-serif;">
    <div class="form-group col-sm-6">
        <h4><label class="col-sm-5" style="color: #858585;">Invoice No</label><label class="col-sm-7" style="color: #000000;">{{ $customerInvoice->invoice_number }}</label></h4>
    </div>
    <div class="form-group col-sm-6">
        <h4><label class="col-sm-5" style="color: #858585;">Invoice Type</label><label class="col-sm-7" style="color: #000000;">
            @if ($customerInvoice->invoice_type == 1)
            Credit <span style="font-size: 22px;"> 💳</span>
            @elseif ($customerInvoice->invoice_type == 2)
            Cash <span style="font-size: 20px;"> 💵</span>
            @elseif ($customerInvoice->invoice_type == 3)
            Cheque <span style="font-size: 20px;"> 📒</span>
            @endif
            </label>
        </h4>
    </div>

    {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button> --}}
</div>

<div class="row" style="font-family: Verdana, Geneva, sans-serif;">
    <div class="form-group col-sm-6">
        <h4><label class="col-sm-5" style=" width: 148px; color: #858585;">Customer</label><label class="col-sm-7" style="color: #000000;">{{ $customer->customer_name }}</label></h4>
    </div>
    <div class="form-group col-sm-6">
        <h4>
            <label class="col-sm-5" style="color: #858585;">Invoice Status</label><label class="col-sm-7" style="color: #000000;">
                @if ($customerInvoice->invoice_status == App\STATIC_DATA_MODEL::$invoicePending)
                    Pending
                @elseif ($customerInvoice->invoice_status == App\STATIC_DATA_MODEL::$invoiceCompleted)
                    Completed
                @elseif ($customerInvoice->invoice_status == App\STATIC_DATA_MODEL::$invoiceDeleted)
                    Deleted
                @endif
            </label>
        </h4>
    </div>
</div>

<div class="modal-body" style="font-family: Verdana, Geneva, sans-serif; padding-top: 0;">
    <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">
    <section class="sheet">
        <div class="row">
            <div class="col-12">
                {{-- <hr class="dots2"> --}}
                <center>
                    {{-- -------------------------------------------------------------- PRODUCT TABLE ------------------------------------------------------------- --}}
                    <h2 style="color: #000;">PRODUCT TABLE</h2>
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="bg-info">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Product</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col" style=" width: 130px;">Unit Price</th>
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
                                        <td style="font-weight: bold; color: #000;">Product Total</td>
                                        <td id="grandTotal" style="font-weight: bold; text-align: right; color: #000;"></td>
                                    </tr>

                                @if ($invoiceSum->discount > 0)
                                    <tr>
                                @else
                                    <tr style="display: none">
                                @endif
                                        <td style="border-top: none;"></td>
                                        <td style="border-top: none;"></td>
                                        <td style="border-top: none;"></td>
                                        <td style="border-top: none; color: #00c763;">Loyalty Discount</td>
                                        <td id="loyalty" style="border-top: none; text-align: right; color: #00c763;">{{ number_format($invoiceSum->discount, 2, '.', ',') }}</td>
                                    </tr>

                                @if ($invoiceSum->display_discount > 0)
                                    <tr>
                                @else
                                    <tr style="display: none">
                                @endif
                                        <td style="border-top: none;"></td>
                                        <td style="border-top: none;"></td>
                                        <td style="border-top: none;"></td>
                                        <td style="border-top: none; color: #00c763;">Display Discount</td>
                                        <td id="display" style="border-top: none; text-align: right; color: #00c763;">{{ number_format($invoiceSum->display_discount, 2, '.', ',') }}</td>
                                    </tr>

                                @if ($invoiceSum->special_discount > 0)
                                    <tr>
                                @else
                                    <tr style="display: none">
                                @endif
                                        <td style="border-top: none;"></td>
                                        <td style="border-top: none;"></td>
                                        <td style="border-top: none;"></td>
                                        <td style="border-top: none; color: #00c763;">Special Discount</td>
                                        <td id="special" style="border-top: none; text-align: right; color: #00c763;">{{ number_format($invoiceSum->special_discount, 2, '.', ',') }}</td>
                                    </tr>

                                @if ($invoiceSum->custom_discount > 0)
                                    <tr>
                                @else
                                    <tr style="display: none">
                                @endif
                                        <td style="border-top: none;"></td>
                                        <td style="border-top: none;"></td>
                                        <td style="border-top: none;"></td>
                                        <td style="border-top: none; color: #00c763;">Discount</td>
                                        <td id="custom_discount" style="border-top: none; text-align: right; color: #00c763;">{{ number_format($invoiceSum->custom_discount, 2, '.', ',') }}</td>
                                    </tr>
                            </tfoot>
                        </table>
                    </div>
                    {{-- ---------------------------------------------------------------------------------------------------------------------------------------- --}}


                    {{-- -------------------------------------------------------------- RETURN TABLE ------------------------------------------------------------- --}}
                    <h2 style="color: #000;">RETURN TABLE</h2>
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="bg-danger">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Product</th>
                                    <th scope="col" style=" width: 100px;">Return</th>
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
                                    <td style="font-weight: bold; color: #000;">Return Total</td>
                                    <td id="grandTotal2" style="font-weight: bold; text-align: right; color: #000;"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    {{-- ---------------------------------------------------------------------------------------------------------------------------------------- --}}
                </center>
            </div>
        </div>


        <div class="table-responsive">
            <table class="table table-borderless text-center" style="width: 40%; margin-left: 60%">
                <tr>
                    {{-- <td></td> --}}
                    {{-- <td></td> --}}
                    {{-- <td></td> --}}
                    <td colspan="4" style="border-top: none; text-align: left; color: #22C6C9; font-size: 15px;">Product Total</td>
                    <td style="border-top: none; text-align: right; color: #22C6C9; font-size: 15px;"><label id="proTot"></label></td>
                </tr>
                {{-- @if ($invoiceSum->discount > 0)
                <tr>
                @else
                <tr style="display: none">
            @endif
            <td></td>
            <td></td>
            <td></td>
            <td>Loyalty Total :</td>
            <td><label id="loyalty">{{ $invoiceSum->discount }}</label></td>
            </tr>

            @if ($invoiceSum->display_discount > 0)
                <tr>
                @else
                <tr style="display: none">
            @endif
            <td></td>
            <td></td>
            <td></td>
            <td>Display Total :</td>
            <td><label id="display">{{ $invoiceSum->display_discount }}</label></td>
            </tr> --}}

                <tr>
                    {{-- <td></td> --}}
                    {{-- <td></td> --}}
                    {{-- <td></td> --}}
                    <td colspan="4" style="border-top: none; text-align: left; color: #ff4747; font-size: 15px;">Return Total</td>
                    <td style="border-top: none; text-align: right; color: #ff4747; font-size: 15px;"><label id="returnTot"></label></td>
                </tr>
                <tr>
                    {{-- <td></td> --}}
                    {{-- <td></td> --}}
                    {{-- <td></td> --}}
                    <td colspan="4" style="border-top: none; font-weight: bold; font-size: 16px; text-align: left; color: #000;">TOTAL</td>
                    <td id="TotSum" style="border-top: none; font-weight: bold; font-size: 16px; text-align: right; color: #000;"></td>
                </tr>
            </table>
        </div>
    </section>
    </body>
</div>
{{-- <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div> --}}


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
        var discountTot = Number($("#loyalty").text().replace(/,/g, ''));
        var displayDiscountTot = Number($("#display").text().replace(/,/g, ''));
        var specialDiscountTot = Number($("#special").text().replace(/,/g, ''));
        var customDiscountTot = Number($("#custom_discount").text().replace(/,/g, ''));
        var totSum = sumPro - (sumReturn + discountTot + displayDiscountTot + specialDiscountTot + customDiscountTot);

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
