@php


$privilageId = \DB::table('pm_interfaces')
->select('pm_interfaces.id AS pageId','pm_interface_topic.id AS grupId')
->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
->where('pm_interfaces.path','adminCreateInvoice')
->first();


@endphp


@extends('layout', ['pageId' => $privilageId->pageId ,'grupId' => $privilageId->grupId ])

@section('content')

<style>
    @page {
        /* size: 14.8cm 21cm; */
        /* margin: 5mm 5mm 5mm 5mm; */
        /* change the margins as you want them to be. */
    }

    .smallText {
        font-size: small;
    }

    body {
        background-color: #CCC;
        font-size: small;
    }

    /* .table-condensed>thead>tr>th,
    .table-condensed>tbody>tr>th,
    .table-condensed>tfoot>tr>th,
    .table-condensed>thead>tr>td,
    .table-condensed>tbody>tr>td,
    .table-condensed>tfoot>tr>td {
        padding: 3px;
    } */

    .sheet {
        padding: 5mm;
    }

    .A5 {
        width: 14.8cm;
        height: 21cm;
    }

    @media print {
        .avoid {
            page-break-inside: avoid;
        }

        .sheet {

            width: 14.8cm;
            height: 21cm;

        }
        .img-fluid{
            width: 100%;
        }

        .A5{
            width:80%;
            height: auto;
        }

        /* page[size="A5"][layout="portrait"] {
            width: 14.8cm;
            height: 21cm;
        } */
        .coloured{
            background-color: sienna !important;
		box-shadow:  inset 0 0 0 1000px #a0522d !important; /* workaround for IE 11*/
        }
        .noPrint {
            display: none;
        }

        .table{
            border:2px solid sienna;
        }
        table.table-bordered{
    border:1px solid sienna;
  }
table.table-bordered > thead > tr > th{
    border:1px solid sienna;
}
table.table-bordered > tbody > tr > td{
    border:1px solid sienna;
}
</style>


<body>
    <section class="sheet A5">

        <div class="container-fluid">
            <div class="row noPrint">
                <div class="col-12">
                    <input type="button" onclick="window.print();" class="btn-block btn btn-success"
                        value="Click Here To Print This Page">
                </div>
            </div>
            <div class="row">
                <div class="col-12" style="border: 3px solid #a0522d ">

                    <div class="row">
                        <div class="col-12" style="border: 1px solid #a0522d ">
                            <center><img class="img-fluid" src="img/logo.png"></center>
                        </div>
                    </div>

                    <div class="row coloured" style="color: white;background-color: sienna;margin-bottom: 5px;">
                        <div class="col-4" >
                            <h5>Batuwaththa, Halamada / </h5>
                        </div>
                        <div class="col-8" style="color: orange;">
                            <h5>Contact No - 0702243051 / 0372243051</h5>
                        </div>
                    </div>



                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td><label style="font-weight: bold">Customer :</label><label>Budhhika Dasanayaka</label></td>
                                            <td><label style="font-weight: bold">Invoice No :</label><label>1234</label></td>
                                            <td><label style="font-weight: bold">Date :</label><label>2019.07.07</label>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                                <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th># </th>
                                                    <th>Product</th>
                                                    <th> Quantity</th>
                                                    <th>Return Qty</th>
                                                    <th>Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>Fruit Cake</td>
                                                    <td>10</td>
                                                    <td>1</td>
                                                    <td>2250.00</td>
                                                </tr>
                                                <tr>
                                                    <td>1</td>
                                                    <td>Butter Cake</td>
                                                    <td>5</td>
                                                    <td>2</td>
                                                    <td>300.00</td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td style="text-align: center;font-weight: bold">Total</td>
                                                    <td style="font-weight: bold">2550.00</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>



                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>
</body>






@endsection


@section('footer')



@endsection
