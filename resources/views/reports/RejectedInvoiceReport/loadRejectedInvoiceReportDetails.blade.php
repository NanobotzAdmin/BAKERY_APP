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
                            {{-- <th style="min-width: 140px;">Sales Rep</th> --}}
                            {{-- <th style="min-width: 140px;">Vehicle</th> --}}
                            <th style="min-width: 160px;">Date & Time</th>
                            <th style="min-width: 100px;">Invoice Type</th>
                            <th style="min-width: 90px;">Status</th>
                            <th style="min-width: 120px; text-align: right; padding-right: 30px;">Invoice Amount</th>
                            <th style="min-width: 120px; text-align: right; padding-right: 30px;">Paid Amount</th>
                            {{-- <th style="min-width: 120px; text-align: right; padding-right: 30px;">Due Amount</th> --}}
                            <th style="min-width: 130px;">Payment History</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $id = 0; @endphp

                        @foreach ($data as $invoice)
                            @php
                                $id++;
                                $invoiceTypeName = '';
                                if ($invoice->invoice_type == 1) {
                                    $invoiceTypeName = 'Credit';
                                } elseif ($invoice->invoice_type == 2) {
                                    $invoiceTypeName = 'Cash';
                                } else {
                                    $invoiceTypeName = 'Cheque';
                                }

                                $netInvoPrice = (float) $invoice->net_price - ( (float) $invoice->discount + (float) $invoice->display_discount + (float) $invoice->special_discount + (float) $invoice->custom_discount );
                                $dueAmount1 = $netInvoPrice - (float) $invoice->total_amout_paid;
                                $dueAmount = ($dueAmount1 < 0) ? 0.0 : $dueAmount1;
                            @endphp

                            <tr>
                                <td style="text-align: center">
                                    <button type="button" class="btn btn-sm" onclick="loadRejectedInvoiceDataModal({{ $invoice->InvoiceId }})" data-toggle="modal" data-target="#invoiceNo">
                                        <i class="fa fa-hashtag" aria-hidden="true"></i> {{ $id }}
                                    </button>
                                </td>
                                <td>{{ $invoice->customer_name }}</td>
                                <td>{{ $invoice->invoice_number }}</td>
                                {{-- <td>{{ $invoice->sales_rep_name ?? '—' }}</td> --}}
                                {{-- <td>{{ $invoice->vehicle_number ?? '—' }}</td> --}}
                                <td>{{ \Carbon\Carbon::parse($invoice->created_at)->format('Y-m-d \a\t h:i A') }}</td>
                                <td>{{ $invoiceTypeName }}</td>
                                <td>Rejected</td> {{-- Since we query for status 3, we can hardcode this --}}
                                <td class="sumPrice" style="text-align: right; padding-right: 30px;">{{ number_format($netInvoPrice, 2) }}</td>
                                <td class="sumPricePaid" style="text-align: right; padding-right: 30px;">{{ number_format($invoice->total_amout_paid, 2) }}</td>
                                {{-- <td class="sumPriceDue" style="text-align: right; padding-right: 30px;">{{ number_format($dueAmount, 2) }}</td> --}}
                                <td>
                                    <button type="button" class="btn btn-dark btn-xs" onclick="loadInvoicePaymentHistory({{ $invoice->InvoiceId }})" data-toggle="modal" data-target="#invoiceHistoryPayment">View Payments</button>
                                    <button type="button" class="btn btn-sm" onclick="loadRejectedInvoiceDataModal({{ $invoice->InvoiceId }})" data-toggle="modal" data-target="#invoiceNo">
                                        View
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="font-weight: bold; color: #000;">
                            <td colspan="6" style="text-align: right;">TOTAL</td>
                            <td style="text-align: right; padding-right: 30px;" id="sumPriceInput">0.0</td>
                            <td style="text-align: right; padding-right: 30px;" id="sumPricePaidInput">0.0</td>
                            {{-- <td style="text-align: right; padding-right: 30px;" id="sumPriceDueInput">0.0</td> --}}
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


    function loadRejectedInvoiceDataModal(invoiceId) {
        var csrf_token = $("#csrf_token").val();
        jQuery.ajax({
            url: "{{ url('/loadRejectedInvoiceDataModal') }}",
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
