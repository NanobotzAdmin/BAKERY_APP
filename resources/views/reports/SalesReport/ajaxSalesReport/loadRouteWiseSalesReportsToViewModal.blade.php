@php
   $customer = App\Customer::find($customerID);
   $totalInvoiceAmount = 0.0;
@endphp

<div class="modal-content" id="">
    <div class="modal-header">
        <h4 class="modal-title">Invoices - {{ $customer->customer_name }}</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>
    <!-- Modal body -->
    <div class="modal-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover dataTables-example" id="datatableView">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Invoice Number</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th style="text-align: right; padding-right: 30px;">Invoice Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ViewData as $views)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $views->invoice_number }}</td>
                            <td>{{ date('Y-m-d', strtotime($views->created_at)) }}</td>
                            <td>
                                @if ($views->invoice_status == 0)
                                <p style="color:#ff0000">Pending</p>
                                @elseif($views->invoice_status == 1)
                                <p style="color:#009e47">Completed</p>
                                @endif
                            </td>
                            <td style="text-align: right; padding-right: 30px;">
                                {{ number_format($views->invoice_price - $views->discount - $views->display_discount - $views->special_discount, 2) }}
                            </td>
                        </tr>
                        @php
                            $totalInvoiceAmount += $views->invoice_price - $views->discount - $views->display_discount - $views->special_discount;
                        @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="font-weight: bold; text-align: right">TOTAL</td>
                        <td style="font-weight: bold; text-align: right; padding-right: 30px;">{{ number_format($totalInvoiceAmount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#datatableView').DataTable({
            pageLength: 10,
            responsive: true,
            dom: '<"html5buttons"B>lTfgitp',
            buttons: []
        });
    });
</script>
