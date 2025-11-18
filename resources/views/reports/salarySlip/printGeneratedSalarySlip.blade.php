@php
    use App\STATIC_DATA_MODEL;
    $company_name = STATIC_DATA_MODEL::$company_name;
    $company_logo = STATIC_DATA_MODEL::$company_logo;
    $company_address = STATIC_DATA_MODEL::$company_address;
    $company_contacts= STATIC_DATA_MODEL::$company_contacts;
@endphp

<html>
<head>
    <meta charset="utf-8">
    <title>{{ $company_name }}</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta content="Preview page of MI7 Admin Theme #2 for statistics, charts, recent events and reports" name="description" />
    <meta content="" name="author" />
    <link rel="icon" type="image/png" href="/../../img/favicon.ico"/>
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
            size: A5
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
            padding: 1cm;
        }

        @media print {
            .avoid {
                page-break-inside: avoid;
            }

            .sheet {
                padding: 1cm;
            }

            page[size="A5"][layout="portrait"] {
                width: 14.8cm;
                height: 21.0cm;
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
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid #aaa;
        }

        table.table-bordered>tfoot>tr>td {
            border: 1px solid #aaa;
        }
    </style>
</head>

<body class="A5" style="font-size: small;text-align: justify">
    <section class="sheet" style="height: auto;width: 14.8cm">
        <input type="button" onclick="window.print();" class="form-control btn btn-success noPrint" value="Click Here To Print This Page">
        <div class="container-fluid">
            <center style="font-family: Verdana, Geneva, sans-serif;">
                <h1>{{ $company_name }}</h1>
                <hr style="border-top: 1px dashed black;">
                <label style="font-size: medium">{{ $company_address }}</label><br><br>
                <label style="font-size: medium">{{ $company_contacts }}</label><br><br>
                <label style="font-size: medium">K/G/002813</label><br><br>
                <h2><b>Monthly Salary Slip</b></h2>
                <br>
            </center>

            <table style="font-size: medium; width: 100%; font-family: Verdana, Geneva, sans-serif;">
                <tbody>
                    <tr style="height: 35px">
                        <td>Employee Name </td>
                        <td> :</td>
                        <td style="padding-left: 20px;">{{ $userName }}</td>
                    </tr>

                    <tr style="height: 35px">
                        <td>Month </td>
                        <td> :</td>
                        <td style="padding-left: 20px;">{{ $monthName }}</td>
                    </tr>

                    <tr style="height: 35px">
                        <td>Working Days </td>
                        <td> :</td>
                        <td style="padding-left: 20px;">{{ $companyWorkingDay_count }}</td>
                    </tr>

                    <tr style="height: 35px">
                        <td>Worked Days </td>
                        <td> :</td>
                        <td style="padding-left: 20px;">{{ $employeeWorkedDay_count }}</td>
                    </tr>

                    <tr style="height: 35px">
                        <td>Attendance Bonus </td>
                        <td> :</td>
                        <td style="padding-left: 20px;">{{ number_format($attendanceBonus, 2, '.', ',') }}</td>
                    </tr>

                    <tr style="height: 35px">
                        <td>Payment </td>
                        <td> :</td>
                        <td style="padding-left: 20px;">{{ number_format($payment, 2, '.', ',') }}</td>
                    </tr>

                    <tr style="height: 35px">
                        <td>Commission </td>
                        <td> :</td>
                        <td style="padding-left: 20px;">{{ number_format($commission, 2, '.', ',') }}</td>
                    </tr>

                    <tr style="height: 35px">
                        <td>Special Sales Commission </td>
                        <td> :</td>
                        <td style="padding-left: 20px;">{{ number_format($specialSalesCommission, 2, '.', ',') }}</td>
                    </tr>

                    <tr style="height: 35px">
                        <td>Bill Deductions </td>
                        <td> :</td>
                        <td style="padding-left: 20px;">{{ number_format($totalUnpaidCreditBillAmount, 2, '.', ',') }}</td>
                    </tr>

                    <tr style="height: 35px">
                        <td>Total Payable Salary </td>
                        <td> :</td>
                        <td style="padding-left: 20px;"><u style="text-decoration-style: double">{{ number_format($totalPayableSalary, 2, '.', ',') }}</u></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</body>
</html>
