<?php

namespace App\Http\Controllers;

use App\Driver;
use App\SaleRep;
use App\Customer;
use Carbon\Carbon;
use App\STATIC_DATA_MODEL;
use App\CommissionSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalarySlipController extends Controller
{
    public function adminGenerateSalarySlips()
    {
        $salesRep = SaleRep::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        $drivers = Driver::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        return view('reports.salarySlip.generateSalarySlips', compact('salesRep', 'drivers'));
    }


    // get Total Unpaid Amount of all the pending Credit Bills  by  User(sale rep)
    private function getTotalUnpaidCreditBillAmount($salesReps_userID, $dateFrom, $dateTo)
    {
        $dateFromFormat = (is_null($dateFrom) ? null : date("Y-m-d", strtotime($dateFrom)));
        $dateToFormat = (is_null($dateTo) ? null : date("Y-m-d", strtotime($dateTo)));

        $query = DB::table('dm_customer_invoice')
            ->select('dm_customer_invoice.*')
            ->where('dm_customer_invoice.invoice_type', STATIC_DATA_MODEL::$credit)
            ->where('dm_customer_invoice.invoice_status', STATIC_DATA_MODEL::$invoicePending);
        if ($salesReps_userID !== null) {
            $query->where('dm_customer_invoice.created_by', $salesReps_userID);
        }

        $creditInvoice_list = $query->get();


        // calculations
        $bill_age_limit = 45;
        $totalUnpaidCreditBillAmount = 0.0;

        foreach ($creditInvoice_list as $creditInvoice) {
            $netPrice = (float) $creditInvoice->net_price - ((float) $creditInvoice->discount + (float) $creditInvoice->display_discount + (float) $creditInvoice->special_discount);
            $paidAmount = $creditInvoice->total_amout_paid;
            $invoiceDate = $creditInvoice->created_at;
            $todayDate = Carbon::now();
            $date1 = date('Y-m-d', strtotime($invoiceDate));
            $date2 = date('Y-m-d', strtotime($todayDate));
            $difference = strtotime($date2) - strtotime($date1);
            $invoiceAge = abs(round($difference / 86400)); // 86,400 seconds in a day. dividing the difference by this number converts seconds to days

            // get bill age limit exceeding credit bills for calculating total due
            if ($invoiceAge > $bill_age_limit) {
                $totalUnpaidCreditBillAmount += ((float) $netPrice - (float) $paidAmount);
            }
        }

        return $totalUnpaidCreditBillAmount;
    }


    // NEW ***** GENERATE SALARY SLIP ************************************************************************
    public function generateSalarySlip(Request $request)
    {
        $sales = $request->sales;
        $driver = $request->driver;
        $userName = '';
        $user_created_date = '';
        // get "Apply salary deductions for credit bills" checkbox input as a boolean
        $apply_salary_deductions = $request->input('deductCreditBills_check') === 'true';


        // echo "request->sales == " . $request->sales . "<br>" .
        //      "request->driver == " . $request->driver . "<br>" .
        //      "- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -<br>" .
        //      "request->dateFrom == " . $request->dateFrom . "<br>" .
        //      "request->dateTo == " . $request->dateTo . "<br>" .
        //      "- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -<br>";

        // get user's name by User Type
        if ($sales !== '0') {
            $salesObj = SaleRep::find($request->sales);
            $userName = $salesObj->sales_rep_name;
            $user_created_date = $salesObj->created_at;
        } elseif ($driver !== '0') {
            $driverObj = Driver::find($request->driver);
            $userName = $driverObj->driver_name;
            $user_created_date = $driverObj->created_at;
        }

        // determine the Date range by User Type
        if ($sales !== '0') { // Sales Rep
            $dateFromConvert = date('Y-m-d', strtotime($request->dateFrom . ' - 1 month'));
            $dateToConvert = date("Y-m-t", strtotime($dateFromConvert));
        } else { // Driver
            $dateFromConvert = $request->dateFrom;
            $dateToConvert = $request->dateTo;
        }

        // echo "dateFrom Convert == " . $dateFromConvert . "<br>" .
        //      "dateTo Convert == " . $dateToConvert . "<br>" .
        //      "- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -<br>";

        $dateFromFormat = date("Y-m-d", strtotime($dateFromConvert));
        $dateToFormat = date("Y-m-d", strtotime($dateToConvert));

        // echo "dateFrom Format == " . $dateFromFormat . "<br>" .
        //      "dateTo Format == " . $dateToFormat . "<br>" .
        //      "- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -<br>";


        // get Calculation Month Name for the report
        $date = Carbon::parse($dateFromFormat)->startOfDay();
        $monthName = $date->format('F');

        $datefromDelivery = date("Y-m-d", strtotime($request->dateFrom));
        $dateToDelivery = date("Y-m-d", strtotime($request->dateTo));

        // echo "dateFrom Delivery == " . $datefromDelivery . "<br>" .
        //      "dateTo Delivery == " . $dateToDelivery . "<br>" .
        //      "- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -<br>";


        // INVOICE List by filters (used for Commission calculations) ======================================================================================
        $mainQuery = DB::table('dm_customer_invoice')
            // ->select('dm_customer_invoice.id As InvoiceId')
            ->select('dm_customer_invoice.id AS InvoiceId',
                    'dm_customer_invoice.invoice_number AS InvoNum',
                    'dm_delivery_vehicle.id AS deliVehi_ID',
                    DB::raw('DATE(dm_delivery_vehicle.created_at) AS deliVehi_createdAt'),
                    DB::raw('dm_customer_invoice.net_price - (dm_customer_invoice.discount + dm_customer_invoice.display_discount + dm_customer_invoice.special_discount) AS invoiceAmount')
                    )
            ->distinct()
            ->join('dm_customer_invoice_has_stock_batch', 'dm_customer_invoice_has_stock_batch.dm_customer_invoice_id', '=', 'dm_customer_invoice.id')
            ->join('dm_delivery_vehicle_has_stock_batch', function ($join) {
                $join->on('dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id', '=', 'dm_customer_invoice_has_stock_batch.dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id');
            })
            ->join('dm_delivery_vehicle', 'dm_delivery_vehicle.id', '=', 'dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id')
            ->where('dm_customer_invoice.invoice_status', '!=', 3); // drop removed invoices
        if ($driver !== '0') {
            $mainQuery->where('dm_delivery_vehicle.vm_drivers_id', $driver);
        }
        if ($sales !== '0') {
            $mainQuery->where('dm_delivery_vehicle.vm_sales_reps_id', $sales);
        }
        $mainQuery->whereDate('dm_customer_invoice.created_at', '>=', $dateFromFormat)
                  ->whereDate('dm_customer_invoice.created_at', '<=', $dateToFormat);

        $data = $mainQuery->groupBy('dm_customer_invoice.id')->get();
        // ================================================================================================================================================

        // echo "invoice Data collection ==> " . "<br>" . $data . "<br>" .
        //      "- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -<br>";
        // echo "invoice Data SIZE ==> " . count($data) . "<br>" .
        //      "- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -<br>";


        // ----------- SALARY ADDITIONS -----------
        // * * * * * Cal Employee Attendance Bonus * * * * *
        $attendanceBonus = 1600;

        // get Company Working Days Count --------------------------------------------------------------------
        // OLD ~~  by Lorry creation days count
        // $ComWDC_Query = DB::table('dm_delivery_vehicle')
        //     ->select('dm_delivery_vehicle.id As DV_ID', 'dm_delivery_vehicle.created_at')
        //     ->where('dm_delivery_vehicle.status', '=', STATIC_DATA_MODEL::$deliveryCompleted)
        //     ->whereDate('dm_delivery_vehicle.created_at', '>=', $datefromDelivery)
        //     ->whereDate('dm_delivery_vehicle.created_at', '<=', $dateToDelivery)
        //     ->groupBy(DB::raw('DATE(dm_delivery_vehicle.created_at)'));

        // $companyWorkingDay_count = $ComWDC_Query->get()->count();

        // NEW ~~ by Invoice creation days count
        $ComWDC_Query = DB::table('dm_customer_invoice')
            ->select('dm_customer_invoice.id As CMI_ID', 'dm_customer_invoice.created_at')
            ->where('dm_customer_invoice.invoice_status', '!=', STATIC_DATA_MODEL::$invoiceDeleted)
            ->whereDate('dm_customer_invoice.created_at', '>=', $datefromDelivery)
            ->whereDate('dm_customer_invoice.created_at', '<=', $dateToDelivery)
            ->groupBy(DB::raw('DATE(dm_customer_invoice.created_at)'));

        $companyWorkingDay_count = $ComWDC_Query->get()->count();

        // get Employee Worked Days Count --------------------------------------------------------------------
        // OLD ~~  by Lorry creation days count including Rep or Driver
        // $EmpWDC_Query = DB::table('dm_delivery_vehicle')
        //     ->select('dm_delivery_vehicle.id As DV_ID', 'dm_delivery_vehicle.created_at')
        //     ->where('dm_delivery_vehicle.status', '=', STATIC_DATA_MODEL::$deliveryCompleted)
        //     ->whereDate('dm_delivery_vehicle.created_at', '>=', $datefromDelivery)
        //     ->whereDate('dm_delivery_vehicle.created_at', '<=', $dateToDelivery);
        //     if ($sales !== '0') { // for Sales Rep
        //         $EmpWDC_Query->where('dm_delivery_vehicle.vm_sales_reps_id', $request->sales);
        //     }
        //     if ($driver !== '0') { // for Driver
        //         $EmpWDC_Query->where('dm_delivery_vehicle.vm_drivers_id', $request->driver);
        //     }
        // $EmpWDC_Query->groupBy(DB::raw('DATE(dm_delivery_vehicle.created_at)'));

        // $employeeWorkedDay_count = $EmpWDC_Query->get()->count();

        // NEW ~~  by Invoice creation day count including Rep or Driver
        $EmpWDC_Query = DB::table('dm_customer_invoice')
            ->select('dm_customer_invoice.id As CMI_ID', 'dm_delivery_vehicle.id As DV_ID', 'dm_customer_invoice.created_at')
            ->join('dm_customer_invoice_has_stock_batch', 'dm_customer_invoice_has_stock_batch.dm_customer_invoice_id', '=', 'dm_customer_invoice.id')
            ->join('dm_delivery_vehicle_has_stock_batch', function ($join) {
                $join->on('dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id', '=', 'dm_customer_invoice_has_stock_batch.dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id');
            })
            ->join('dm_delivery_vehicle', 'dm_delivery_vehicle.id', '=', 'dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id')
            ->where('dm_customer_invoice.invoice_status', '!=', STATIC_DATA_MODEL::$invoiceDeleted)
            ->whereDate('dm_customer_invoice.created_at', '>=', $datefromDelivery)
            ->whereDate('dm_customer_invoice.created_at', '<=', $dateToDelivery);


            if ($sales !== '0') { // for Sales Rep
                $EmpWDC_Query->where('dm_delivery_vehicle.vm_sales_reps_id', $request->sales);
            }
            if ($driver !== '0') { // for Driver
                $EmpWDC_Query->where('dm_delivery_vehicle.vm_drivers_id', $request->driver);
            }
        $EmpWDC_Query->groupBy(DB::raw('DATE(dm_customer_invoice.created_at)'));

        $employeeWorkedDay_count = $EmpWDC_Query->get()->count();

        // compare Employee's worked days with Company working days
        if ($companyWorkingDay_count != $employeeWorkedDay_count) {
            $attendanceBonus = 0.0;
        }

        // echo "companyWorkingDay count ==> " . $companyWorkingDay_count . "<br>" .
        //      "employeeWorkedDay count ==> " . $employeeWorkedDay_count . "<br>" .
        //      "Attendance Bonus == " . $attendanceBonus . "<br>" .
        //      "- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -<br>";



        // * * * * --------- Get Employee's service time since the enrolement -----------------* * * *
        $employeeServiceDayCount = 0;
        // Check if user_created_date is available and not empty
        if (!empty($user_created_date)) {
            // Ensure $user_created_date is a Carbon instance for easy date manipulation
            $registrationDate = Carbon::parse($user_created_date)->startOfDay();
            $today = Carbon::today();
            $employeeServiceDayCount = $registrationDate->diffInDays($today);
        }
        $employeeServiceYearCount = (int) ($employeeServiceDayCount / 365);
        // dump("employeeServiceDayCount --> " . $employeeServiceDayCount);
        // dump("Employee Service Years --> " . $employeeServiceYearCount);
        // dump("Employee Service Bonus --> " . $employeeServiceYearBonus);
        // * * * * ------------------------------------------------------------------------------* * * *



        // * * * * * Cal Commission * * * * *
        // ~ this part is in Blade file


        // * * * * * cal Special Sales Commission * * * * *
        $specialSalesCommission = 0.00;
        $carb_dateFrom = Carbon::parse($dateFromFormat)->startOfDay();
        $carb_dateTo = Carbon::parse($dateToFormat)->endOfDay();
        // $carb_dateFrom = Carbon::createFromFormat('Y-m-d', $dateFromFormat);
        // $carb_dateTo = Carbon::createFromFormat('Y-m-d', $dateToFormat);

        // get day count
        $dayCount = $carb_dateTo->diffInDays($carb_dateFrom);

        // echo "carb_dateFrom ==> " . $carb_dateFrom . "<br>" .
        //      "carb_dateTo ==> " . $carb_dateTo . "<br>" .
        //      "- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -<br>" .
        //      "Special Sales Commission == " . $specialSalesCommission . "<br>" .
        //      "Day Count == " . $dayCount . "<br>" .
        //     //  "Vehicle Count == " . $vehicleCount. "<br>" .
        //      "- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -<br>";


        // initialize an array to hold the Delivery Vehicle ids and sum of Invoice Amounts
        $vehicleSums_arr = [];

        // Loop through the data and sum the invoice amounts for each delivery vehicle
        foreach ($data as $invoice) {
            $vehicleId = $invoice->deliVehi_ID; // Get the delivery vehicle ID
            $invoiceAmount = $invoice->invoiceAmount; // Get the invoice amount

            // Check if the vehicle ID is already in the array
            if (!isset($vehicleSums_arr[$vehicleId])) {
                $vehicleSums_arr[$vehicleId] = 0; // Initialize if not set
            }

            // Sum the invoice amount
            $vehicleSums_arr[$vehicleId] += $invoiceAmount;
        }


        // // print the summed results
        // foreach ($vehicleSums_arr as $vehicleId => $totalAmount) {
        //     echo "Delivery Vehicle ID: {$vehicleId} - Total Invoice Amount: {$totalAmount}<br>";
        // }
        // echo "- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -<br>";


        // get active Commission Settings list
        $cs_query = DB::table('st_commission_settings')
            ->select('id', 'min_sales_amount', 'max_sales_amount', 'commission_rate')
            ->where('is_active', STATIC_DATA_MODEL::$Active);
        $commissionSettings_List = $cs_query->get();


        // calculate Special Commission - using dynamic settings
        foreach ($vehicleSums_arr as $vehicleId => $totalAmount) {
            foreach ($commissionSettings_List as $setting) {
                // check if the total amount falls within the min and max range of the commission setting
                if ($totalAmount >= $setting->min_sales_amount && $totalAmount <= $setting->max_sales_amount) {
                    $commission = $totalAmount * ($setting->commission_rate / 100);
                    $specialSalesCommission += $commission;
                    break; // Exit the loop after finding the applicable commission setting
                }
            }
        }


        // calculate Special Commission - using hard code
        // foreach ($vehicleSums_arr as $vehicleId => $totalAmount) {
        //     // Check if totalAmount is between 127,000 and 200,000
        //     if ($totalAmount >= 127000 && $totalAmount < 200000) {
        //         $specialSalesCommission += $totalAmount * 0.01; // 1% of the total amount
        //     }

        //     // Check if totalAmount is between 200,000 and 1,000,000
        //     if ($totalAmount >= 200000 && $totalAmount < 1000000) {
        //         $specialSalesCommission += $totalAmount * 0.02; // 2% of the total amount
        //     }
        // }


        // // Display the results
        // echo "Special Sales Commission: {$specialSalesCommission}<br>" . "- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -<br>";


        // ----------- SALARY DEDUCTIONS -----------
        // get Total Unpaid Amount of all the pending Credit Bills  by  Sales Rep
        $totalUnpaidCreditBillAmount = 0.0;

        if ($sales !== '0' && $apply_salary_deductions) { // when the checkbox is checked
            $totalUnpaidCreditBillAmount = $this->getTotalUnpaidCreditBillAmount($salesObj->um_user_id, $request->dateFrom, $request->dateTo);
        }


        return view('reports.salarySlip.ajaxSlips.loadSalarySlip', compact(
            'sales',
            'driver',
            'dateFromFormat',
            'dateToFormat',
            'data',
            'userName',
            'companyWorkingDay_count',
            'employeeWorkedDay_count',
            'monthName',
            'attendanceBonus',
            'specialSalesCommission',
            'totalUnpaidCreditBillAmount',
            'employeeServiceYearCount'
        ));
    }


    // NEW ***** PRINT SALARY SLIP **************************************************************************
    public function printGeneratedSalarySlip($userName, $companyWorkingDay_count, $employeeWorkedDay_count, $monthName, $attendanceBonus, $payment, $commission, $specialSalesCommission, $totalUnpaidCreditBillAmount, $totalPayableSalary)
    {
        // Sanitize input values
        $attendanceBonus = (float) str_replace(',', '', $attendanceBonus);
        $payment = (float) str_replace(',', '', $payment);
        $commission = (float) str_replace(',', '', $commission);
        $specialSalesCommission = (float) str_replace(',', '', $specialSalesCommission);
        $totalUnpaidCreditBillAmount = (float) str_replace(',', '', $totalUnpaidCreditBillAmount);
        $totalPayableSalary = (float) str_replace(',', '', $totalPayableSalary);

        // Return the view with the retrieved data
        return view('reports.salarySlip.printGeneratedSalarySlip', compact(
            'userName',
            'companyWorkingDay_count',
            'employeeWorkedDay_count',
            'monthName',
            'attendanceBonus',
            'payment',
            'commission',
            'specialSalesCommission',
            'totalUnpaidCreditBillAmount',
            'totalPayableSalary'
        ));
    }




// ============================================================================================================================================
    // OLD **** GENERATE SALARY SLIP ******************************************************************
    public function generateSalarySlip_OLD(Request $request)
    {
        $sales = $request->sales;
        $driver = $request->driver;
        $userName = '';

        if ($sales !== '0') {
            $salesObj = SaleRep::find($request->sales);
            $userName = $salesObj->sales_rep_name;
        }

        if ($driver !== '0') {
            $driverObj = Driver::find($request->driver);
            $userName = $driverObj->driver_name;
        }
        if ($sales !== '0') {
            $dateFromConvert = date('Y-m-d', strtotime($request->dateFrom . ' - 1 month'));
            //$dateToConvert = date('Y-m-d', strtotime($request->dateTo . ' - 1 month'));
            //  dd($dateFromConvert);
            $dateToConvert = date("Y-m-t", strtotime($dateFromConvert));
        } else {
            $dateToConvert = $request->dateTo;
            $dateFromConvert = $request->dateFrom;
        }

        $date = Carbon::createFromFormat('m/d/Y', $request->dateFrom);
        $monthName = $date->format('F');

        $dateFrom = $dateFromConvert;
        $dateTo = $dateToConvert;

// dump("request-dateFrom -->> ".$request->dateFrom);
// dump("request-dateTo -->> ".$request->dateTo);

        $dateFromFormat = date("Y-m-d", strtotime($dateFrom));
        $dateToFormat = date("Y-m-d", strtotime($dateTo));

        $datefromDelivery = date("Y-m-d", strtotime($request->dateFrom));
        $dateToDelivery = date("Y-m-d", strtotime($request->dateTo));


        // USER'S VEHICLE COUNT (used to get Working Days count) ============================================================================================
        $query2 = DB::table('dm_customer_invoice')
            ->select('dm_customer_invoice.id As InvoiceId')
            ->distinct('dm_customer_invoice.id')
            ->join('dm_customer_invoice_has_stock_batch', 'dm_customer_invoice_has_stock_batch.dm_customer_invoice_id', '=', 'dm_customer_invoice.id')
            ->join("dm_delivery_vehicle_has_stock_batch", function ($join) {
                $join->on("dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id", "=", "dm_customer_invoice_has_stock_batch.dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id");
            })
            ->join('dm_delivery_vehicle', 'dm_delivery_vehicle.id', '=', 'dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id')
            ->where('dm_customer_invoice.invoice_status', '!=', 3); // drop removed invoices

        if ($request->sales !== '0') {
            $query2->where('dm_delivery_vehicle.vm_sales_reps_id', $request->sales);
        }

        if ($request->driver !== '0') {
            $query2->where('dm_delivery_vehicle.vm_drivers_id', $request->driver);
        }

        if ($request->dateFrom !== null && $request->dateTo == null) {
            $query2->whereDate('dm_customer_invoice.created_at', $datefromDelivery);
        }

        if ($request->dateTo !== null && $request->dateFrom == null) {
            $query2->whereDate('dm_customer_invoice.created_at', $dateToDelivery);
        }

        if ($request->dateTo !== null && $request->dateFrom != null) {
            $query2->whereDate('dm_customer_invoice.created_at', '>=', $datefromDelivery);
            $query2->whereDate('dm_customer_invoice.created_at', '<=', $dateToDelivery);
        }

        $data2 = $query2->groupBy(DB::raw("DATE_FORMAT(dm_customer_invoice.created_at, '%Y-%m-%d')"))->get();
        $vehicleCount = count($data2);


        // INVOICE List by filters (used for Commission calculations) ======================================================================================
        $query = DB::table('dm_customer_invoice')
            ->select('dm_customer_invoice.id As InvoiceId')
            ->distinct('dm_customer_invoice.id')
            ->join('dm_customer_invoice_has_stock_batch', 'dm_customer_invoice_has_stock_batch.dm_customer_invoice_id', '=', 'dm_customer_invoice.id')
            ->join("dm_delivery_vehicle_has_stock_batch", function ($join) {
                $join->on("dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id", "=", "dm_customer_invoice_has_stock_batch.dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id");
            })
            ->join('dm_delivery_vehicle', 'dm_delivery_vehicle.id', '=', 'dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id')
            ->where('dm_customer_invoice.invoice_status', '!=', 3); // drop removed invoices

        if ($driver !== '0') {
            $query->where('dm_delivery_vehicle.vm_drivers_id', $driver);
        }

        if ($sales !== '0') {
            $query->where('dm_delivery_vehicle.vm_sales_reps_id', $sales);
        }

        $query->whereDate('dm_customer_invoice.created_at', '>=', $dateFromFormat);
        $query->whereDate('dm_customer_invoice.created_at', '<=', $dateToFormat);
        $data = $query->groupBy('dm_customer_invoice.id')->get();


        // calculate Special Sales Commission -------------------------------------
        $specialSalesCommission = 0.00;
        $carb_dateFrom = Carbon::createFromFormat('Y-m-d', $dateFromFormat);
        $carb_dateTo = Carbon::createFromFormat('Y-m-d', $dateToFormat);
        // Calculate the difference in days
        $dayCount = $carb_dateTo->diffInDays($carb_dateFrom);

// dump("carb_dateFrom --> ".$carb_dateFrom);
// dump("carb_dateTo --> ".$carb_dateTo);
// dump("dayCount -- ".$dayCount);

        $x = 1;
        while ($x <= $dayCount) {
            // dump("x IN -- ".$x);
            foreach ($data as $invoice) {
                $invoicePriceTotal = DB::table('dm_customer_invoice')
                    ->whereDate('created_at', $carb_dateFrom)
                    ->where('id', $invoice->InvoiceId)
                    ->sum('invoice_price');
                // check sales of the day is more than LKR 200,000/=
                if ($invoicePriceTotal > 200000) {
                    // Calculate 1% commission from Total sales in that Day
                    $commission = ($invoicePriceTotal / 100) * 1;
                    $specialSalesCommission += $commission;
                }
            }
            // $date->addDay(); // Increment Date
            $x++;
        }
// dump("X -- ".$x);
// dd($specialSalesCommission);

        return view('reports.salarySlip.ajaxSlips.loadSalarySlip', compact('sales', 'driver', 'dateFromFormat', 'dateToFormat', 'data', 'userName', 'vehicleCount', 'monthName', 'specialSalesCommission'));
    }



    // OLD **** PRINT GENERATED SALARY SLIP ******************************************************************
    public function printSalarySlip($sales, $driver, $dateFromFormat, $dateToFormat)
    {
        $userName = '';

        if ($sales !== '0') {
            $salesObj = SaleRep::find($sales);
            $userName = $salesObj->sales_rep_name;
        }

        if ($driver !== '0') {
            $driverObj = Driver::find($driver);
            $userName = $driverObj->driver_name;
        }

        // $date = Carbon::createFromFormat('m/d/Y', $dateFromFormat);
        $monthName = date('F', strtotime($dateFromFormat));
        $deliveryVehicle = DB::table('dm_delivery_vehicle')
            ->select('dm_delivery_vehicle.id As ID');

        if ($sales !== '0') {
            $deliveryVehicle->where('vm_sales_reps_id', $sales);
        }

        if ($driver !== '0') {
            $deliveryVehicle->where('vm_drivers_id', $driver);
        }

        $deliveryVehicle->whereDate('created_at', '>=', $dateFromFormat);
        $deliveryVehicle->whereDate('created_at', '<=', $dateToFormat);
        $vehicleCount = $deliveryVehicle->count();

        $query = DB::table('dm_customer_invoice')
            ->select('dm_customer_invoice.id As InvoiceId')
            ->distinct('dm_customer_invoice.id')
            ->join('dm_customer_invoice_has_stock_batch', 'dm_customer_invoice_has_stock_batch.dm_customer_invoice_id', '=', 'dm_customer_invoice.id')
            ->join("dm_delivery_vehicle_has_stock_batch", function ($join) {
                $join->on("dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id", "=", "dm_customer_invoice_has_stock_batch.dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id");
            })
            ->join('dm_delivery_vehicle', 'dm_delivery_vehicle.id', '=', 'dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id')
            ->where('dm_customer_invoice.invoice_status', '!=', 3); // drop removed invoices

        if ($driver !== '0') {
            $query->where('dm_delivery_vehicle.vm_drivers_id', $driver);
        }

        if ($sales !== '0') {
            $query->where('dm_delivery_vehicle.vm_sales_reps_id', $sales);
        }

        $query->whereDate('dm_customer_invoice.created_at', '>=', $dateFromFormat);
        $query->whereDate('dm_customer_invoice.created_at', '<=', $dateToFormat);
        $data = $query->groupBy('dm_customer_invoice.id')->get();


        // calculate Special Sales Commission -------------------------------------
        $specialSalesCommission = 0.00;
        $carb_dateFrom = Carbon::createFromFormat('Y-m-d', $dateFromFormat);
        $carb_dateTo = Carbon::createFromFormat('Y-m-d', $dateToFormat);
        // Calculate the difference in days
        $dayCount = $carb_dateTo->diffInDays($carb_dateFrom);

        $x = 1;
        while ($x <= $dayCount) {
            // dump("x IN -- ".$x);
            foreach ($data as $invoice) {
                $invoicePriceTotal = DB::table('dm_customer_invoice')
                    ->whereDate('created_at', $carb_dateFrom)
                    ->where('id', $invoice->InvoiceId)
                    ->sum('invoice_price');
                // check sales of the day is more than LKR 200,000/=
                if ($invoicePriceTotal > 200000) {
                    // Calculate 1% commission from Total sales in that Day
                    $commission = ($invoicePriceTotal / 100) * 1;
                    $specialSalesCommission += $commission;
                }
            }
            // $date->addDay(); // Increment Date
            $x++;
        }

        return view('reports.salarySlip.generateSalarySlipPrint', compact('data', 'userName', 'vehicleCount', 'monthName', 'specialSalesCommission'));
    }

}
