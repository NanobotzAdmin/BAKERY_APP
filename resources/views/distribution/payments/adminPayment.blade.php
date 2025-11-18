@php
    $privilageId = \DB::table('pm_interfaces')
        ->select('pm_interfaces.id AS pageId', 'pm_interface_topic.id AS grupId')
        ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
        ->where('pm_interfaces.path', 'adminPayment')
        ->first();
@endphp

@extends('layout', ['pageId' => $privilageId->pageId, 'grupId' => $privilageId->grupId])

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2><b>Payments</b></h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/admindashboard">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a>Distribution</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Payments</strong>
                </li>
            </ol>
        </div>
    </div>
    <br>

    <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>New Payment</h5>
                </div>
                <div class="ibox-content">

                    <div class="form-group row">
                        <div class="form-group col-md-4">
                            <label for="">Customer</label>
                            <select class="select2_demo_3 form-control" onchange="loadInvoicesToCustomer()" id="customer">
                                <option value="0">-- Select One --</option>
                                @foreach ($customerList as $customers)
                                    <option value="{{ $customers->id }}">{{ $customers->customer_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row" style="display: none;" id="invoiceDiv">

                    </div>

                    <div class="form-group row">
                        <div class="form-group col-md-4">
                            <label for="">Payment Type</label><br>
                            <select id="payment" name="payment" class="select2_demo_3 form-control"
                                onchange="paymentType()">
                                <option value="cash">Cash</option>
                                <option value="cheque">Cheque</option>
                            </select>
                        </div>
                    </div>

                    <div id="cash" class="cash"></div>
                    <div id="cheque" class="cheque"></div>

                    <div class="form-group row">
                        {{-- <div class="form-group col-md-4">
                            <label for="">Amount</label>
                            <input type="text" class="form-control" id="amount" maxlength="15" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*?)\..*/g, '$1');" autocomplete="off">
                        </div> --}}

                        <div class="col-sm-2">
                            <label for="">Amount</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text form-control-sm" id="basic-addon1" style="background-color: #faf8e3">LKR</span>
                                </div>
                                <input type="text" class="form-control form-control-sm" id="amount" maxlength="15" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="form-group col-md-4">
                            <span><br></span>
                            <button type="button" class="btn btn-primary btn-sm pull-right" style="margin-top: 2%" onclick="addInvoicePyament()"><i class="fa fa-credit-card" aria-hidden="true"></i> &nbsp; Make Payment</button>
                        </div>
                    </div>


                    <div id="loadInvoiceDetails"></div>

                </div>
            </div>
        </div>
    </div>
@endsection


@section('footer')
    <script>
        $(document).ready(function() {
            $(".select2_demo_3").select2({
                placeholder: "-- Select One --",
                allowClear: true
            });
        });



        function paymentType() {
            var dropd = document.getElementById("payment");
            var text = document.getElementById("cash");
            var text1 = document.getElementById("cheque");

            if (dropd.value == "cash") {
                text.innerHTML =
                    ' <div class="form-group row"> <div class="form-group col-sm-4"><span><br></span> </div> </div>';
                return false;
            } else if (dropd.value == "cheque") {
                text.innerHTML =
                    ' <div class="form-group row"><div class="form-group col-md-2"> <label for="">Cheque Date</label><br><input type="date" class="form-control form-control-sm" id="paymentDate"> </div> <div class="form-group col-md-2"> <label for="">Cheque No</label>  <input type="text" class="form-control form-control-sm" id="chequeNo" maxlength="10" autocomplete="off"></div> </div>';
                return false;
            }
        }


        function loadInvoicesToCustomer() {
            var csrf_token = $("#csrf_token").val();
            var customer = $('#customer option:selected').val();

            if (customer == 0) {
                swal("", "Please select a Customer.", "warning");
                ("#invoiceCombo").val($("#invoiceCombo option:first").val());
            } else {
                jQuery.ajax({
                    url: "{{ url('/loadInvoicesToCustomer') }}",
                    type: "POST",
                    data: {
                        "_token": csrf_token,
                        "customer": customer,
                        "url": "distribution.payments.ajaxPayments.ajaxloadInvoiceToCustomers",
                    },
                    beforeSend: function() {
                        showLder();
                    },
                    complete: function() {},
                    error: function(data) {},
                    success: function(data) {
                        hideLder();
                        $('#invoiceDiv').html(data);
                        $('#invoiceDiv').show();
                        $(".select2_demo_3").select2();
                    }
                });
            }
        }


        function addInvoicePyament() {
            var csrf_token = $("#csrf_token").val();
            var customer = $('#customer option:selected').val();
            var invoice = $("#invoiceCombo option:selected").val();
            var paymentType = $("#payment option:selected").val();
            var amount = $("#amount").val();
            var paymentDate = $("#paymentDate").val();
            var chequeNo = $("#chequeNo").val();

            if (validatePaymentForm()) {
                jQuery.ajax({
                    url: "{{ url('/saveInvoicePayment') }}",
                    type: "POST",
                    data: {
                        "_token": csrf_token,
                        "customer": customer,
                        "invoice": invoice,
                        "paymentType": paymentType,
                        "amount": amount,
                        "paymentDate": paymentDate,
                        "chequeNo": chequeNo
                    },
                    beforeSend: function() {
                        showLder();
                    },
                    complete: function() {},
                    error: function(data) {},
                    success: function(data) {
                        hideLder();

                        if (data.msg == "success") {
                            swal("Success!", "Save Success!", "success");
                            window.location = "/adminPayment";
                        } else if (data.msg == "already") {
                            swal("Sorry!", "Payment No already exits!", "warning");
                        } else if (data.msg == "totalExceed") {
                            swal("Sorry!", "Payment Price Exceed to Invoice Price!", "warning");

                        } else {
                            swal("Error!", "Save Error!", "warning");
                        }
                    }
                });

            }
        }


        function validatePaymentForm() {
            var customer = $('#customer option:selected').val();
            var invoice = $("#invoiceCombo option:selected").val();
            var paymentType = $("#payment option:selected").val();
            var amount = $("#amount").val();
            var paymentDate = $("#paymentDate").val();
            var chequeNo = $("#chequeNo").val();
            var state = true;

            if (customer == 0) {
                swal("", "Please select a Customer.", "warning");
                state = false;
                //   $('#invoiceCombo').val('');

            } else if (invoice == 0) {
                swal("", "Please select an Invoice.", "warning");
                state = false;

            } else {
                if (paymentType == "cash") {
                    if (amount == "" || isNaN(amount)) {
                        swal("Sorry!", "Enter Amount!", "warning");
                        state = false;

                    } else {

                        state = true;
                    }
                } else {

                    if (amount == "" || isNaN(amount)) {
                        swal("", "Please enter an Amount.", "warning");
                        state = false;

                    } else if (paymentDate == '') {
                        swal("", "Please select a Cheque Date.", "warning");
                        state = false;
                    } else if (chequeNo == "") {
                        swal("", "Please enter a Cheque Number.", "warning");

                        state = false;
                    } else {
                        state = true;
                    }
                }
            }
            return state;
        }


        function loadInvoiceData() {
            var csrf_token = $("#csrf_token").val();
            var invoiceId = $('#invoiceCombo option:selected').val();

            if (invoiceCombo == 0) {
                swal("Sorry!", "Select Invoice!", "warning");
            } else {
                jQuery.ajax({
                    url: "{{ url('/loadInvoiceData') }}",
                    type: "POST",
                    data: {
                        "_token": csrf_token,
                        "invoiceId": invoiceId,
                        "url": "distribution.payments.ajaxPayments.ajaxloadInvoiceData",
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
                        $('#loadInvoiceDetails').html(data);
                    }
                });
            }
        }


        function removeInvoicePayment(PaymentId) {
            var csrf_token = $("#csrf_token").val();
            jQuery.ajax({
                url: "{{ url('/removeInvoicePayment') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "PaymentId": PaymentId,
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
                    if (data.msg == "sucess") {
                        swal("Success!", "Remove Success!", "success");
                        window.location = "/adminPayment";

                    } else {
                        swal("Error!", "Save Error!", "warning");
                    }
                }
            });
        }
    </script>
@endsection
