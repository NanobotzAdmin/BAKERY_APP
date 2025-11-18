{{-- @php


$privilageId = \DB::table('pm_interfaces')
->select('pm_interfaces.id AS pageId','pm_interface_topic.id AS grupId')
->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
->where('pm_interfaces.path','adminRouteWiseCreditReport')
->first();


@endphp --}}
<html>
<head>
    <meta charset="utf-8">
        <title></title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="Preview page of MI7 Admin Theme #2 for statistics, charts, recent events and reports" name="description" />
        <meta content="" name="author" />
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <!-- Normalize or reset CSS with your favorite library -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.css">
        <!-- Load paper.css for happy printing -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.2.3/paper.css">
        <link href="../../assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <!-- Set page size here: A5, A4 or A3 -->
        <!-- Set also "landscape" if you need -->
        <style>
            @page {
                size: A4
            }

            .smallText {
                font-size: small;
            }

            body {
                background-color: #CCC;
                font-size: small;
            }

            .table-condensed>thead>tr>th,
            .table-condensed>tbody>tr>th,
            .table-condensed>tfoot>tr>th,
            .table-condensed>thead>tr>td,
            .table-condensed>tbody>tr>td,
            .table-condensed>tfoot>tr>td {
                padding: 3px;
            }

            .sheet {
                padding: 5mm;
            }

            @media print {
                .avoid {
                    page-break-inside: avoid;
                }

                .sheet {
                    padding: 10mm;
                }

                page[size="A4"][layout="portrait"] {
                    width: 29.7cm;
                    height: 21cm;
                }

                .noPrint {
                    display: none;
                }
            }

            table th {
                font-size: 18px;
                padding-left: 5px !important;
                padding-right: 5px !important;
            }

            table td {
                padding-left: 5px !important;
                padding-right: 5px !important;
            }

            table.table {
                border: 1px solid #fff;
                padding: 1px;
            }

            table.table>thead>tr>th {
                border: 1px solid #fff;
                padding: 1px;
            }

            table.table>tbody>tr>td {
                border: 1px solid #fff;
                padding: 1px;
            }

            table.table-bordered {
                border: 1px solid #aaa;
                padding: 2px;
            }

            table.table-bordered>thead>tr>th {
                border: 1px solid #aaa;
                padding: 2px;
            }

            table.table-bordered>tbody>tr>td {
                border: 1px solid #aaa;
                padding: 2px;
            }
        </style>
</head>

<body class="A4" style="font-size: small;text-align: justify">
    <section class="sheet" style="height: auto;width: 32cm">
        <input type="button" onclick="window.print();" class="form-control btn btn-success noPrint"
            value="Click Here To Print This Page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12">
                    <center>
                        <h1>Route Wise Credit Report &nbsp;—&nbsp; {{ $routeName }}</h1>
                    </center>
                </div>
            </div>
            <br><br>

            <div class="row">
                <table class="table table-bordered" style="width: 100%;">
                    <thead>
                        <tr style="height: 30px;">
                            <th style="min-width: 30px; text-align: left;">#</th>
                            <th style="min-width: 110px; text-align: left;">Invoice Number</th>
                            <th style="min-width: 200px; text-align: left;">Customer Name</th>
                            <th style="text-align: left;">Date</th>
                            <th style="text-align: left;">Full Amount</th>
                            <th style="text-align: left;">Paid Amount</th>
                            <th style="min-width: 40px; text-align: left;">Age</th>
                            <th style="text-align: left;">Received Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $id = 0; ?>
                        @foreach ($data as $datas)
                            <?php
                                $customer = App\Customer::find($datas->cm_customers_id);
                            ?>
                            @if ($customer->is_active == 1)
                                <?php
                                $id++;
                                $netPrice = (float) $datas->net_price - ((float) $datas->discount + (float) $datas->display_discount  + (float) $datas->special_discount);
                                $paid = $datas->total_amout_paid;
                                $fdate = $datas->created_at;
                                $tdate = Carbon\Carbon::now();
                                $dateFromFormat = date('Y-m-d', strtotime($fdate));
                                $dateToFormat = date('Y-m-d', strtotime($tdate));
                                $diff = strtotime($dateToFormat) - strtotime($dateFromFormat);
                                $interval = abs(round($diff / 86400));
                                ?>
                                <tr>
                                    <td style="text-align: left;">{{ $id }}</td>
                                    <td>{{ $datas->invoice_number }}</td>
                                    <td>{{ $customer->customer_name }}</td>
                                    <td>{{ $dateFromFormat }}</td>
                                    <td style="text-align: right;"><?php echo number_format((float) $netPrice, 2, '.', ','); ?></td>
                                    <td style="text-align: right;"><?php echo number_format((float) $paid, 2, '.', ','); ?></td>
                                    <td style="text-align: right;">{{ $interval }}</td>
                                    <td></td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                    </tbody>
                </table>
            </div>

        </div>
    </section>

</body>

</html>
