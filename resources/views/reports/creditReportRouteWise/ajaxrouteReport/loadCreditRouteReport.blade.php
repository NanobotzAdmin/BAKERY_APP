{{-- <a href=" {{ url('loadCreditReportPrint/'.(empty($dateFromFormat)?"ANY":$dateFromFormat).'/'.(empty($dateToFormat)?"ANY":$dateToFormat).'/'.$routeSend)}}  " target="_blank"><button type="button" class="btn btn-success btn-sm" >Print</button></a> --}}

<div class="col-sm-12">
    <div class="ibox">
        <div class="ibox-content">
            <a href="{{ url('loadCreditReportPrint/' . (empty($dateFromFormat) ? 'ANY' : $dateFromFormat) . '/' . (empty($dateToFormat) ? 'ANY' : $dateToFormat) . '/' . $routeSend) }}" target="_blank"><button type="button" class="btn btn-primary btn-sm btn-block"><i class="fa fa-print" aria-hidden="true"></i> &nbsp; Print Report</button></a>
            <br><br>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover dataTables-example">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Invoice Number</th>
                            <th>Customer Name</th>
                            <th>Date</th>
                            <th style="text-align: right; padding-right: 30px;">Full Amount</th>
                            <th style="text-align: right; padding-right: 30px;">Paid Amount</th>
                            <th style="text-align: right; padding-right: 30px;">Age</th>
                            <th>Received Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $id = 0; ?>
                        @foreach ($data as $datas)
                            <?php
                            $customer = App\Customer::find($datas->cm_customers_id);
                            $netPrice = (float) $datas->net_price - ((float) $datas->discount + (float) $datas->display_discount + (float) $datas->special_discount);
                            $paid = $datas->total_amout_paid;
                            $fdate = $datas->created_at;
                            $tdate = Carbon\Carbon::now();
                            $dateFromFormat = date('Y-m-d', strtotime($fdate));
                            $dateToFormat = date('Y-m-d', strtotime($tdate));
                            $diff = strtotime($dateToFormat) - strtotime($dateFromFormat);
                            $interval = abs(round($diff / 86400));
                            ?>
                            @if ($customer->is_active == 1)
                                <?php
                                $id++;
                                ?>
                                <tr>
                                    <td>{{ $id }}</td>
                                    <td>{{ $datas->invoice_number }}</td>
                                    <td>{{ $customer->customer_name }}</td>
                                    <td>{{ $dateFromFormat }}</td>
                                    <td style="text-align: right; padding-right: 30px;">{{ number_format($netPrice, 2) }}</td>
                                    <td style="text-align: right; padding-right: 30px;">{{ number_format($paid, 2) }}</td>
                                    <td style="text-align: right; padding-right: 30px;">{{ $interval }}</td>
                                    <td></td>
                                </tr>
                            @endif
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
            pageLength: 10,
                responsive: true,
                dom: '<"html5buttons"B>lTfgitp',
                buttons: [
                    {
                        extend: 'pdf',
                        title: 'ExampleFile'
                    },
                    {
                        extend: 'csv'
                    },
                    {
                        extend: 'excel',
                        title: 'ExampleFile'
                    },
                ]
        });
    });
</script>
