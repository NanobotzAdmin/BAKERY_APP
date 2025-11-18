<a href="{{ url('print/' . (empty($dateFromFormat) ? 'ANY' : $dateFromFormat) . '/' . $customer . '/' . $invoiceType . '/' . $salesRep . '/' . $drivers . '/' . $vehicle . '/' . (empty($dateToFormat) ? 'ANY' : $dateToFormat)) }}"
   target="_blank"
   class="btn btn-dark btn-block"
   style="font-size: 15px; color: #62d146; font-family: 'Roboto', sans-serif; letter-spacing: 1.5px; display: flex; align-items: center; justify-content: center;">
    <i class="fa fa-print" aria-hidden="true"></i>&nbsp; Click Here To Print
</a>

<br>
<table class="table table-bordered table-responsive" style="">
    <thead>
        <tr>
            <th style="min-width: 200px;">Customer</th>
            @foreach ($products as $pro)
                <th style="min-width: 80px;">{{ $pro->sub_category_name }}</th>
            @endforeach
            <th>Cash</th>
            <th>Credit</th>
            <th>Market Return</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $datas)
            <?php
                $invoice = App\customerInvoices::find($datas->InvoiceId);
                $customer = App\Customer::find($invoice->cm_customers_id);
            ?>
            @if ($customer->is_active == 1)
                <tr class="txtMult2">
                    <td>
                        <button type="button" class="btn btn-sm" onclick="loadInvoiceDataModal({{ $invoice->id }})" data-toggle="modal" data-target="#invoiceNo"><i class="fa fa-hashtag" aria-hidden="true"></i></button>
                         {{ $customer->customer_name }}
                    </td>
                    @foreach ($products as $pro)
                        <?php
                        $proQty = App\customerInvoiceHasStock::where([['dm_customer_invoice_id', $invoice->id], ['pm_product_sub_category_id', $pro->id]])->first();

                        if (empty($proQty)) {
                            $proQtyInvoice = 0;
                        } else {
                            $proQtyInvoice = $proQty->quantity;
                        }
                        ?>
                        <td class="{{ $pro->id }}">{{ $proQtyInvoice }} </td>



                        <script>
                            $(document).ready(function() {
                                var mult2 = 0;
                                // for each row:
                                $("tr.txtMult2").each(function() {
                                    // get the values from this row:
                                    var $val2 = parseFloat($('.<?php echo $pro->id; ?>', this).html());
                                    mult2 += $val2;
                                });

                                $("#<?php echo $pro->id; ?>").html(mult2.toFixed(2));
                            });
                        </script>

                    @endforeach

                    <?php
                    $tot = (float) $invoice->net_price + (float) $invoice->return_price;
                    $invoiceNet = (float) $invoice->net_price;

                    ?>
                    @if ($invoice->invoice_type == 1)
                        <td class="val3">0</td>
                        <td class="val4">{{ $invoiceNet }}</td>
                    @else
                        <td class="val3">{{ $invoiceNet }}</td>
                        <td class="val4">0</td>
                    @endif
                    <td class="val5">{{ $invoice->return_price }}</td>
                    <td class="val6">{{ $tot }}</td>
                </tr>
            @endif
        @endforeach

    </tbody>
    <tfoot>
        <tr style="font-weight: bold; background-color: #fcf4ff;">
            <td style="text-align: center">TOTAL</td>
            @foreach ($products as $pro)
                <td id="{{ $pro->id }}"></td>
            @endforeach
            <td id="cashTot"></td>
            <td id="creditTot"></td>
            <td id="returnTot"></td>
            <td id="sumTot"></td>
        </tr>
    </tfoot>
</table>
<br>


<script>
    $(document).ready(function() {
        var mult2 = 0;
        var mult3 = 0;
        var mult4 = 0;
        var mult5 = 0;
        // for each row:
        $("tr.txtMult2").each(function() {
            // get the values from this row:
            var $val2 = parseFloat($('.val3', this).html());
            var $val3 = parseFloat($('.val4', this).html());
            var $val4 = parseFloat($('.val5', this).html());
            var $val5 = parseFloat($('.val6', this).html());
            mult2 += $val2;
            mult3 += $val3;
            mult4 += $val4;
            mult5 += $val5;
        });

        $("#cashTot").html(mult2.toFixed(2));
        $("#creditTot").html(mult3.toFixed(2));
        $("#returnTot").html(mult4.toFixed(2));
        $("#sumTot").html(mult5.toFixed(2));

    });


    function loadInvoiceDataModal(invoiceId) {
        var csrf_token = $("#csrf_token").val();
        jQuery.ajax({
            url: "{{ url('/adminInvoicePrint') }}",
            type: "POST",
            data: {
                "_token": csrf_token,
                "invoiceId": invoiceId,
                "url": "reports.dailyReport.ajaxSalesReport.ajaxLoadInvouceDataToModal",
            },
            beforeSend: function() {
                showLder();
            },
            complete: function() {
            },
            error: function(data) {
            },
            success: function(data) {
                $('#InvoiceDataModal').html(data);
                hideLder();
            }
        });
    }
</script>
