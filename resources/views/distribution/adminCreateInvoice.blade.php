@php
    $privilageId = \DB::table('pm_interfaces')
        ->select('pm_interfaces.id AS pageId', 'pm_interface_topic.id AS grupId')
        ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
        ->where('pm_interfaces.path', 'adminCreateInvoice')
        ->first();
@endphp

@extends('layout', ['pageId' => $privilageId->pageId, 'grupId' => $privilageId->grupId])

@section('content')

{{-- google fonts --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto&family=Roboto+Slab&display=swap" rel="stylesheet">

{{-- Sawarabi Gothic font --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sawarabi+Gothic&display=swap" rel="stylesheet">

<style>
    /* --- Table CSS begins --- */
    .styled-table th:first-child {
        border-radius: 5px 0 0 0;
    }
    .styled-table th:last-child {
        border-radius: 0 5px 0 0;
    }
    .styled-table {
        border-collapse: collapse;
        margin: 25px 0;
        font-size: 14px;
        /* font-family: Verdana, Geneva, sans-serif; */
        font-family:'Roboto Slab', serif;
        /* font-family: 'Sawarabi Gothic', sans-serif; */
        min-width: 400px;
    }
    .styled-table thead tr {
        background-color: #846f5d;
        color: #ffffff;
        text-align: left;
        font-size: 13px;
        font-weight: bold;
        /* font-family: Verdana, Geneva, sans-serif; */
        /* font-family: 'Roboto Slab', serif; */
        font-family: 'Sawarabi Gothic', sans-serif;
        letter-spacing: 1px;
    }
    .styled-table th,
    .styled-table td {
        padding: 12px 15px;
    }
    .styled-table tbody tr {
        border-bottom: 1px solid #dddddd;
    }
    .styled-table tbody tr:nth-of-type(even) {
        background-color: #ffffff;
    }
    .styled-table tbody tr:last-of-type {
        border-bottom: 2px solid #846f5d;
    }
    .styled-table tbody tr:hover td {
        background-color: #fffcf1;
        color: #e47a00;
    }

    .list-tbl {
        font-family: 'Roboto Slab', serif;
    }
    .list-tbl tbody tr:hover td {
        background-color: #faf6ec;
    }

    .table .sticky {
        border-collapse: collapse;
    }
    .table .sticky th {
        position: sticky;
        top: 0; /* Don't forget this, required for the stickiness */
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    }
</style>

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2><b>Create Invoice</b></h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/admindashboard">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a>Distribution</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Create Invoice</strong>
                </li>
            </ol>
        </div>
    </div>
    <br>

    <div id="InvoiceContent">
        <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>New Invoice</h5>
                    </div>
                    <div class="ibox-content">
                        <input type="hidden" value="{{ $deliveryUnloadingRack }}" id="hiddenDeliveryId21" />
                        <div class="form-group row">
                            <div class="form-group col-sm-4">
                                <label for="">Customer</label>
                                <select class="form-control select2" id="customer" onchange="checkCreditBilAvailability()">
                                    <option value="0" id='customer'>-- Select One --</option>
                                    @foreach ($customerList as $customers)
                                        <option value="{{ $customers->id }}">🏪 {{ $customers->customer_name }} &nbsp;➨&nbsp; {{ $customers->address }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" value="" id="customerHideenID" readonly />
                                <input type="hidden" value="" id="customerMaxCreditAmount" readonly />
                            </div>
                            <?php
                            $logUser = session('logged_user_id');
                            $userdetail = App\User::find($logUser);
                            ?>
                            <div class="form-group col-sm-4">
                                <label for="">Invoice Type</label>
                                <select class="form-control select2" id='invoiceType' onchange="validateInvoiceType()">
                                    <option value="0">-- Select One --</option>
                                    @if ($userdetail->credit_allowed == 1)
                                        <option value="1">💳 Credit</option>
                                    @endif

                                    @if ($userdetail->cash_allowed == 1)
                                        <option value="2">💵 Cash</option>
                                    @endif

                                    @if ($userdetail->cheque_allowed == 1)
                                        <option value="3">📒 Cheque</option>
                                    @endif
                                </select>
                            </div>

                            <div class="form-group col-sm-4">
                                <div class="col-sm-12">&nbsp;</div>
                                <div class="btn-group mt-1">
                                    <button type="button" class="btn btn-info btn-sm" onclick="loadDataToTBL()"><i class="fa fa-download" aria-hidden="true"></i> &nbsp; Load Products</button>
                                </div>
                                <div class="btn-group mt-1" id="creditBilButtonDiv">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="ibox" id="loadStockDeliveryDataTbl">
                    {{-- content in ajax --}}
                </div>


                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Invoice History</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-hover styled-table" style="width: 60%;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th style="min-width: 160px;">Invoice Number</th>
                                        <th style="min-width: 130px;">Invoice Type</th>
                                        <th style="min-width: 250px;">Customer Name</th>
                                        <th style="min-width: 180px;">Amount</th>
                                        <th style="min-width: 90px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $id = 0;
                                    @endphp
                                    @foreach ($InvoiceData as $invoiceItems)
                                        @php
                                            $id++;
                                            $customer = App\Customer::find($invoiceItems->cm_customers_id);
                                            $netInvoice = (float) $invoiceItems->net_price - ( (float) $invoiceItems->discount + (float) $invoiceItems->display_discount + (float) $invoiceItems->special_discount + (float) $invoiceItems->custom_discount );
                                            $invoiceType = '--';

                                            if ($invoiceItems->invoice_type == 1) {
                                                $invoiceType = '💳 Credit';
                                            } elseif ($invoiceItems->invoice_type == 2) {
                                                $invoiceType = '💵 Cash';
                                            } elseif ($invoiceItems->invoice_type == 3) {
                                                $invoiceType = '📒 Cheque';
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $id }}</td>
                                            <td>{{ $invoiceItems->invoice_number }}</td>
                                            <td>{{ $invoiceType }}</td>
                                            <td>{{ $customer->customer_name }}</td>
                                            <td>Rs. &nbsp;{{ number_format($netInvoice, 2) }}</td>
                                            <td><button type="button" class="btn btn-dark btn-xs" onclick="returnToInvoicePage({{ $invoiceItems->id }})"><i class="fa fa-print" aria-hidden="true"></i>&nbsp; Print &nbsp;</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Generate Invoice Modal -->
    <div class="modal fade" id="generateInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="exampleModalLabel">Generate Invoice</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="generateInvoiceModal_ContentDIV">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-warning btn-sm" onclick="saveInvoiceData()"><i class="fa fa-print" aria-hidden="true"></i> Generate Invoice</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Return Modal -->
    <div class="modal fade" id="returnModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Return</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="returnModalContent">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="addReturns()">Add</button>
                </div>
            </div>
        </div>
    </div>


    <!----Customer Credit Modal- --->
    <div class="modal fade" id="creditModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content" id="CustomercreditModalContent">

            </div>
        </div>
    </div>


    <!----Customer LOAD Credit BillS aLL Modal- --->
    <div class="modal fade" id="creditBillsLoadModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content" id="CustomercreditBillModalContent">

            </div>
        </div>
    </div>

@endsection


@section('footer')
    <script>
        $(document).ready(function() {
            $('.dataTables-example').DataTable({
                pageLength: 10,
                responsive: true,
                dom: '<"html5buttons"B>lTfgitp',
                buttons: []
            });

            $(".select2").select2();

            $(".allow_decimal").on("input", function(evt) {

                var self = $(this);
                self.val(self.val().replace(/[^0-9\.]/g, ''));
                if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which >
                        57)) {
                    evt.preventDefault();
                }
            });
        });


        function loadDataToTBL() {
            var csrf_token = $("#csrf_token").val();
            var customer = $('#customer option:selected').val();
            var invoiceType = $('#invoiceType option:selected').val();
            if (customer == 0) {
                swal("", "Please select a Cutomer.", "warning");
            } else if (invoiceType == 0) {
                swal("", "Please select an Invoice Type.", "warning");
            } else {
                jQuery.ajax({
                    url: "{{ url('/loadInvoiceDeliveryDataToTBL') }}",
                    type: "POST",
                    data: {
                        "_token": csrf_token,
                        "page": 1,
                        "customer": customer,
                        "invoiceType": invoiceType,
                        "url": "distribution.ajaxInvoice.ajaxInvoiceLoadTable",
                    },
                    beforeSend: function() {
                        showLder();
                    },
                    complete: function() {},
                    error: function(data) {},
                    success: function(data) {
                        hideLder();
                        $('#loadStockDeliveryDataTbl').html(data);
                    }
                });
            }
        }


        //     function changeSubTotal(quantityInput){
        //   var productRow = $(quantityInput).parent().parent();
        //   var price = parseFloat(productRow.children('.product_price').text());
        //   var retunQty =  parseFloat(productRow.children('.product_return_quantity').text());
        // alert(retunQty);
        //   var quantity = $(quantityInput).val() - retunQty;
        //   var linePrice = price * quantity;


        //   productRow.children('.product_line_price').each(function () {

        //       $(this).text(linePrice.toFixed(2));
        //       recalculateCart();

        //   });
        //             }


        //   /* Recalculate cart */
        // function recalculateCart()
        // {
        //   var subtotal = 0;

        //   /* Sum up row totals */
        //   $('.product').each(function () {

        //     subtotal += parseFloat($(this).children('.product_line_price').text());

        //   }) ;
        // alert(subtotal);

        //   $('#cart_total').text(subtotal.toFixed(2));

        // }


        function validateReturn(id) {
            $('#returnPriceDiv' + id).empty();
            var returnQty = $('#returnQty' + id).val();
            var comboSelect = $('#returnCombo' + id).val();
            if (comboSelect == "custom") {
                $('#hideReturnPrice' + id).val(0);
                $('#returnPriceDiv' + id).append(
                    '<input type="number" min="0" class="form-control form-control-sm" style="margin-top:10px" ' +
                    'name="manualReurnPrice" id="manualReurnPrice' + id + '" value="0.00" ' +
                    'oninput="validity.valid || (this.value = \'\')" ' +
                    'onkeyup="addReurnManual(' + id + ', this.value)" ' +
                    'onblur="checkReturnPriceEmpty(' + id + ')" />'
                );
                changeSubTotal(id);
            } else {
                if (returnQty == "0") {
                    //swal("Sorry!", "Enter Return Qty!", "warning");
                    if (comboSelect == "select") {
                        $('#hideReturnPrice' + id).val(0);
                    } else {
                        $('#hideReturnPrice' + id).val(comboSelect);
                    }
                } else {
                    var comboSelect = $('#returnCombo' + id).val();
                    if (comboSelect == "select") {
                        $('#hideReturnPrice' + id).val(0);
                    } else {
                        $('#hideReturnPrice' + id).val(comboSelect);
                    }
                    changeSubTotal(id);
                }
            }
        }


        function addReurnManual(id, typeVal) {
            $('#hideReturnPrice' + id).val(typeVal);
            changeSubTotal(id);
        }


        function checkReturnPriceEmpty(id) {
            let input = document.getElementById('manualReurnPrice' + id);
            let value = input.value.trim();

            if (value === "") {
                // swal("", "Please enter a valid return price!", "warning");
                $('#hideReturnPrice' + id).val(0);
                changeSubTotal(id);
                input.value = "0.00";
                input.focus();
            }
        }


        function validateReturnQty(id) {
            var returnPrice = $('#returnCombo' + id).val();
            if (returnPrice == "select") {
                swal("Sorry!", "Select a price!", "warning");
            } else {
                changeSubTotal(id);
            }
        }


        function changeSubTotal(id) {
            if ($('#returnQty' + id).val() == "") {
                $('#returnQty' + id).val('0');
            }
            if ($('#qtyActual' + id).val() == "") {
                $('#qtyActual' + id).val('0');
            }
            var arr = document.getElementsByName('qtyActual');
            var scale = document.getElementsByName('sellingPrice');
            var sellingHidden = document.getElementsByName('sellingPriceNormal');
            var reurn = document.getElementsByName('returnQty');
            var availableQty = document.getElementsByName('availableQty');
            var returnPriceEnter = document.getElementsByName('hideReturnPrice');
            var discountedQty = document.getElementsByName('discountable_qty');
            var discountedPrice = document.getElementsByName('discounted_price');

            var tot = 0;
            var ActualQty = 0;
            var qtyPrice = 0;
            var issuePrice = 0;
            var returnPrice = 0;

            for (var i = 0; i < arr.length; i++) {
                if (id == i + 1) {
                    if (arr[i].value != "" && scale[i].value != "") {
                        if (parseFloat(arr[i].value) > parseFloat(availableQty[i].value)) {
                            swal("Sorry!", " Qty Cannot be greater than to Available Qty!", "warning");
                            $("#qtyActual" + id).val(0);
                            $("#returnQty" + id).val(0);
                        }
                        //    else if(parseFloat(reurn[i].value) >  parseFloat(arr[i].value)){

                        //         swal("Sorry!", "Return Qty Cannot be greater than to Qty!", "warning");

                        //         $("#returnQty"+id).val(0);

                        //     }

                        if (parseFloat(arr[i].value) >= parseFloat(discountedQty[i].value)) {
                            $("#sellingPrice" + id).val(discountedPrice[i].value);
                        } else if (parseFloat(arr[i].value) < parseFloat(discountedQty[i].value)) {
                            $("#sellingPrice" + id).val(sellingHidden[i].value);
                            // ;
                        }
                    }

                    var ActualPrice = scale[i].value;

                    ActualQty = parseFloat(arr[i].value) - parseFloat(reurn[i].value);
                    //qtyPrice = ActualQty * parseFloat(ActualPrice);
                    issuePrice = parseFloat(arr[i].value) * parseFloat(ActualPrice);
                    returnPrice = parseFloat(reurn[i].value) * parseFloat(returnPriceEnter[i].value)
                    //    if(arr[i].value == 0){
                    //     qtyPrice = parseFloat(returnPrice);
                    //    }else{
                    qtyPrice = parseFloat(issuePrice) - parseFloat(returnPrice);
                    //  }
                    //alert(qtyPrice);

                    if (isNaN(qtyPrice)) {
                        $('#qtyTot' + id).html('0.0');
                    } else {
                        $('#qtyTot' + id).html(qtyPrice.toFixed(2));
                    }

                    if (isNaN(issuePrice)) {
                        $('#IssueTot' + id).html('0.0');
                    } else {
                        $('#IssueTot' + id).html(issuePrice.toFixed(2));
                    }

                    if (isNaN(returnPrice)) {
                        $('#returnTot' + id).html('0.0');
                    } else {
                        $('#returnTot' + id).html(returnPrice.toFixed(2));
                    }
                }
            }

            calculateTotal();
            // if(isNaN(tot)){
            //     $("#totSub").text(0);
            // }else{
            //     $("#totSub").text(tot.toFixed(2));
            // }
        }

        var json_invoice_details = {};


        // load  "Generate Invoice"  modal
        function generateInvoiceModal() {
            var csrf_token = $("#csrf_token").val();
            var totReturn = $('#totReturn').val();
            var totSubReal = $('#totSubReal').text();
            var is_forbiddenItemsAdded = false;
            // get the tbody element of the table
            const tbody = document.querySelector('#TblAddReturnToTBL tbody');
            const tableRows = tbody.querySelectorAll('tr');
            var forbidden_product_count = 0;

            // Iterate through each row of the tbody starting from the first row
            for (var i = 0; i < tableRows.length; i++) {
                // Get the Quantity cell of the row
                const cell = tableRows[i].cells[3];
                // Get all the input tags inside the cell with the forbiddenProduct_input class name
                const inputs = cell.querySelectorAll('.forbiddenProduct_input');
                // Iterate through each "forbidden input"  and  get it's quantity as forbidden_product_count
                for (var j = 0; j < inputs.length; j++) {
                    forbidden_product_count += Number(inputs[j].value);
                }
            }

            if (forbidden_product_count > 0) {
                is_forbiddenItemsAdded = true;
            }

            // Check all manualReurnPrice inputs for zero
            const manualPriceInputs = document.querySelectorAll("input[name='manualReurnPrice']");
            for (let input of manualPriceInputs) {
                let val = parseFloat(input.value);
                if (isNaN(val) || val === 0) {
                    swal("", "Custom return price cannot be 0.00 Please recheck all the return prices and try again.", "warning");
                    input.focus();
                    return;
                }
            }

            jQuery.ajax({
                url: "{{ url('/loadGenerateInoviceModal') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "page": 1,
                    "totSubReal": totSubReal,
                    "totReturn": totReturn,
                    "is_forbiddenItemsAdded": is_forbiddenItemsAdded
                },
                beforeSend: function() {
                    showLder();
                },
                complete: function() {},
                error: function(data) {},
                success: function(data) {
                    hideLder();
                    $('#generateInvoiceModal').modal('show');
                    $("#generateInvoiceModal_ContentDIV").html(data);
                }
            });
        }


        // save Funtion for Grenerate Invoice button
        function saveInvoiceData() {
            var takenRackCount = $("#takenRackCount").val();
            var givenRackCount = $("#givenRackCount").val();
            var csrf_token = $("#csrf_token").val();
            var rowCount = document.getElementById("tableInvoiceData").rows.length;
            var invoice_details = new Array();
            for (var x = 0; x < rowCount; x++) {
                var qty = document.getElementById("tableInvoiceData").rows[x].cells[3].children[0].value;
                var returnQty = document.getElementById("tableInvoiceData").rows[x].cells[4].children[1].value;
                var unitPrice = document.getElementById("tableInvoiceData").rows[x].cells[6].children[0].value;
                var stockId = document.getElementById("tableInvoiceData").rows[x].cells[7].innerHTML;
                var vehicleId = document.getElementById("tableInvoiceData").rows[x].cells[8].innerHTML;
                var proId = document.getElementById("tableInvoiceData").rows[x].cells[14].innerHTML;
                var returnVal = document.getElementById("tableInvoiceData").rows[x].cells[4].children[3].value;
                if ((qty > 0 || qty == '') || (returnQty > 0 || returnQty == '')) {
                    if (qty == '') {
                        qty = 0;
                    }
                    if (stockId == '') {
                        stockId = 0;
                    }
                    var rowOfData = {};
                    rowOfData['qty'] = qty;
                    rowOfData['returnQty'] = returnQty;
                    rowOfData['unitPrice'] = unitPrice;
                    rowOfData['stockId'] = stockId;
                    rowOfData['vehicleId'] = vehicleId;
                    rowOfData['proId'] = proId;
                    rowOfData['returnVal'] = returnVal;
                    invoice_details.push(rowOfData);
                }
            }

            json_invoice_details['invoiceDetails'] = invoice_details;
            var customer = $("#customer").val();
            var invoiceType = $("#invoiceType").val();
            var subTotal = $('#totSubReal').text();
            var customersMaxCreditAmount = $('#customerMaxCreditAmount').val();

            if (rowCount == 0) {
                swal("Sorry!", " You dont have stock batches.", "warning");
            } else if (customer == 0) {
                swal("", "Please select a customer.", "warning");
            } else if ((Number(customersMaxCreditAmount) < Number(subTotal)) && (invoiceType == 1)) {
                swal("Maximum Credit Amount Exceeded!", "Sub-Total exceeded the selected customer's Maximum Credit Amount! Please reduce the Total and retry Generate Invoice.", "warning");
            } else if (invoiceType == 0) {
                swal("", "Please select invoice type.", "warning");
            } else if (invoice_details.length == 0) {
                swal("Stop", "Please add products and quantities before generate invoice.", "warning");
            } else if ($("#totSubReal").text() <= 0) {
                swal("", "Invalid Invoice Price.", "warning");
            } else if (takenRackCount == '') {
                swal("", "Please enter valid Taken Rack Count.", "warning");
            } else if (givenRackCount == '') {
                swal("", "Please enter valid Given Rack Count.", "warning");
            } else {
                jQuery.ajax({
                    url: "{{ url('/saveInvoiceData') }}",
                    type: "POST",
                    data: {
                        "invoiceData": json_invoice_details,
                        "_token": csrf_token,
                        "page": 1,
                        "customer": customer,
                        "invoiceType": invoiceType,
                        "subTotal": $('#totSubReal').text(),
                        "totReturn": $('#totReturn').val(),
                        "totIssue": $('#totIssue').val(),
                        "givenRackCount": givenRackCount,
                        "takenRackCount": takenRackCount,
                        "deliveryIDToReturn": $("#deliveryIDToReturn").val(),
                        // "subDiscount": $('#totDiscount').val(),
                        // "displayDiscount": $('#totDiscountDisplay').val(),
                        "subDiscount": Number($('#genLoyaltyDiscount').text().replace(/,/g, '')),
                        "displayDiscount": Number($('#genDisplayDiscount').text().replace(/,/g, '')),
                        "specialDiscount": Number($('#genSpecialDiscount').text().replace(/,/g, '')),
                        "customDiscount": 0.0,
                        "customDiscountPercentage": 0.0
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
                        if (data.status == 'true') {
                            swal("", "Invoice Generated Successfully.", "success");
                            returnToInvoicePage(data.InvoiceId);
                            $('#generateInvoiceModal').modal('hide');
                            // window.location = "/adminInvoicePrint/"+data.InvoiceId;
                        } else if (data.status == 'rack') {
                            swal("Sorry! Update Customer Rack Count", data.msgDB, "warning");

                        } else if (data.status == 'false') {
                            swal("Sorry! Enter Your invoice again", data.msgDB, "warning");
                        } else {
                            swal("Error", "Error occured !!", "danger");
                        }
                    }
                });
            }
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
                    $("#InvoiceContent").css("background-color", "#fff");
                }
            });
        }


        function addReturns() {
            var product = $("#productsReturn").val();
            var returnQty = $("#ReturnQty").val();
            var hiddenUnit = $("#hiddenReturnUnitPrice").val();
            var hiddenDeliveryId = $("#hiddenDeliveryId").val();
            var total = returnQty * parseFloat(hiddenUnit);
            var vehicleId = $("#returnProId").val();
            var tableUsers = document.getElementById("TblAddReturnToTBL");
            var rowCount = tableUsers.rows.length;
            var dublicateData = true;
            if (returnQty == 0 || returnQty == '') {
                swal("Sorry", "Enter Valid Return Qty !", "warning");
            } else if (product == 0) {
                swal("Sorry", "Select a Product !", "warning");
            } else if (hiddenUnit == '' || hiddenUnit == '0') {
                swal("Sorry", "Enter Valid Return Price !", "warning");
            } else {
                for (var i = 1; i < rowCount; i++) {
                    var Checkvehicle = document.getElementById("TblAddReturnToTBL").rows[i].cells[14].innerText;
                    if (parseInt(Checkvehicle) == parseInt(vehicleId)) {
                        dublicateData = false;
                    }
                }

                if (dublicateData) {
                    var row = tableUsers.insertRow(1);
                    $('#TblAddReturnToTBL > tbody:last').append(row);
                    var cell1 = row.insertCell(0);
                    var cell2 = row.insertCell(1);
                    var cell3 = row.insertCell(2);
                    var cell4 = row.insertCell(3);
                    var cell5 = row.insertCell(4);
                    var cell6 = row.insertCell(5);
                    var cell7 = row.insertCell(6);
                    var cell8 = row.insertCell(7);
                    var cell9 = row.insertCell(8);
                    var cell10 = row.insertCell(9);
                    var cell11 = row.insertCell(10);
                    var cell12 = row.insertCell(11);
                    var cell13 = row.insertCell(12);
                    var cell14 = row.insertCell(13);
                    var cell15 = row.insertCell(14);
                    var cell16 = row.insertCell(15);
                    var cell17 = row.insertCell(16);

                    cell1.innerHTML = '0';
                    cell2.innerHTML = $("#productsReturn option:selected").text();
                    cell3.innerHTML = '';
                    cell4.innerHTML = "<input type='hidden' value=''/>";
                    cell5.innerHTML = "<lable>Qty</lable><input type='text' class='form-control form-control-sm' value='" + returnQty +
                        "' disabled/><lable>Price</lable><input type='text' class='form-control form-control-sm' value='" + hiddenUnit + "' disabled/><br/>";
                    cell6.innerHTML = '';
                    cell7.innerHTML = "<input type='text' value='" + hiddenUnit + "' disabled/>";
                    cell8.innerHTML = '';
                    cell9.innerHTML = hiddenDeliveryId;
                    cell10.innerHTML =
                        "<button onclick='removeFunction(this)' type='button' class='btn btn-xs btn-danger' value='Remove'><i class='fa fa-trash-o' aria-hidden='true'></i> Remove</button>";
                    cell11.innerHTML = '-' + total.toFixed(2);
                    cell12.innerHTML = '';
                    cell13.innerHTML = '';
                    cell14.innerHTML = '';
                    cell15.innerHTML = vehicleId;
                    cell16.innerHTML = '0.0';
                    cell17.innerHTML = total.toFixed(2);

                    cell1.style.display = "none";
                    cell3.style.display = "none";
                    cell8.style.display = "none";
                    cell9.style.display = "none";
                    cell12.style.display = "none";
                    cell13.style.display = "none";
                    cell14.style.display = "none";
                    cell15.style.display = "none";
                    cell16.style.display = "none";
                    cell17.style.display = "none";

                    changeSubTotal();
                    $("#ReturnQty").val(0);
                    $("#ReturnPrice2").val(0);
                    $("#hiddenReturnUnitPrice").val(0);
                    $('#productsReturn option[value="0"]').attr("selected", true);
                } else {
                    swal("Stop", "This Delivery Stock already in Table !", "error");
                }
            }
        }


        function removeFunction(oButton) {
            var empTab = document.getElementById('TblAddReturnToTBL');
            empTab.deleteRow(oButton.parentNode.parentNode.rowIndex);
            calculateTotal();
        }


        // Calculate TOTAL
        function calculateTotal() {
            var table = document.getElementById("TblAddReturnToTBL");
            var sumVal = 0.0;
            var sumIssueVal = 0.0;
            var sumReturnVal = 0.0;
            var sumDiscount = 0.0;
            var discountableItemTotalPrice = 0;
            var nonDiscountableProductsTotalPrice = 0;
            var genProductTotal = parseFloat($("#genProductTotal").text());

            // loop through each Row of the Product table
            for (var i = 1; i < table.rows.length; i++) {
                var NanCheck = table.rows[i].cells[10].innerHTML;
                var IssueTotNan = table.rows[i].cells[15].innerHTML;
                var ReturnTotNan = table.rows[i].cells[16].innerHTML;

                if (!isNaN(parseFloat(NanCheck)) && NanCheck !== "") {
                    sumVal += parseFloat(NanCheck);
                }
                if (!isNaN(parseFloat(IssueTotNan)) && IssueTotNan !== "") {
                    sumIssueVal += parseFloat(IssueTotNan);
                }
                if (!isNaN(parseFloat(ReturnTotNan)) && ReturnTotNan !== "") {
                    sumReturnVal += parseFloat(ReturnTotNan);
                }

                // Get qty total as number
                var qtyTotalValue = parseFloat($("#qtyTot" + i).text().replace(/,/g, '')) || 0;

                // --------------------------------------------- SKIP PRODUCT DISCOUNT SECTION -------------------------------------------------------------------------------
                // get Main Category Id   and  Sub-Category Id
                var productMainCategory_id = parseInt(table.rows[i].getAttribute("product-main-category"));
                var productSubCategory_id = parseInt(table.rows[i].getAttribute("product-sub-category"));

                // get Total Amount for Non Discountable Products considering "Main Categories" = (4, 6)  and  "Sub-Categories" = (59, 60, 61)
                if (qtyTotalValue > 0) {  // ✅ Only include positive values
                    if (productMainCategory_id === 4 ||
                        productMainCategory_id === 6 ||
                        productSubCategory_id === 59 ||
                        productSubCategory_id === 60 ||
                        productSubCategory_id === 61
                    ) {
                        nonDiscountableProductsTotalPrice += qtyTotalValue;
                    } else {
                        discountableItemTotalPrice += qtyTotalValue;
                    }
                }
            }

            // nonDiscountableProductsTotalPrice = Math.abs(nonDiscountableProductsTotalPrice);

            var subDiscountTot = 0.0;
            var allSubDis = 0.0;
            var finalTotal = 0.0;

            var grossAmount = sumIssueVal;
            var loyaltyDiscount = 0.0;
            var displayDiscount = 0.0;
            var specialDiscount = 0.0;

            // calculate Loyalty Discount (2% rate)
            if ($("#loyalty").prop("checked") == true) {
                loyaltyDiscount = ((grossAmount - sumReturnVal - nonDiscountableProductsTotalPrice) * 2) / 100;
                // allSubDis = parseFloat(sumIssueVal) - parseFloat(subDiscountTot);
                // finalTotal = parseFloat(allSubDis) - parseFloat(sumReturnVal);
            }

            // calculate Display Discount (2% rate)
            if ($("#display").prop("checked") == true) {
                displayDiscount = ((grossAmount - sumReturnVal - nonDiscountableProductsTotalPrice) * 2) / 100;
                // allSubDis = parseFloat(sumIssueVal) - parseFloat(subDiscountTot);
                // finalTotal = parseFloat(allSubDis) - parseFloat(sumReturnVal);
            }

            // calculate Special Discount (2% rate)
            if ($("#special").prop("checked") == true) {
                specialDiscount = ((grossAmount - sumReturnVal - nonDiscountableProductsTotalPrice) * 2) / 100;
            }

            allSubDis = parseFloat(grossAmount) - parseFloat(sumReturnVal) - parseFloat(loyaltyDiscount) - parseFloat(displayDiscount) - parseFloat(specialDiscount);
            finalTotal = parseFloat(allSubDis) - parseFloat(sumReturnVal);

            // console.log("genProductTotal -- "+grossAmount);
            // console.log("allSubDis -- "+allSubDis);
            // console.log("finalTotal -- "+finalTotal);
            // console.log("sumReturnVal -- "+sumReturnVal);
            // console.log("nonDiscountableProductsTotalPrice ==> "+nonDiscountableProductsTotalPrice);
            // console.log("===========================");

            $("#totSubReal").text(sumVal.toFixed(2));
            $("#totSub").text(finalTotal.toFixed(2));
            $("#totIssue").val(grossAmount.toFixed(2));
            $("#totReturn").val(sumReturnVal.toFixed(2));
            $("#totDiscount").val(loyaltyDiscount.toFixed(2));
            $("#totDiscountDisplay").val(displayDiscount.toFixed(2));
            // $("#totSpecialDiscount").val(specialDiscount.toFixed(2));

            // set values in the "Generate Inovice modal"
            $("#discountableItemTotalPrice").text(discountableItemTotalPrice.toLocaleString("en-US", {minimumFractionDigits: 2, maximumFractionDigits: 2})); // Discountable Item Total Price
            $("#nonDiscountableItemTotalPrice").text(nonDiscountableProductsTotalPrice.toLocaleString("en-US", {minimumFractionDigits: 2, maximumFractionDigits: 2})); // Non-Discountable Item Total Price
            $("#genSubTotal").text(sumVal.toLocaleString("en-US", {minimumFractionDigits: 2, maximumFractionDigits: 2})); // Subtotal
            $("#genLoyaltyDiscount").text(loyaltyDiscount.toLocaleString("en-US", {minimumFractionDigits: 2, maximumFractionDigits: 2})); // Loyalty Discount
            $("#genDisplayDiscount").text(displayDiscount.toLocaleString("en-US", {minimumFractionDigits: 2, maximumFractionDigits: 2})); // Display Discount
            $("#genSpecialDiscount").text(specialDiscount.toLocaleString("en-US", {minimumFractionDigits: 2, maximumFractionDigits: 2})); // Special Discount
            $("#genTotal").text(allSubDis.toLocaleString("en-US", {minimumFractionDigits: 2, maximumFractionDigits: 2})); // Total
        }


        function checkCreditBilAvailability() {
            var csrf_token = $("#csrf_token").val();
            var customer = $("#customer option:selected").val();
            $("#creditBilButtonDiv").empty();
            jQuery.ajax({
                url: "{{ url('/checkCreditBilAvailability') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "customer": customer,
                },
                beforeSend: function() {
                    showLder();
                },
                complete: function() {},
                error: function(data) {},
                success: function(data) {
                    hideLder();
                    $("#customerHideenID").val(data.rack);
                    $("#customerMaxCreditAmount").val(data.maxCreditAmount);
                    if (data.customerCreditStatus == true) {
                        // $('#customer').select2("val", 0);
                        $("#loadStockDeliveryDataTbl").empty();
                        $("#invoiceType").val(0);
                        loadCustomercreditModal(data.customer, data.customerAllCreditStatus, data
                            .customerCreditStatus);
                        // $('#invoiceType').select2("val", 0);
                    }

                    //    if(data.customerAllCreditStatus == true){
                    //        alert('s');

                    //     $("#creditBilButtonDiv").append("<button type='button' class='btn btn-success btn-sm' onclick='loadAllCreditBills("+data.customer+")'>View Credit Bills</button>");
                    //    }
                }
            });
        }


        function loadCustomercreditModal(customer, cusStatus, cusCreditStatus) {
            var csrf_token = $("#csrf_token").val();
            jQuery.ajax({
                url: "{{ url('/loadCustomercreditModal') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "customer": customer,
                },
                beforeSend: function() {
                    showLder();
                },
                complete: function() {},
                error: function(data) {},
                success: function(data) {
                    hideLder();
                    $('#creditModal').modal('show');
                    $("#CustomercreditModalContent").html(data);
                    if (cusCreditStatus == true) {
                        $("#creditBilButtonDiv").append("<button type='button' class='btn btn-danger btn-sm' onclick='loadAllCreditBills(" + customer + ")'><i class='fa fa-list-alt' aria-hidden='true'></i> &nbsp; View Credit Bills</button>");
                    }
                }
            });
        }


        function loadAllCreditBills(customer) {
            var csrf_token = $("#csrf_token").val();
            jQuery.ajax({
                url: "{{ url('/loadAllCreditBillstoTBL') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "customer": customer,
                },
                beforeSend: function() {
                    showLder();
                },
                complete: function() {},
                error: function(data) {},
                success: function(data) {
                    hideLder();
                    $('#creditBillsLoadModal').modal('show');
                    $("#CustomercreditBillModalContent").html(data);
                }
            });
        }


        function validateInvoiceType() {
            $("#loadStockDeliveryDataTbl").empty();
            var csrf_token = $("#csrf_token").val();
            var customer = $("#customer option:selected").val();
            var invoiceType = $("#invoiceType option:selected").val();

            if (customer === "0") {
                swal("", "Please select a Customer first.", "warning");
                $('#invoiceType').val("0").trigger('change');
                // $('#invoiceType').val("0");
            } else {
                jQuery.ajax({
                    url: "{{ url('/validateInvoiceType') }}",
                    type: "POST",
                    data: {
                        "_token": csrf_token,
                        "customer": customer,
                        "invoiceType": invoiceType,
                    },
                    beforeSend: function() {
                        showLder();
                    },
                    complete: function() {},
                    error: function(data) {},
                    success: function(data) {
                        if (data.customerCreditStatus == true) {
                            hideLder();
                        } else {
                            if (data.msg == 'creditBillExceed') {
                                hideLder();
                                $('#invoiceType').val(0);
                                swal("Sorry", "The customer's credit bill count has already exceeded.", "error");
                                $('#creditModal').modal('show');
                                // $('#invoiceType').select2("val", 0);
                            } else if (data.msg == "creditBillAvailabilityExceed") {
                                hideLder();
                                $('#invoiceType').val(0);
                                swal("Stop", "Customer Credit bill availability Exceed", "error");
                            } else {
                                hideLder();
                            }
                        }
                    }
                });
            }
        }


        function changeTakenValue(val) {
            var inputCustomerVal = $("#customerHideenID").val();
            if ($("#customer").val() == '0') {
                swal("Sorry!", "Select a Customer", "warning");
                $("#takenRackCount").val(0);
                $("#givenRackCount").val(0);
            } else {
                if (inputCustomerVal == "NO") {
                    swal("Sorry!", "Update Customer Rack Count", "warning");
                    $("#takenRackCount").val(0);
                    $("#givenRackCount").val(0);
                } else {
                    if (parseFloat(val) > parseFloat(inputCustomerVal)) {
                        swal("Sorry!", "Entered Rack Count Exceed to customer rack count", "warning");
                        $("#takenRackCount").val(0);
                        $("#givenRackCount").val(0);
                    }
                }
            }
        }


        function changeGivenValue(val) {
            var inputDeliveryVal = $("#hiddenDeliveryId21").val();
            if ($("#customer").val() == '0') {
                swal("Sorry!", "Select a Customer", "warning");
                $("#takenRackCount").val(0);
                $("#givenRackCount").val(0);
            } else {
                if (inputDeliveryVal == "NO") {
                    swal("Sorry!", "You don't have assign delivery vehicle", "warning");
                    $("#takenRackCount").val(0);
                    $("#givenRackCount").val(0);
                } else {
                    if (parseFloat(val) > parseFloat(inputDeliveryVal)) {
                        swal("Sorry!", "Entered Rack Count Exceed to delivery unloading rack count", "warning");
                        $("#takenRackCount").val(0);
                        $("#givenRackCount").val(0);
                    }
                }
            }
        }


        // setInputFilter(document.getElementById("uintTextBox"), function(value) {
        //     return /^\d*$/.test(value);
        // });
    </script>
@endsection
