<div class="modal-header">
    <h3 class="modal-title" id="exampleModalLabel">Invoice Details</h3>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">

    <div class="form-group row">
        <label for="" class="col-sm-3 col-form-label">Customer Name :-</label>
        <div class="col-sm-5">
            <label for="" class=" col-form-label">{{ $customer->customer_name }}</label>
        </div>
        <label for="" class="col-sm-2 col-form-label">Invoice No :-</label>
        <div class="col-sm-2">
            <label for="" class=" col-form-label">{{ $invoiceData->invoice_number }}</label>
        </div>
    </div>

    <div class="form-group row">
        <label for="" class="col-sm-3 col-form-label">Invoice Date :-</label>
        <div class="col-sm-5">
            <label for="" class=" col-form-label">{{ $invoiceData->created_at }}</label>
        </div>
        <label for="" class="col-sm-2 col-form-label">Invoice Type :-</label>
        <div class="col-sm-2">
            <label for="" class=" col-form-label">Credit</label>
        </div>
    </div>


    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Payment Date</th>
                    <th>Paid Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $id = 0; ?>
                @foreach ($payment as $payments)
                    <?php $id++; ?>
                    <tr>
                        <td>{{ $id }}</td>
                        <td>{{ date('Y-m-d', strtotime($payments->created_at)) }}</td>
                        <td style="text-align: right;">{{ number_format($payments->amount, 2) }}</td>
                        <td>
                            <button type="button" class="btn btn-danger btn-xs" onclick="reversePayment({{ $payments->id }},{{ $invoiceData->id }})">Reverse Payment</button>
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>

<script>
    function reversePayment(paymentId, InvoiceId) {
        var csrf_token = $("#csrf_token").val();
        swal({
                title: "Are you sure?",
                text: "You are about to change Reverse the payment. Please Confirm.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, Reverse",
                cancelButtonText: "Cancel",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function(isConfirm) {
                if (isConfirm) {
                    jQuery.ajax({
                        url: "{{ url('/reversePayment') }}",
                        type: "POST",
                        data: {
                            "paymentId": paymentId,
                            "_token": csrf_token,
                            "InvoiceId": InvoiceId,
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
                            if (data.msg == "success") {
                                swal({
                                        title: "Successful",
                                        text: "Reject Success",
                                        type: "success",
                                        showCancelButton: false,
                                        confirmButtonClass: "btn-primary",
                                        confirmButtonText: "Ok",
                                        closeOnConfirm: true
                                    },
                                    function(isConfirm) {
                                        if (isConfirm) {
                                            swal("Success", "Reverse success!", "success");
                                            window.location = "/adminPaymentReverse";
                                        }
                                    });
                            } else {
                                swal("Error", "Reject Failed!", "warning");
                            }
                        }
                    });
                } else {
                    swal("Cancelled", "Cancelled.", "error");
                }
            });
    }
</script>
