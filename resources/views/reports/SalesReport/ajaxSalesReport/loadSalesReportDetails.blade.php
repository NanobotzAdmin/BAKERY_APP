<style>
    .highlight-border td {
        border-top: 2px solid #c81bd8 !important;
        border-bottom: 2px solid #c81bd8 !important;
        /* background-color: #e0b0ff !important; */
        /* color: #000 !important; */
    }
    /* Add border to first and last cell for a complete outline */
    .highlight-border td:first-child {
        border-left: 2px solid #c81bd8 !important;
        /* background-color: #e0b0ff !important; */
        /* color: #000 !important; */
    }
    .highlight-border td:last-child {
        border-right: 2px solid #c81bd8 !important;
        /* background-color: #e0b0ff !important; */
        /* color: #000 !important; */
    }


    .btn-purple {
        background-color: #6f42c1; /* A nice shade of purple */
        border-color: #6f42c1;
        color: #fff !important; /* Ensures the text is white */
    }
    .btn-purple:hover {
        background-color: #5a349b; /* A darker purple for hover */
        border-color: #5a349b;
        color: #fff !important;
    }
</style>


<div class="col-sm-12">
    <div class="ibox">
        <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">
        <div class="ibox-content">

            <div class="table-responsive">
                <table class="table table-bordered table-hover dataTables-example" id="salesReportDetailsTable" style="font-family: Verdana, Geneva, sans-serif;">
                    <thead>
                        <tr>
                            <th style="text-align: center"><i class="fa fa-hashtag" aria-hidden="true"></i></th>
                            <th style="min-width: 150px;">Customer Name</th>
                            <th style="min-width: 110px;">Invoice No</th>
                            <th style="min-width: 160px;">Date & Time</th>
                            <th style="min-width: 100px;">Invoice Type</th>
                            <th style="min-width: 90px;">Status</th>
                            <th style="min-width: 120px; text-align: right; padding-right: 30px;">Invoice Amount</th>
                            <th style="min-width: 120px; text-align: right; padding-right: 30px;">Paid Amount</th>
                            <th style="min-width: 120px; text-align: right; padding-right: 30px;">Due Amount</th>
                            <th style="min-width: 130px;">Payment History</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $id = 0; ?>
                        @foreach ($data as $datas)
                            @php
                                $invoice = App\customerInvoices::find($datas->InvoiceId);
                                $customer = App\Customer::find($invoice->cm_customers_id);
                                $invoiceType = $invoice->invoice_type;
                                $invoiceTypeName = '';
                                $dueAmount = 0;

                                if ($invoiceType == 1) {
                                    $invoiceTypeName = 'Credit';
                                } elseif ($invoiceType == 2) {
                                    $invoiceTypeName = 'Cash';
                                } else {
                                    $invoiceTypeName = 'Cheque';
                                }

                                $netInvoPrice = (float) $invoice->net_price - ( (float) $invoice->discount + (float) $invoice->display_discount + (float) $invoice->special_discount + (float) $invoice->custom_discount );
                                $dueAmount1 = (float) $netInvoPrice - (float) $invoice->total_amout_paid;

                                if ($dueAmount1 < 0) {
                                    $dueAmount = 0.0;
                                } else {
                                    $dueAmount = $dueAmount1;
                                }

                                // ✅ Check for custom discount and set the CSS class variable
                                $borderClass = ($invoice->custom_discount !== null && $invoice->custom_discount > 0) ? 'highlight-border' : '';
                                // $borderClass = '';

                                // ✅ Check for custom discount and set the BUTTON class variable
                                $buttonClass = ($invoice->custom_discount !== null && $invoice->custom_discount > 0) ? 'btn-purple' : 'btn-light';
                                // $buttonClass = 'btn-light';
                            @endphp

                            @if ($customer->is_active == 1)
                                @if ($invoice->invoice_status == 0 && $invoiceType == 1)
                                    @php
                                        $invoiceDateFormat = date('Y-m-d', strtotime($invoice->created_at));
                                        $dt = Carbon\Carbon::now();
                                        $date = $dt->format('Y-m-d');
                                        $to = Carbon\Carbon::createFromFormat('Y-m-d', $date);
                                        $from = Carbon\Carbon::createFromFormat('Y-m-d', $invoiceDateFormat);
                                        $diff_in_days = $to->diffInDays($from);
                                    @endphp

                                    @if ((int) $diff_in_days > (int) $customer->max_credit_bill_availability)
                                        <tr class="{{ $borderClass }}" style="background-color: #ffcdd2; color: #424242;">
                                            <?php $id++; ?>
                                            <td style="text-align: center"><button type="button" class="btn {{ $buttonClass }} btn-sm" onclick="loadInvoiceDataModal({{ $datas->InvoiceId }})" data-toggle="modal" data-target="#invoiceNo"><i class="fa fa-hashtag" aria-hidden="true"></i> {{ $id }}</button></td>
                                            <td>{{ $customer->customer_name }}</td>
                                            <td>{{ $invoice->invoice_number }}</td>
                                            <td>{{ $invoice->created_at->format('Y-m-d \a\t h:i A') }}</td>
                                            <td>{{ $invoiceTypeName }}</td>
                                            <td>Pending</td>
                                            <td class="sumPrice" style="text-align: right; padding-right: 30px;">{{ number_format($netInvoPrice, 2) }}</td>
                                            <td class="sumPricePaid" style="text-align: right; padding-right: 30px;">{{ number_format($invoice->total_amout_paid, 2) }}</td>
                                            <td class="sumPriceDue" style="text-align: right; padding-right: 30px;">{{ number_format($dueAmount, 2) }}</td>
                                            <td><button type="button" class="btn btn-dark btn-xs" onclick="loadInvoicePaymentHistory({{ $invoice->id }})" data-toggle="modal" data-target="#invoiceHistoryPayment">View Payments</button></td>
                                        </tr>
                                    @else
                                        <tr class="{{ $borderClass }}" style="background-color: #ffecb3; color: #424242;">
                                            <?php $id++; ?>
                                            <td style="text-align: center"><button type="button" class="btn {{ $buttonClass }} btn-sm" onclick="loadInvoiceDataModal({{ $datas->InvoiceId }})" data-toggle="modal" data-target="#invoiceNo"><i class="fa fa-hashtag" aria-hidden="true"></i> {{ $id }}</button></td>
                                            <td>{{ $customer->customer_name }}</td>
                                            <td>{{ $invoice->invoice_number }}</td>
                                            <td>{{ $invoice->created_at->format('Y-m-d \a\t h:i A') }}</td>
                                            <td>{{ $invoiceTypeName }}</td>
                                            <td>Pending</td>
                                            <td class="sumPrice" style="text-align: right; padding-right: 30px;">{{ number_format($netInvoPrice, 2) }}</td>
                                            <td class="sumPricePaid" style="text-align: right; padding-right: 30px;">{{ number_format($invoice->total_amout_paid, 2) }}</td>
                                            <td class="sumPriceDue" style="text-align: right; padding-right: 30px;">{{ number_format($dueAmount, 2) }}</td>
                                            <td><button type="button" class="btn btn-dark btn-xs" onclick="loadInvoicePaymentHistory({{ $invoice->id }})" data-toggle="modal" data-target="#invoiceHistoryPayment">View Payments</button></td>
                                        </tr>
                                    @endif
                                @else
                                    @if ($invoiceTypeName == 'Cheque')
                                        <tr class="{{ $borderClass }}" style="background-color: #c8e6c9; color: #424242;">
                                            <?php $id++; ?>
                                            <td style="text-align: center"><button type="button" class="btn {{ $buttonClass }} btn-sm" onclick="loadInvoiceDataModal({{ $datas->InvoiceId }})" data-toggle="modal" data-target="#invoiceNo"><i class="fa fa-hashtag" aria-hidden="true"></i> {{ $id }}</button></td>
                                            <td>{{ $customer->customer_name }}</td>
                                            <td>{{ $invoice->invoice_number }}</td>
                                            <td>{{ $invoice->created_at->format('Y-m-d \a\t h:i A') }}</td>
                                            <td>{{ $invoiceTypeName }}</td>
                                            <td>@if ($invoice->invoice_status == 1) Completed @else Pending @endif</td>
                                            <td class="sumPrice" style="text-align: right; padding-right: 30px;">{{ number_format($netInvoPrice, 2) }}</td>
                                            <td class="sumPricePaid" style="text-align: right; padding-right: 30px;">{{ number_format($invoice->total_amout_paid, 2) }}</td>
                                            <td class="sumPriceDue" style="text-align: right; padding-right: 30px;">{{ number_format(0, 2) }}</td>
                                            <td><button type="button" class="btn btn-dark btn-xs" onclick="loadInvoicePaymentHistory({{ $invoice->id }})" data-toggle="modal" data-target="#invoiceHistoryPayment">View Payments</button></td>
                                        </tr>
                                    @else
                                        <tr class="{{ $borderClass }}">
                                            <?php $id++; ?>
                                            <td style="text-align: center"><button type="button" class="btn {{ $buttonClass }} btn-sm" onclick="loadInvoiceDataModal({{ $datas->InvoiceId }})" data-toggle="modal" data-target="#invoiceNo"><i class="fa fa-hashtag" aria-hidden="true"></i> {{ $id }}</button></td>
                                            <td>{{ $customer->customer_name }}</td>
                                            <td>{{ $invoice->invoice_number }}</td>
                                            <td>{{ $invoice->created_at->format('Y-m-d \a\t h:i A') }}</td>
                                            <td>{{ $invoiceTypeName }}</td>
                                            <td>@if ($invoice->invoice_status == 1) Completed @else Pending @endif</td>
                                            <td class="sumPrice" style="text-align: right; padding-right: 30px;">{{ number_format($netInvoPrice, 2) }}</td>
                                            <td class="sumPricePaid" style="text-align: right; padding-right: 30px;">{{ number_format($invoice->total_amout_paid, 2) }}</td>
                                            <td class="sumPriceDue" style="text-align: right; padding-right: 30px;">{{ number_format($dueAmount, 2) }}</td>
                                            <td><button type="button" class="btn btn-dark btn-xs" onclick="loadInvoicePaymentHistory({{ $invoice->id }})" data-toggle="modal" data-target="#invoiceHistoryPayment">View Payments</button></td>
                                        </tr>
                                    @endif
                                @endif
                            @endif
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="font-weight: bold; color: #000;">
                            <td colspan="6" style="text-align: right;">TOTAL</td>
                            <td style="text-align: right; padding-right: 30px;" id="sumPriceInput">0.0</td>
                            <td style="text-align: right; padding-right: 30px;" id="sumPricePaidInput">0.0</td>
                            <td style="text-align: right; padding-right: 30px;" id="sumPriceDueInput">0.0</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Data Table function
        $(document).ready(function() {
            $('.dataTables-example').DataTable({
                pageLength: 25,
                responsive: true,
                dom: '<"html5buttons"B>lTfgitp',
                buttons: [
                    {extend: 'pdf', title: 'ExampleFile', footer: true},
                    {extend: 'copy', footer: true},
                    {extend: 'csv', footer: true},
                    {extend: 'excel', title: 'ExampleFile', footer: true},
                    {extend: 'print',
                        customize: function(win) {
                            $(win.document.body).addClass('white-bg');
                            $(win.document.body).css('font-size', '10px');
                            $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                        }, footer: true
                    }
                ]
            });
        });


        // calculate Total of Invoice Amount
        var sumPrice = 0;
        $('.sumPrice').each(function() {
            var value = $(this).text().replace(/,(?=\d{3})/g, '');
            // add only if the value is number
            if (!isNaN(value) && value.length != 0) {
                sumPrice += parseFloat(value);
            }
        });
        $("#sumPriceInput").html(sumPrice.toLocaleString('en', {minimumFractionDigits: 2}));


        // calculate Total of Paid Amount
        var sumPricePaid = 0;
        $('.sumPricePaid').each(function() {
            var value = $(this).text().replace(/,(?=\d{3})/g, '');
            // add only if the value is number
            if (!isNaN(value) && value.length != 0) {
                sumPricePaid += parseFloat(value);
            }
        });
        $("#sumPricePaidInput").html(sumPricePaid.toLocaleString('en', {minimumFractionDigits: 2}));


        // calculate Total of Due Amount
        var sumPriceDue = 0;
        $('.sumPriceDue').each(function() {
            var value = $(this).text().replace(/,(?=\d{3})/g, '');
            // add only if the value is number
            if (!isNaN(value) && value.length != 0) {
                sumPriceDue += parseFloat(value);
            }
        });
        $("#sumPriceDueInput").html(sumPriceDue.toLocaleString('en', {minimumFractionDigits: 2}));
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
            complete: function() {},
            error: function(data) {},
            success: function(data) {
                $('#InvoiceDataModal').html(data);
                hideLder();
            }
        });
    }


    function loadInvoicePaymentHistory(invoiceId) {
        var csrf_token = $("#csrf_token").val();
        jQuery.ajax({
            url: "{{ url('/loadInvoicePaymentHistory') }}",
            type: "POST",
            data: {
                "_token": csrf_token,
                "invoiceId": invoiceId,
            },
            beforeSend: function() {
                showLder();
            },
            complete: function() {},
            error: function(data) {},
            success: function(data) {
                $('#InvoicePaymentHistory').html(data);
                hideLder();
            }
        });
    }
</script>
