<input type="hidden" id="csrf_token" value="{{ csrf_token() }}">

<div class="modal-header">
    <h5 class="modal-title">Available Credit Bills of  &nbsp; 🏪 &nbsp;<span style="color: #000; font-weight: bold;">{{ $customer->customer_name }}</span></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="form-group col-md">
        <label style="">Invoices</label>
        <select class="form-control" onchange="loadInvoiceDetails()" id="invoiceCombo">
            <option value="0" selected disabled>-- Select Invoice --</option>
            @foreach ($invoiceCustomer as $invoices)
                <option value="{{ $invoices->id }}">{{ $invoices->invoice_number }}</option>
            @endforeach

        </select>
    </div>
    <div id="loadPaymentDetails">

    </div>

    {{-- <div class="form-group col-md">
        <label style="">Payment Amount: </label>
        <input type="text" name="" id="paymentCredit" class="form-control allow_decimal" maxlength="20"/>
    </div> --}}



</div>
<div class="modal-footer">
    <button type="button" class="btn btn-primary btn-sm" onclick="addCreditPayment()"><i class="fa fa-money" aria-hidden="true"></i> &nbsp; Add Payment</button>
    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times-circle" aria-hidden="true"></i> &nbsp; Skip Payment</button>
    {{-- <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button> --}}
</div>


<script>
    $(document).ready(function() {

        $(".allow_decimal").on("input", function(evt) {
            var self = $(this);
            self.val(self.val().replace(/[^0-9\.]/g, ''));
            if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which >
                57)) {
                evt.preventDefault();
            }
        });
    });


    function loadInvoiceDetails() {
        var csrf_token = $("#csrf_token").val();
        var invoice = $('#invoiceCombo option:selected').val();
        jQuery.ajax({
            url: "{{ url('/loadInvoicePaymentDetais') }}",
            type: "POST",
            data: {
                "_token": csrf_token,
                "invoice": invoice,
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
                $("#loadPaymentDetails").html(data);
            }
        });
    }



    function addCreditPayment() {
        var csrf_token = $("#csrf_token").val();
        var invoice = $('#invoiceCombo option:selected').val();
        var payment = $("#paymentCredit").val();
        var showindBalance = $("#showindBalance").val();
        if (invoice == 0) {
            swal("", "Please select an Invoice.", "warning");
        } else if (payment == '') {
            swal("", "Please enter the Payment Amount.", "warning");
        } else {
            jQuery.ajax({
                url: "{{ url('/addCreditPayment') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "invoice": invoice,
                    "subTotal": payment,
                    "showingBalance": showindBalance
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
                    if (data.msg == "totalExceed") {
                        swal("Amount Exceeded", "Payment amount exceeded the Balance amount !! \n Please check the payment amount and try again.", "warning");
                    } else if (data.msg == "success") {
                        $('#creditModal').modal('hide');
                        swal("Success", "Payment completed successfully.", "success");
                    } else {
                        swal("Sorry", "Payment failed !!!", "error");
                    }
                }
            });
        }
    }
</script>
