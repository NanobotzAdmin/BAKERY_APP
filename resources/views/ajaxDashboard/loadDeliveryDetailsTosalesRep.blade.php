<style>
    .table-bordered-bottom tbody tr:last-child td {
        border-bottom: 2px solid #343a40; /* Set your desired border color and thickness */
    }
</style>

<div class="modal-content">
    <div class="modal-header">
        <h3 class="modal-title" id="exampleModalLabel">Activity</h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @php
        $Customer = App\Customer::find($customerID);
    @endphp
    <div class="modal-body">
        <h4  style="font-family: 'Lato', sans-serif;">
            <label class="col-md-2">Shop Name</label>
            <label class="col-md-6" style="color: #000;">{{ $Customer->customer_name }}</label>
        </h4>
        <h4  style="font-family: 'Lato', sans-serif;">
            <label class="col-md-2">Address</label>
            <label class="col-md-6" style="color: #000;">{{ $Customer->address }}</label>
        </h4>
        <h4 style="font-family: 'Lato', sans-serif;">
            <label class="col-md-2">Location</label>
            <label class="col-md-6">
                @if($Customer->location_link != null)
                    <a href="{{ $Customer->location_link }}" class="btn btn-dark btn-xs" style="font-size: 10px; color: #08f100;" target="_blank">View On Map &nbsp; <i class="fa fa-external-link-square"></i></a>
                @else
                    <span style="font-size: 12px; color: #df0000;">N/A</span>
                @endif
            </label>
        </h4>

        <hr>

        <h2 class="font-bold">Invoices</h2>
        <div class="table-responsive">
            <table class="table table-bordered-bottom" style="font-family: 'Lato', sans-serif;">
                <thead style="font-size: 14px; color: #000;">
                    <tr class="bg-info">
                        <th style="min-width: 30px; max-width: 80px;">#</th>
                        <th style="min-width: 140px; max-width: 180px;">Invoice Number</th>
                        <th style="min-width: 110px;">Invoice Type</th>
                        <th style="min-width: 200px;">Date & Time</th>
                        <th style="min-width: 140px;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $id = 0; ?>
                    @if (count($invoiceObj) != 0)
                        @foreach ($invoiceObj as $invo)
                            <tr>
                                <?php
                                    $invoice = App\customerInvoices::find($invo->invoId);
                                    $netInvoice = (float) $invoice->net_price - ( (float) $invoice->discount + (float) $invoice->display_discount  + (float) $invoice->special_discount + (float) $invoice->custom_discount );
                                    $id++;
                                ?>
                                <td>{{ $id }}</td>
                                <td>{{ $invoice->invoice_number }}</td>
                                @if ($invoice->invoice_type == '1')
                                    <td>Credit</td>
                                @elseif($invoice->invoice_type == '2')
                                    <td>Cash</td>
                                @elseif($invoice->invoice_type == '3')
                                    <td>Cheque</td>
                                @endif
                                <td>{{ $invoice->created_at->format('Y-m-d \a\t h:i A') }}</td>
                                <td>Rs. {{ number_format($netInvoice, 2, '.', ',') }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <th colspan="5" style="font-size: 12px; text-align: center;">- No records found -</th>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <br>

        <h2 class="font-bold">Payments <span style="font: 3px;">(credit)</span></h2>
        <div class="table-responsive">
            <table class="table table-bordered-bottom" style="font-family: 'Lato', sans-serif;">
                <thead style="font-size: 14px; color: #000;">
                    <tr class="bg-warning">
                        <th style="min-width: 30px; max-width: 80px;">#</th>
                        <th style="min-width: 140px; max-width: 180px;">Invoice Number</th>
                        <th style="min-width: 110px; max-width: 180px;">Invoice Type</th>
                        <th style="min-width: 120px;">Invoice Date</th>
                        <th style="min-width: 140px;">Paid Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $id = 0; ?>
                    @if (count($customerInvoicePayment) != 0)
                        @foreach ($customerInvoicePayment as $invo2)
                            <tr>
                                <?php $id++; ?>
                                <td>{{ $id }}</td>
                                <td>{{ $invo2->invoNum }}</td>
                                @if ($invo2->invoType == '1')
                                    <td>Credit</td>
                                @elseif($invo2->invoType == '2')
                                    <td>Cash</td>
                                @elseif($invo2->invoType == '3')
                                    <td>Cheque</td>
                                @endif
                                <td>{{ date('Y-m-d', strtotime($invo2->invoDate)) }}</td>
                                <td>Rs. {{ number_format($invo2->sumPaymentAmount, 2, '.', ',') }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <th colspan="5" style="font-size: 12px; text-align: center;">- No records found -</th>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    </div>
</div>
