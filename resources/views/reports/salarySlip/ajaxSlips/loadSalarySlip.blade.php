@php
    use App\STATIC_DATA_MODEL;
    $company_name = STATIC_DATA_MODEL::$company_name;
    $company_logo = STATIC_DATA_MODEL::$company_logo;
    $company_address = STATIC_DATA_MODEL::$company_address;
    $company_contacts= STATIC_DATA_MODEL::$company_contacts;
@endphp


@php
    $netPrice = 0;
    $returnPrice = 0;

    $dailyBasicSalary = 1600; // Rs.1,600
    $serviceBonusAmount_perYear = 100; // Rs.100
    $employeeDailySalaryAmount = $dailyBasicSalary + ($employeeServiceYearCount * $serviceBonusAmount_perYear); // == Employee's Basic Daily Salary + (Service Year Count x Service Bonus Amount per year)

    // $payment = (float) $vehicleCount * (float) $employeeDailySalaryAmount;
    $payment = (float) $employeeWorkedDay_count * (float) $employeeDailySalaryAmount;
    $netPriceTotal = 0;
    $returnPrice = 0;
@endphp

@foreach ($data as $data1)
    @php
        $cusNet = App\customerInvoices::find($data1->InvoiceId);
        $netInvo = (float) $cusNet->net_price - ( (float) $cusNet->discount + (float) $cusNet->display_discount + (float) $cusNet->special_discount);

        $netIncrement = (float) $netInvo;
        $netPriceTotal += $netIncrement;

        $returnIncrement = (float) $cusNet->return_price;
        $returnPrice += $returnIncrement;
    @endphp
@endforeach

@php
    $invoicesTotalAmount = (float) $netPriceTotal;
    $commission = (float) ($invoicesTotalAmount * 1 / 100);  // apply 1% commission
    $specialSalesCommission = $specialSalesCommission; // calculation in Controller...
    $totalUnpaidCreditBillAmount = $totalUnpaidCreditBillAmount;
    $tot = (float) $payment + (float) $commission + (float) $specialSalesCommission - (float) $totalUnpaidCreditBillAmount;
@endphp

<div class="ibox-content">
    {{-- PRINT BUTTON --}}
    <button type="button" class="btn-block btn btn-dark" style="font-size: 15px; color: #62d146; font-family: 'Roboto', sans-serif; letter-spacing: 1.5px;" onclick="printGeneratedSalarySlip()"><i class="fa fa-print" aria-hidden="true"></i> &nbsp; Print Salary Slip</button>
    {{-- <a href=" {{ url('printSalarySlipNew/' . $sales . '/' . $driver . '/' . $dateFromFormat . '/' . $dateToFormat) }}" target="_blank" id="clickAhrf"><button type="button" class="btn-block btn btn-dark" style="font-size: 15px; color: #62d146; font-family: 'Roboto', sans-serif; letter-spacing: 1.5px;"> <i class="fa fa-print" aria-hidden="true"></i> &nbsp; Print Salary Slip </button></a> --}}

    <br>
    <div class="row" id="">
        <div class="col-sm-3"></div>
        <div class="col-sm-6">
            <center style="font-family: Verdana, Geneva, sans-serif;">
                <h1>{{ $company_name }}</h1>
                <hr style="border-top: 1px dashed black;">
                <label style="font-size: medium">{{ $company_address }}</label><br>
                <label style="font-size: medium">{{ $company_contacts }}</label><br>
                <label style="font-size: medium">K/G/002813</label><br><br>
                <h2><b>Monthly Salary Slip</b></h2>
                <br>

                <table style="font-size: medium; width: 70%; font-family: Verdana, Geneva, sans-serif;">
                    <tbody>
                        <tr style="height: 35px">
                            <td>Employee Name </td>
                            <td> :</td>
                            <td style="padding-left: 20px;"><span id="userName">{{ $userName }}</span></td>
                        </tr>

                        <tr style="height: 35px">
                            <td>Month </td>
                            <td> :</td>
                            <td style="padding-left: 20px;"><span id="monthName">{{ $monthName }}</span></td>
                        </tr>

                        <tr style="height: 35px">
                            <td>Working Days </td>
                            <td> :</td>
                            <td style="padding-left: 20px;"><span id="companyWorkingDay_count">{{ $companyWorkingDay_count }}</span></td>
                        </tr>

                        <tr style="height: 35px">
                            <td>Worked Days </td>
                            <td> :</td>
                            <td style="padding-left: 20px;"><span id="employeeWorkedDay_count">{{ $employeeWorkedDay_count }}</span></td>
                        </tr>

                        {{-- @if ($attendanceBonus > 0.0) --}}
                        <tr style="height: 35px">
                            <td>Attendance Bonus </td>
                            <td> :</td>
                            <td style="padding-left: 20px;"><span id="attendanceBonus">{{ number_format($attendanceBonus, 2, '.', ',') }}</span></td>
                        </tr>
                        {{-- @endif --}}

                        <tr style="height: 35px">
                            <td>Payment </td>
                            <td> :</td>
                            <td style="padding-left: 20px;"><span id="payment">{{ number_format($payment, 2, '.', ',') }}</span></td>
                        </tr>

                        <tr style="height: 35px">
                            <td>Commission </td>
                            <td> :</td>
                            <td style="padding-left: 20px;"><span id="commission">{{ number_format($commission, 2, '.', ',') }}</span></td>
                        </tr>

                        {{-- @if ($specialSalesCommission > 0.0) --}}
                        <tr style="height: 35px">
                            <td>Special Sales Commission </td>
                            <td> :</td>
                            <td style="padding-left: 20px;"><span id="specialSalesCommission">{{ number_format($specialSalesCommission, 2, '.', ',') }}</span></td>
                        </tr>
                        {{-- @endif --}}

                        {{-- @if ($totalUnpaidCreditBillAmount > 0.0) --}}
                        <tr style="height: 35px">
                            <td>Bill Deductions </td>
                            <td> :</td>
                            <td style="padding-left: 20px;"><span id="totalUnpaidCreditBillAmount">{{ number_format($totalUnpaidCreditBillAmount, 2, '.', ',') }}</span></td>
                        </tr>
                        {{-- @endif --}}

                        <tr style="height: 35px">
                            <td><b>Total Payable Salary</b> </td>
                            <td> :</td>
                            <td style="padding-left: 20px; font-weight: bold;">
                                <u style="text-decoration-style: double">
                                    <span id="totalPayableSalary">{{ number_format($tot, 2, '.', ',') }}</span>
                                </u>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </center>
        </div>
        <div class="col-sm-3"></div>
    </div>
</div>

<script>
    // print the Generated Salary slip
    function printGeneratedSalarySlip() {
        var userName = encodeURIComponent($('#userName').html());
        var companyWorkingDay_count = encodeURIComponent($('#companyWorkingDay_count').html());
        var employeeWorkedDay_count = encodeURIComponent($('#employeeWorkedDay_count').html());
        var monthName = encodeURIComponent($('#monthName').html());
        var attendanceBonus = encodeURIComponent($('#attendanceBonus').html());
        var payment = encodeURIComponent($('#payment').html());
        var commission = encodeURIComponent($('#commission').html());
        var specialSalesCommission = encodeURIComponent($('#specialSalesCommission').html());
        var totalUnpaidCreditBillAmount = encodeURIComponent($('#totalUnpaidCreditBillAmount').html());
        var totalPayableSalary = encodeURIComponent($('#totalPayableSalary').html());

        // Construct the URL with parameters
        var url = "{{ url('printGeneratedSalarySlip') }}/"
            + userName + "/"
            + companyWorkingDay_count + "/"
            + employeeWorkedDay_count + "/"
            + monthName + "/"
            + attendanceBonus + "/"
            + payment + "/"
            + commission + "/"
            + specialSalesCommission + "/"
            + totalUnpaidCreditBillAmount + "/"
            + totalPayableSalary;

        // Open the URL in a new tab
        window.open(url, '_blank');
    }
</script>
