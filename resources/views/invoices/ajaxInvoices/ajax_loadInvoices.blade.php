<div class="col-md-12">
    <div class="ibox">
        <div class="ibox-content">
            <div class="table-responsive">
                <table class="table table-bordered table-hover dataTables-example">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Invoice No</th>
                            <th>Vehicle</th>
                            <th>Invoice Type</th>
                            <th>Total Price <small>(LKR)</small></th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $datas)
                            @php
                                $invoice = App\customerInvoices::find($datas->InvoiceId);
                                $customerInvoice = App\customerInvoiceHasStock::where('dm_customer_invoice_id', $invoice->id)
                                    ->distinct()
                                    ->first();
                                $deliveryVehicle = App\DeliveryVehicle::find($customerInvoice->dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id);
                                $vehicle = App\Vehicles::find($deliveryVehicle->vm_vehicles_id);
                                $netPrice = (float) $invoice->net_price - ( (float) $invoice->discount + (float) $invoice->display_discount + (float) $invoice->special_discount  + (float) $invoice->custom_discount );
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $invoice->invoice_number }}</td>
                                <td>{{ $vehicle->reg_number }}</td>
                                @if ($invoice->invoice_type == 1)
                                    <td>Credit</td>
                                @elseif($invoice->invoice_type == 2)
                                    <td>Cash</td>
                                @else
                                    <td>Cheque</td>
                                @endif
                                <td style="text-align: right;">{{ number_format($netPrice, 2) }}</td>
                                <td>
                                    <button type="button" class="btn btn-dark btn-xs" onclick="returnToInvoicePage({{ $datas->InvoiceId }})"><i class="fa fa-print" aria-hidden="true"></i> Print</button>
                                    <button type="button" class="btn btn-info btn-xs" onclick="loadInvoiceDataModal({{ $datas->InvoiceId }})" data-toggle="modal" data-target="#invoiceNo"><i class="fa fa-external-link-square" aria-hidden="true"></i> View</button>
                                    @if ($deliveryVehicle->status != 2)
                                        @if (session('user_type') == 1 || session('user_type') == 2 || session('user_type') == 5)
                                            <button type="button" class="btn btn-danger btn-xs" onclick="removeInvoice({{ $datas->InvoiceId }})"><i class="fa fa-trash" aria-hidden="true"></i> Remove</button>
                                        @endif
                                    @endif
                                </td>
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
            pageLength: 25,
            responsive: true,
            dom: '<"html5buttons"B>lTfgitp',
            buttons: [{
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
                    extend: 'pdf',
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


    function removeInvoice(invoiceId) {
        swal({
                title: "Are you sure?",
                text: "This will not able to reverse . Please Confirm.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, Remove",
                cancelButtonText: "Cancel",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function(isConfirm) {
                if (isConfirm) {
                    var csrf_token = $("#csrf_token").val();
                    jQuery.ajax({
                        url: "{{ url('/removeInvoice') }}",
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
                            hideLder();
                            if (data.msg == "success") {
                                swal({
                                    title: "Success",
                                    text: "Invoice Deleted.",
                                    type: "success",
                                    timer: 1300,
                                    showConfirmButton: false
                                });
                                getInvoices();
                                // window.location = '/adminviewInvoices';
                            } else {
                                swal("Error", data.msgDB, "error");
                            }
                        }
                    });
                } else {
                    // swal("Cancelled", "Cancelled.", "error");
                    swal.close();
                }
            })
    }


    function returnToInvoicePage(invoiceId) {
        var csrf_token = $("#csrf_token").val();
        jQuery.ajax({
            url: "{{ url('/adminInvoicePrint') }}",
            type: "POST",
            data: {
                "_token": csrf_token,
                "invoiceId": invoiceId,
                "url": "distribution.payments.invoice.invoicePrint2",
            },
            beforeSend: function() {
                showLder();
            },
            complete: function() {},
            error: function(data) {},
            success: function(data) {
                hideLder();
                $("#InvoiceContent").html(data);
            }
        });
    }
</script>
