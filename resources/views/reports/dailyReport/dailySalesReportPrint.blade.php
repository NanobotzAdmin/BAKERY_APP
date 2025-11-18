@php


$privilageId = \DB::table('pm_interfaces')
->select('pm_interfaces.id AS pageId','pm_interface_topic.id AS grupId')
->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
->where('pm_interfaces.path','adminDailySalesReport')
->first();


@endphp
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
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

        <!-- Load paper.css for happy printing -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.2.3/paper.css">

        <link href="../../assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <!-- Set page size here: A5, A4 or A3 -->
        <!-- Set also "landscape" if you need -->
        <style>
            @page { size: A4 landscape }
            .smallText{
                font-size: medium;
            }
            body{
                background-color: #CCC;
                font-size: medium;
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

                .noPrint{
                    display: none;
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
                table, tr, td, th {
                    border: 1px solid #000;
                    position: relative;
                    padding: 3px;
                }

                th span {
                    transform-origin: 0 10%;
                    transform: rotate(-90deg);
                    white-space: nowrap;
                    display: block;
                    position: absolute;
                    bottom: 0;
                    left: 10%;
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
            table, tr, td, th {
                border: 1px solid #000;
                position: relative;
                padding: 5px;
            }

            th span {
                transform-origin: 0 10%;
                transform: rotate(-90deg);
                white-space: nowrap;
                display: block;
                position: absolute;
                bottom: 0;
                left: 10%;
            }



        </style>
    </head>
    <body class="A4 landscape" style="font-size: medium;text-align: justify">
        <section class="sheet" style="height: auto">
            <input type="button" onclick="window.print();" class="form-control btn btn-success noPrint" value="Click Here To Print This Page">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-12">
                        <center>
                            <h2>Daily Sales Report &nbsp;&nbsp; - &nbsp;&nbsp;({{ $dateFromFormat }})</h2>
                         </center>
                       </div>
                   </div>
                <div class="row">
                   <table class="table table-bordered" style="width: 100%;font-size: small;">
                        <thead>
                            <tr style="height: 160px;">
                                <th>Customer</th>
                                @foreach ($products as $pro)
                                <th><span>{{ $pro->sub_category_name }}</span></th>
                                @endforeach
                                <th>Cash</th>
                                <th>Credit</th>
                                <th>Market Return</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $datas)
                            <?php


                            $invoice =  App\customerInvoices::find($datas->InvoiceId);
                            $customer = App\Customer::find($invoice->cm_customers_id);

                            ?>

@if($customer->is_active == 1)

                            <tr class="txtMult2">
                            <td>{{ $customer->customer_name }}</td>
                            @foreach ($products as $pro)
                            <?php


                            $proQty = App\customerInvoiceHasStock::where([['dm_customer_invoice_id',$invoice->id],['pm_product_sub_category_id',$pro->id]])->first();

                            if(empty($proQty)){
                            $proQtyInvoice = 0;
                            }else{
                                $proQtyInvoice = $proQty->quantity;
                            }

                            ?>



                            <td class="{{ $pro->id }}">{{ $proQtyInvoice }} </td>



                            <script>




                                $( document ).ready(function() {




                                var mult2 = 0;

                                       // for each row:
                                       $("tr.txtMult2").each(function () {
                                           // get the values from this row:
                                           var $val2 =parseFloat($('.<?php echo $pro->id ?>', this).html());


                                           mult2 += $val2;

                                       });


                                $("#<?php echo $pro->id ?>").html(mult2.toFixed(2));

                                    });





                                </script>




                            @endforeach

                            <?php
                            $tot  = (float)$invoice->net_price + (float)$invoice->return_price;
                            $invoiceNet  = (float)$invoice->net_price;



                            ?>
                            @if($invoice->invoice_type == 1)
                            <td class="val3">0</td>
                            <td class="val4">{{ $invoiceNet }}</td>
                            @else

                            <td class="val3">{{ $invoiceNet }}</td>
                            <td class="val4">0</td>
                            @endif
                            <td class="val5">{{ $invoice->return_price }}</td>
                            <td class="val6">{{ $tot }}</td>
                            </tr>
                            @endif
                            @endforeach

                            <tr class="avoid" >
                                <td style="text-align: center">Total</td>
                                @foreach ($products as $pro)
                                <td  id="{{ $pro->id }}"></td>
                                @endforeach
                                <td id="cashTot"></td>
                                <td id="creditTot"></td>
                                <td id="returnTot"></td>
                                <td id="sumTot"></td>

                            </tr>
                         </tbody>

                            </table>
                </div>

            </div>
        </section>

     </body>
<script>

    $( document ).ready(function() {




        var mult2 = 0;
        var mult3 = 0;

        var mult4 = 0;
        var mult5 = 0;
               // for each row:
               $("tr.txtMult2").each(function () {
                   // get the values from this row:
                   var $val2 =parseFloat($('.val3', this).html());

                   var $val3 = parseFloat($('.val4', this).html());

                   var $val4 = parseFloat($('.val5', this).html());
                   var $val5 = parseFloat($('.val6', this).html());
                   mult2 += $val2;
                   mult3 += $val3;
                   mult4 += $val4;
                   mult5 += $val5;
               });


        $("#cashTot").html(mult2.toFixed(2));
        $("#creditTot").html(mult3.toFixed(2));
        $("#returnTot").html(mult4.toFixed(2));
        $("#sumTot").html(mult5.toFixed(2));


                });





            </script>
</html>






