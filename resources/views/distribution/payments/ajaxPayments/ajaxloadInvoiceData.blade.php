<div class="form-group row">
    <div class="form-group col-md-2">
        <?php
            $invoicePrice = floatval($invoiceData->net_price) - (floatval($invoiceData->discount) + floatval($invoiceData->display_discount));
        ?>
        <label for="">Invoice Price :</label>
        <label style="font-weight: bold;margin-left: 1%">{{ $invoicePrice }}</label>
    </div>
    <div class="form-group col-md-2">
        <label for="">Paid Amount :</label>
        <label style="font-weight: bold;margin-left: 1%">{{ $invoiceData->total_amout_paid }}</label>
    </div>
</div>

<table class="table table-striped table-bordered table-hover dataTables-example">
    <thead>
        <tr>
            <th>#</th>
            <th>Paid Amount</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php $id = 0; ?>
        @foreach ($invoicePayments as $payments)
            <?php $id++; ?>

            <tr>
                <td>{{ $id }}</td>
                <td>{{ $payments->amount }}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-xs" onclick="removeInvoicePayment({{ $payments->id }})">Remove</button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
