<div class="col-sm-12">
    <div class="ibox">
        <div class="ibox-content">

            <div class="table-responsive">
                <table class="table table-bordered table-hover dataTables-example" style="font-family: Verdana, Geneva, sans-serif;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer Name</th>
                            <th style="text-align: right; padding-right: 30px;">Bills Count</th>
                            <th style="text-align: right; padding-right: 30px;">Total Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalBillCount = 0;
                            $totalInvoiceAmount = 0.0;
                        @endphp
                        @foreach ($data as $datas)
                            @php
                                $customer = App\Customer::find($datas->CustomerID);
                                $dateFrom = $dateFromFormat;
                                $dateTo = $dateToFormat;
                                $routeId = $route;
                                // get totals
                                $totalBillCount += $datas->BillCount;
                                $totalInvoiceAmount += $datas->TotalAmount;
                            @endphp
                            <tr>
                                <td style="width: 50px;">{{ $loop->iteration }}</td>
                                <td>{{ $customer->customer_name }}</td>
                                <td style="width: 150px; text-align: right; padding-right: 30px;">{{ $datas->BillCount }}</td>
                                <td style="width: 150px; text-align: right; padding-right: 30px;">{{ number_format($datas->TotalAmount, 2) }}</td>
                                <td style="width: 200px;">
                                    <button type="button" style="background-color: #0084fffb; border-color: #0084fffb;" class="btn btn-success btn-xs" data-target="#view" data-toggle="modal" onclick="ViewRouteWiseSalesReports('{{ $datas->CustomerID }}', '{{ $dateFrom }}', '{{ $dateTo }}', '{{ $routeId }}')">View Bills</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" style="font-weight: bold; text-align: right">TOTAL</td>
                            <td style="font-weight: bold; text-align: right; padding-right: 30px;">{{ $totalBillCount }}</td>
                            <td style="font-weight: bold; text-align: right; padding-right: 30px;">{{ number_format($totalInvoiceAmount, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="view" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document" id="viewSalesReportsDIV">
                    {{-- --content in Ajax-- --}}
                </div>
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
            buttons: [
                {
                    extend: 'pdf',
                    title: 'Route Wise Sales Report'
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

    // VIEW Route Wise Sales Reports to Modal
    function ViewRouteWiseSalesReports(viewCustomerId, selectedDateFrom, selectedDateTo, route) {
        var csrf_token = $("#csrf_token").val();
        jQuery.ajax({
            url: "{{ url('/viewInvoiceListModal') }}",
            type: "POST",
            data: {
                "_token": csrf_token,
                "viewCustomerId": viewCustomerId,
                "selectedDateFrom": selectedDateFrom,
                "selectedDateTo": selectedDateTo,
                "route": route,
            },
            beforeSend: function() {
                showLder();
            },
            complete: function() {
            },
            error: function(data) {
            },
            success: function(data) {
                $("#viewSalesReportsDIV").html(data);
                hideLder();
            }
        });
    }
</script>
