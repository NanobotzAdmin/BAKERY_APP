
<div class="ibox-title">
    <h5>Payment Reverse</h5>
</div>
<div class="ibox-content">
    <div class="table-responsive" >
        <table class="table table-bordered table-hover dataTables-example">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Invoice No</th>
                    <th>Invoice Type</th>
                    <th>Total Amount</th>
                    <th>Paid Amount</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php $id = 0; ?>
                @if (empty($data))
                    <tr>
                        <td></td>
                    </tr>
                @else
                    @foreach ($data as $datas)
                        <?php
                        $customer = App\Customer::find($datas->cm_customers_id);
                        $netInvoPrice = (float) $datas->net_price - ((float) $datas->discount + (float) $datas->display_discount);
                        $id++;
                        ?>
                        <tr>
                            <td>{{ $id }}</td>
                            <td>{{ $customer->customer_name }}</td>
                            <td>{{ $datas->invoice_number }}</td>
                            <td>Credit</td>
                            <td style="text-align: right;">{{ number_format($netInvoPrice, 2) }}</td>
                            <td style="text-align: right;">{{ number_format($datas->total_amout_paid, 2) }}</td>
                            <td>
                                <button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#view" onclick="loadInvicesData({{ $datas->id }})"><i class="fa fa-external-link" aria-hidden="true"></i>&nbsp; View</button>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.dataTables-example').DataTable({
            pageLength: 25,
            responsive: true,
            dom: '<"html5buttons"B>lTfgitp',
            buttons: []
        });

        hideLder();
    });

    function loadInvicesData(invoiceId) {
        var csrf_token = $("#csrf_token").val();
        jQuery.ajax({
            url: "{{ url('/loadInvicesDataToModal') }}",
            type: "POST",
            data: {
                "_token": csrf_token,
                "invoiceId": invoiceId,
            },
            beforeSend: function() {
                showLder();
            },
            complete: function() {
            },
            error: function(data) {
            },
            success: function(data) {
                hideLder();
                $("#loadInvoiceModalArea").html(data);
            }
        });
    }
</script>
