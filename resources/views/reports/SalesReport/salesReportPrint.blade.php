<html>
<head>
    <meta charset="utf-8">
    <title></title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="Preview page of MI7 Admin Theme #2 for statistics, charts, recent events and reports"
            name="description" />
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
                size: A4 landscape
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

                .noPrint {
                    display: none;
                }
            }

            table.table {
                border: 1px solid #fff;
            }

            table.table>thead>tr>th {
                border: 1px solid #fff;
            }

            table.table>tbody>tr>td {
                border: 1px solid #fff;
            }

            table.table-bordered {
                border: 1px solid #aaa;

            }

            table.table-bordered>thead>tr>th {
                border: 1px solid #aaa;
                padding: 1px;
            }

            table.table-bordered>tbody>tr>td {
                border: 1px solid #aaa;
                padding: 1px;
            }

            table.table-bordered>tfoot>tr>td {
                border: 1px solid #aaa;
                padding: 1px;
            }
        </style>
</head>

<body class="A4 landscape" style="font-size: small;text-align: justify">
    <section class="sheet">
        <input type="button" onclick="window.print();" class="form-control btn btn-success noPrint"
            value="Click Here To Print This Page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12">
                    <center>
                        <h1>Sales Report</h1>
                    </center>
                </div>
            </div>
            <div class="row">
                <table class="table table-bordered" style="width: 100%;font-size: large;text-align: center">
                    <thead>
                        <tr style="height: 25px;">
                            <th>#</th>
                            <th>Customer Name</th>
                            <th>Invoice No</th>
                            <th>Date</th>
                            <th>Invoice Type</th>
                            <th>Invoice Amount</th>
                            <th>Paid Amount</th>
                            <th>Due Amount</th>
                            <th>Status</th>
                            <th>Payment History</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                    <tfoot>
                        <tr style="font-weight: bold">
                            <td colspan="5" style="text-align: center">Total</td>
                            <td id="sumPriceInput">0.0</td>
                            <td id="sumPricePaidInput">0.0</td>
                            <td id="sumPriceDueInput">0.0</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </section>

</body>
</html>
