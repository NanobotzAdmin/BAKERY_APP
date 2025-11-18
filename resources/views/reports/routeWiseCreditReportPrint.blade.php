
<html>
    <head>

        <meta charset="utf-8">
        <title</title>

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
            @page { size: A4 }
            .smallText{
                font-size: small;
            }
            body{
                background-color: #CCC;
                font-size: small;
            }
            .table-condensed>thead>tr>th, .table-condensed>tbody>tr>th, .table-condensed>tfoot>tr>th, .table-condensed>thead>tr>td, .table-condensed>tbody>tr>td, .table-condensed>tfoot>tr>td{
                padding: 3px;
            }
            .sheet{
                padding: 5mm;
            }
            @media print {
                .avoid{page-break-inside: avoid;}
                .sheet{
                    padding: 10mm;
                }
                page[size="A4"][layout="portrait"] {
                    width: 29.7cm;
                    height: 21cm;  
                }
                .noPrint{
                    display: none;
                }
            }

            table.table{
                border:1px solid #fff;
            }
            table.table > thead > tr > th{
                border:1px solid #fff;
            }
            table.table > tbody > tr > td{
                border:1px solid #fff ;
            }
            table.table-bordered{
                border:1px solid #aaa;

            }
            table.table-bordered > thead > tr > th{
                border:1px solid #aaa;
            }
            table.table-bordered > tbody > tr > td{
                border:1px solid #aaa ;
            }
        </style>
    </head>
    <body class="A4" style="font-size: small;text-align: justify">
        <section class="sheet" style="height: auto;width: 32cm">
            <input type="button" onclick="window.print();" class="form-control btn btn-success noPrint" value="Click Here To Print This Page">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-12">
                        <center>
                            <h2>Route Wise Credit Report &nbsp;&nbsp; - &nbsp;&nbsp; (Route Name)</h2>
                            </center>
                    </div>
                </div>
                <br><br>
               
                <div class="row">
                   <table class="table table-bordered" style="width: 100%">
                    <thead>
                        <tr style="height: 30px">
                            <th>#</th>
                            <th>Invoice No</th>
                            <th>Customer Name</th>
                            <th> Date</th>
                            <th>Full Amount</th>
                            <th>Paid Amount</th>
                            <th>Age</th>
                            <th>Received Amount</th>
                           </tr>
                    </thead>
                    <tbody>

                    </tbody>
                   </table>
                </div>

            </div> 
        </section>

        </body>
</html>

