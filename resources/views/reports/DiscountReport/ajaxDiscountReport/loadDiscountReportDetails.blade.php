<div class="col-sm-12">
    <div class="ibox">
        <div class="ibox-content">

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover dataTables-example" style="font-family: Verdana, Geneva, sans-serif;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Invoice No</th>
                            <th>Invoice Type</th>
                            <th>Date</th>
                            <th>Vehicle No</th>
                            <th>Shop Name</th>
                            <th style="text-align: right; padding-right: 30px;">Loyalty Discount</th>
                            <th style="text-align: right; padding-right: 30px;">Display Discount</th>
                            <th style="text-align: right; padding-right: 30px;">Special Discount</th>
                            <th style="text-align: right; padding-right: 30px;">Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoiceCollection as $invoiceData)
                            @php
                                // Calculate Total Price
                                $TotalPrice = (float) $invoiceData->net_price - ( (float) $invoiceData->discount + (float) $invoiceData->display_discount + (float) $invoiceData->special_discount );
                            @endphp

                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $invoiceData->invoice_number }}</td>
                                @if ($invoiceData->invoice_type == 1)
                                    <td>Credit</td>
                                @elseif($invoiceData->invoice_type == 2)
                                    <td>Cash</td>
                                @else
                                    <td>Cheque</td>
                                @endif
                                <td>{{ date('Y-m-d', strtotime($invoiceData->created_at)) }}</td>
                                <td>{{ $invoiceData->vehicleRegNumber }}</td>
                                <td>{{ $invoiceData->customerName }}</td>
                                <td style="text-align: right; padding-right: 30px;">{{ number_format($invoiceData->discount, 2) }}</td>
                                <td style="text-align: right; padding-right: 30px;">{{ number_format($invoiceData->display_discount, 2) }}</td>
                                <td style="text-align: right; padding-right: 30px;">{{ number_format($invoiceData->special_discount, 2) }}</td>
                                <td style="text-align: right; padding-right: 30px;">{{ number_format($TotalPrice, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
            $('.dataTables-example').DataTable({
                pageLength: 10,
                responsive: true,
                dom: '<"html5buttons"B>lTfgitp',
                buttons: [
                    {
                        extend: 'pdf',
                        title: 'ExampleFile'
                    },
                    {
                        extend: 'copy'
                    },
                    {
                        extend: 'csv'
                    },
                    {
                        extend: 'excel',
                        title: 'ExampleFile'
                    },
                    {
                        extend: 'print',
                        customize: function(win) {
                            $(win.document.body).addClass('white-bg');
                            $(win.document.body).css('font-size', '10px');
                            $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                        }
                    }
                ]
            });
        });
</script>
