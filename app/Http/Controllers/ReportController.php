<?php

namespace App\Http\Controllers;

use App\Driver;
use App\Routes;
use App\SaleRep;
use App\Customer;
use App\Vehicles;
use App\SubCategory;
use App\STATIC_DATA_MODEL;
use App\invoicePayments;
use App\customerInvoices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function adminStockReportIndex(Request $request)
    {
        $stock = SubCategory::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        return view('reports.StockReprts.stockReport', compact('stock'));
    }

    public function getStockReport(Request $request)
    {

        if ($request->ajax()) {
            $query = DB::table('pm_product_sub_category')
                ->select('pm_product_sub_category.*', 'pm_stock_batch.*')
                ->join('pm_stock_batch', 'pm_stock_batch.pm_product_sub_category_id', '=', 'pm_product_sub_category.id')
                ->where('pm_product_sub_category.id', $request->product);

            $dateFrom = $request->dateFrom;
            $dateTo = $request->dateTo;

            $dateFromFormat = date("Y-m-d", strtotime($dateFrom));
            $dateToFormat = date("Y-m-d", strtotime($dateTo));

            // if ($request->dateFrom !== NULL) {

            //     $query->whereDate('pm_product_sub_category.created_at','>=',$dateFromFormat);
            // }

            // if ($request->dateTo !== NULL) {

            //     $query->whereDate('pm_product_sub_category.created_at','<=', $dateToFormat );

            // }

            if ($request->dateFrom !== null && $request->dateTo == null) {
                $query->whereDate('pm_product_sub_category.created_at', $dateFromFormat);
            }
            if ($request->dateTo !== null && $request->dateFrom == null) {
                $query->whereDate('pm_product_sub_category.created_at', $dateToFormat);
            }
            if ($request->dateTo !== null && $request->dateFrom != null) {
                $query->whereDate('pm_product_sub_category.created_at', '>=', $dateFromFormat);
                $query->whereDate('pm_product_sub_category.created_at', '<=', $dateToFormat);
            }

            $data = $query->orderBy('pm_stock_batch.id', 'DESC')->get();

            return view('reports.StockReprts.ajaxStockReports.loadStockReport', compact('data'));
        }
    }


    public function adminSalesReportIndex()
    {
        $vehicles = Vehicles::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        $drivers = Driver::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        $customers = Customer::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        $salesRep = SaleRep::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        $products = SubCategory::where('is_active', STATIC_DATA_MODEL::$Active)->get();

        return view('reports.SalesReport.salesReport', compact('vehicles', 'drivers', 'customers', 'salesRep', 'products'));
    }

    public function adminSalesReport2Index()
    {
        $vehicles = Vehicles::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        $drivers = Driver::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        $customers = Customer::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        $salesRep = SaleRep::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        $products = SubCategory::where('is_active', STATIC_DATA_MODEL::$Active)->get();

        return view('reports.SalesReport.salesReport2', compact('vehicles', 'drivers', 'customers', 'salesRep', 'products'));
    }


    public function getSalesReport(Request $request)
    {
        if ($request->ajax()) {

            $query = DB::table('dm_customer_invoice')
                ->select('dm_customer_invoice.id As InvoiceId')
                ->distinct('dm_customer_invoice.id')
                ->join('dm_customer_invoice_has_stock_batch', 'dm_customer_invoice_has_stock_batch.dm_customer_invoice_id', '=', 'dm_customer_invoice.id')
                ->join("dm_delivery_vehicle_has_stock_batch", function ($join) {
                    $join->on("dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id", "=", "dm_customer_invoice_has_stock_batch.dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id");
                })
                ->join('dm_delivery_vehicle', 'dm_delivery_vehicle.id', '=', 'dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id')
                ->where('dm_customer_invoice.invoice_status', '!=', 3); // drop removed invoices

            $dateFrom = $request->dateFrom;
            $dateTo = $request->dateTo;
            $dateFromFormat = date("Y-m-d", strtotime($dateFrom));
            $dateToFormat = date("Y-m-d", strtotime($dateTo));

            if ($request->customer !== '0') {
                $query->where('dm_customer_invoice.cm_customers_id', $request->customer);
            }
            if ($request->invoiceType !== '0') {
                $query->where('dm_customer_invoice.invoice_type', $request->invoiceType);
            }
            if ($request->invoiceStatus !== '99') {
                $query->where('dm_customer_invoice.invoice_status', $request->invoiceStatus);
            }
            if ($request->vehicle !== '0') {
                $query->where('dm_delivery_vehicle.vm_vehicles_id', $request->vehicle);
            }
            if ($request->salesRep !== '0') {
                $query->where('dm_delivery_vehicle.vm_sales_reps_id', $request->salesRep);
            }
            if ($request->drivers !== '0') {
                $query->where('dm_delivery_vehicle.vm_drivers_id', $request->drivers);
            }

            // if ($request->dateFrom !== null && $request->dateTo == null) {
            //     $query->whereDate('dm_customer_invoice.created_at', $dateFromFormat);
            // }
            // if ($request->dateTo !== null && $request->dateFrom == null) {
            //     $query->whereDate('dm_customer_invoice.created_at', $dateToFormat);
            // }
            // if ($request->dateTo !== null && $request->dateFrom != null) {
            //     $query->whereDate('dm_customer_invoice.created_at', '>=', $dateFromFormat);
            //     $query->whereDate('dm_customer_invoice.created_at', '<=', $dateToFormat);
            // }


            if ($request->dateFrom !== null && $request->dateTo == null) {
                // From dateFrom to the future
                $query->where('dm_customer_invoice.created_at', '>=', $dateFromFormat . " 00:00:00");
            }
            if ($request->dateTo !== null && $request->dateFrom == null) {
                // Up to and including dateTo
                $dateToFormat = date("Y-m-d 23:59:59", strtotime($dateTo)); // Include time to get the end of the day
                $query->where('dm_customer_invoice.created_at', '<=', $dateToFormat);
            }
            if ($request->dateTo !== null && $request->dateFrom != null) {
                // Between dateFrom and dateTo, inclusive
                $dateFromFormat = $dateFromFormat . " 00:00:00";
                $dateToFormat = date("Y-m-d 23:59:59", strtotime($dateTo)); // Adjust to include the end of the day
                $query->whereBetween('dm_customer_invoice.created_at', [$dateFromFormat, $dateToFormat]);
            }



            $data = $query->groupBy('dm_customer_invoice.id')->orderBy('dm_customer_invoice.id')->get();

            $products = SubCategory::where('is_active', STATIC_DATA_MODEL::$Active)->get();

            return view('reports.SalesReport.ajaxSalesReport.loadSalesReportDetails', compact('data', 'products'));
        }
    }


    public function getSalesReport2(Request $request)
    {
        if ($request->ajax()) {

            $query = DB::table('dm_customer_invoice')
                ->select('dm_customer_invoice.id As InvoiceId')
                ->distinct('dm_customer_invoice.id')
                ->join('dm_customer_invoice_has_stock_batch', 'dm_customer_invoice_has_stock_batch.dm_customer_invoice_id', '=', 'dm_customer_invoice.id')
                ->join("dm_delivery_vehicle_has_stock_batch", function ($join) {
                    $join->on("dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id", "=", "dm_customer_invoice_has_stock_batch.dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id");
                })
                ->join('dm_delivery_vehicle', 'dm_delivery_vehicle.id', '=', 'dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id')
                ->where('dm_customer_invoice.invoice_status', '!=', 3); // drop removed invoices

            $dateFrom = $request->dateFrom;
            $dateTo = $request->dateTo;
            $dateFromFormat = date("Y-m-d", strtotime($dateFrom));
            $dateToFormat = date("Y-m-d", strtotime($dateTo));

            if ($request->customer !== '0') {
                $query->where('dm_customer_invoice.cm_customers_id', $request->customer);
            }
            if ($request->invoiceType !== '0') {
                $query->where('dm_customer_invoice.invoice_type', $request->invoiceType);
            }
            if ($request->invoiceStatus !== '99') {
                $query->where('dm_customer_invoice.invoice_status', $request->invoiceStatus);
            }
            if ($request->vehicle !== '0') {
                $query->where('dm_delivery_vehicle.vm_vehicles_id', $request->vehicle);
            }
            if ($request->salesRep !== '0') {
                $query->where('dm_delivery_vehicle.vm_sales_reps_id', $request->salesRep);
            }
            if ($request->drivers !== '0') {
                $query->where('dm_delivery_vehicle.vm_drivers_id', $request->drivers);
            }

            // if ($request->dateFrom !== null && $request->dateTo == null) {
            //     $query->whereDate('dm_customer_invoice.created_at', $dateFromFormat);
            // }
            // if ($request->dateTo !== null && $request->dateFrom == null) {
            //     $query->whereDate('dm_customer_invoice.created_at', $dateToFormat);
            // }
            // if ($request->dateTo !== null && $request->dateFrom != null) {
            //     $query->whereDate('dm_customer_invoice.created_at', '>=', $dateFromFormat);
            //     $query->whereDate('dm_customer_invoice.created_at', '<=', $dateToFormat);
            // }


            if ($request->dateFrom !== null && $request->dateTo == null) {
                // From dateFrom to the future
                $query->where('dm_customer_invoice.created_at', '>=', $dateFromFormat . " 00:00:00");
            }
            if ($request->dateTo !== null && $request->dateFrom == null) {
                // Up to and including dateTo
                $dateToFormat = date("Y-m-d 23:59:59", strtotime($dateTo)); // Include time to get the end of the day
                $query->where('dm_customer_invoice.created_at', '<=', $dateToFormat);
            }
            if ($request->dateTo !== null && $request->dateFrom != null) {
                // Between dateFrom and dateTo, inclusive
                $dateFromFormat = $dateFromFormat . " 00:00:00";
                $dateToFormat = date("Y-m-d 23:59:59", strtotime($dateTo)); // Adjust to include the end of the day
                $query->whereBetween('dm_customer_invoice.created_at', [$dateFromFormat, $dateToFormat]);
            }



            $data = $query->groupBy('dm_customer_invoice.id')->orderBy('dm_customer_invoice.id')->get();

            $products = SubCategory::where('is_active', STATIC_DATA_MODEL::$Active)->get();

            return view('reports.SalesReport.ajaxSalesReport.loadSalesReportDetails2', compact('data', 'products'));
        }
    }


    public function adminRejectedInvoiceReport()
    {
        $vehicles = Vehicles::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        $drivers = Driver::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        $customers = Customer::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        $salesRep = SaleRep::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        $products = SubCategory::all();

        return view('reports.RejectedInvoiceReport.adminRejectedInvoiceReport', compact('vehicles', 'drivers', 'customers', 'salesRep', 'products'));
    }




    public function getRejectedInvoiceReport(Request $request)
    {
        $query = DB::table('dm_customer_invoice')
            ->select(
                'dm_customer_invoice.id As InvoiceId',
                'dm_customer_invoice.invoice_number',
                'dm_customer_invoice.created_at',
                'dm_customer_invoice.invoice_type',
                'dm_customer_invoice.net_price',
                'dm_customer_invoice.discount',
                'dm_customer_invoice.display_discount',
                'dm_customer_invoice.special_discount',
                'dm_customer_invoice.custom_discount',
                'dm_customer_invoice.total_amout_paid',
                'cm_customers.customer_name',
                // **THE CRUCIAL FIX: Select the vehicle number and sales rep name**
                'vm_vehicles.reg_number AS vehicle_number',
                'vm_sales_reps.sales_rep_name AS sales_rep_name'
            )
            ->leftJoin('cm_customers', 'cm_customers.id', '=', 'dm_customer_invoice.cm_customers_id')
            ->leftJoin('dm_customer_invoice_has_stock_batch', 'dm_customer_invoice_has_stock_batch.dm_customer_invoice_id', '=', 'dm_customer_invoice.id')
            ->leftJoin("dm_delivery_vehicle_has_stock_batch", function ($join) {
                $join->on("dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id", "=", "dm_customer_invoice_has_stock_batch.dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id")
                    ->on("dm_delivery_vehicle_has_stock_batch.pm_stock_batch_id", "=", "dm_customer_invoice_has_stock_batch.dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id");
            })
            ->leftJoin('dm_delivery_vehicle', 'dm_delivery_vehicle.id', '=', 'dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id')
            // **NEW JOINS: To get the actual names and numbers**
            ->leftJoin('vm_vehicles', 'vm_vehicles.id', '=', 'dm_delivery_vehicle.vm_vehicles_id')
            ->leftJoin('vm_sales_reps', 'vm_sales_reps.id', '=', 'dm_delivery_vehicle.vm_sales_reps_id')
            ->where('dm_customer_invoice.invoice_status', 3);

        // --- Applying Filters ---
        // (The filtering logic remains exactly the same as the previous step)
        $dateFrom = null;
        if ($request->filled('dateFrom')) {
            $dateFrom = \DateTime::createFromFormat('m/d/Y', $request->dateFrom)->format('Y-m-d');
        }
        $dateTo = null;
        if ($request->filled('dateTo')) {
            $dateTo = \DateTime::createFromFormat('m/d/Y', $request->dateTo)->format('Y-m-d');
        }
        if ($request->filled('invoiceType') && $request->invoiceType > 0) {
            $query->where('dm_customer_invoice.invoice_type', $request->invoiceType);
        }
        if ($request->filled('vehicle')) {
            if ($request->vehicle > 0) {
                $query->where('dm_delivery_vehicle.vm_vehicles_id', $request->vehicle);
            } elseif ($request->vehicle == -1) {
                $query->whereNull('dm_delivery_vehicle.vm_vehicles_id');
            }
        }
        if ($request->filled('salesRep')) {
            if ($request->salesRep > 0) {
                $query->where('dm_delivery_vehicle.vm_sales_reps_id', $request->salesRep);
            } elseif ($request->salesRep == -1) {
                $query->whereNull('dm_delivery_vehicle.vm_sales_reps_id');
            }
        }
        if ($dateFrom) {
            $query->whereDate('dm_customer_invoice.created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('dm_customer_invoice.created_at', '<=', $dateTo);
        }

        $data = $query->distinct()->get();

        return view('reports.RejectedInvoiceReport.loadRejectedInvoiceReportDetails', compact('data'));
    }


    public function adminStockInReportIndex()
    {
        $stock = DB::select('select A.pid,p.sub_category_name as proname,
                            sum(A.in_qty)as in_qty,sum(A.out_qty)as out_qty
                            from(
                            (select 0 as out_qty,0 as in_qty,pc.id as pid,NOW() as createdat
                            from pm_product_sub_category pc)
                            union all
                            (select 0 as out_qty,sum(stock_in_quantity)as
                            in_qty,pm_product_sub_category_id as pid,created_at as createdat
                            from pm_stock_batch group by pm_product_sub_category_id,created_at)
                            union all (select sum(quantity) as out_qty,0 as in_qty,pm_product_sub_category_id as pid,created_at as createdat
                            from dm_customer_invoice_has_stock_batch
                            group by pm_product_sub_category_id,created_at))A inner join pm_product_sub_category p on A.pid=p.id
                            where A.createdat between
                            CONCAT(CURDATE()," ","00:00:00") and
                            CONCAT(CURDATE()," ","23:59:59")
                            group by A.pid,p.sub_category_name');

        return view('reports.StockInReport.stockInReport', compact('stock'));
    }


    public function adminDailySalesReport()
    {
        $vehicles = Vehicles::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        $drivers = Driver::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        $customers = Customer::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        $salesRep = SaleRep::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        $products = SubCategory::all();

        return view('reports.dailyReport.salesReport', compact('vehicles', 'drivers', 'customers', 'salesRep', 'products'));
    }


    public function getSalesReportDaily(Request $request)
    {
        $query = DB::table('dm_customer_invoice')
            ->select('dm_customer_invoice.id As InvoiceId')
            ->distinct('dm_customer_invoice.id')
            ->join('dm_customer_invoice_has_stock_batch', 'dm_customer_invoice_has_stock_batch.dm_customer_invoice_id', '=', 'dm_customer_invoice.id')
            ->join("dm_delivery_vehicle_has_stock_batch", function ($join) {
                $join->on("dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id", "=", "dm_customer_invoice_has_stock_batch.dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id");
            })
            ->join('dm_delivery_vehicle', 'dm_delivery_vehicle.id', '=', 'dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id')
            ->where('dm_customer_invoice.invoice_status', '!=', 3); // drop removed invoices

        $dateFrom = $request->dateFrom;
        $dateTo = $request->dateTo;
        $dateFromFormat = (is_null($dateFrom) ? null : date("Y-m-d", strtotime($dateFrom)));
        $dateToFormat = (is_null($dateTo) ? null : date("Y-m-d", strtotime($dateTo)));

        $customer = $request->customer;
        $invoiceType = $request->invoiceType;
        $salesRep = $request->salesRep;
        $drivers = $request->drivers;
        $vehicle = $request->vehicle;

        if ($request->customer !== '0') {
            $query->where('dm_customer_invoice.cm_customers_id', $request->customer);
        }
        if ($request->invoiceType !== '0') {
            $query->where('dm_customer_invoice.invoice_type', $request->invoiceType);
        }
        if ($request->vehicle !== '0') {
            $query->where('dm_delivery_vehicle.vm_vehicles_id', $request->vehicle);
        }
        if ($request->salesRep !== '0') {
            $query->where('dm_delivery_vehicle.vm_sales_reps_id', $request->salesRep);
        }
        if ($request->drivers !== '0') {
            $query->where('dm_delivery_vehicle.vm_drivers_id', $request->drivers);
        }
        // if ($request->dateFrom !== null) {

        //     $query->whereDate('dm_customer_invoice.created_at', $dateFromFormat);
        // }

        if ($request->dateFrom !== null && $request->dateTo == null) {
            $query->whereDate('dm_customer_invoice_has_stock_batch.created_at', '>=', $dateFromFormat);
        }
        if ($request->dateTo !== null && $request->dateFrom == null) {
            $query->whereDate('dm_customer_invoice_has_stock_batch.created_at', '<=', $dateToFormat);
        }
        if ($request->dateTo !== null && $request->dateFrom != null) {
            $query->whereDate('dm_customer_invoice_has_stock_batch.created_at', '>=', $dateFromFormat);
            $query->whereDate('dm_customer_invoice_has_stock_batch.created_at', '<=', $dateToFormat);
        }

        $data = $query->groupBy('dm_customer_invoice.id')->get();

        $products = $query = DB::table('pm_product_sub_category')
            ->select('pm_product_sub_category.*')
            ->join('pm_stock_batch', 'pm_stock_batch.pm_product_sub_category_id', '=', 'pm_product_sub_category.id')
            ->join('dm_delivery_vehicle_has_stock_batch', 'dm_delivery_vehicle_has_stock_batch.pm_stock_batch_id', '=', 'pm_stock_batch.id')
            ->join('dm_delivery_vehicle', 'dm_delivery_vehicle.id', '=', 'dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id')
            ->join('vm_vehicles', 'vm_vehicles.id', '=', 'dm_delivery_vehicle.vm_vehicles_id')
            ->where('vm_vehicles.id', $request->vehicle)
            ->groupBy('pm_product_sub_category.id')
            ->get();

        return view('reports.dailyReport.ajaxSalesReport.loadSalesReportDetails', compact('data', 'products', 'customer', 'invoiceType', 'salesRep', 'drivers', 'dateFromFormat', 'vehicle', 'dateToFormat'));
    }


    // Sales Report Details - Print
    function print($dateFromFormat1, $customer, $invoiceType, $salesRep, $drivers, $vehicle, $dateToFormat1)
    {
        $query = DB::table('dm_customer_invoice')
            ->select('dm_customer_invoice.id As InvoiceId')
            ->distinct('dm_customer_invoice.id')
            ->join('dm_customer_invoice_has_stock_batch', 'dm_customer_invoice_has_stock_batch.dm_customer_invoice_id', '=', 'dm_customer_invoice.id')
            ->join("dm_delivery_vehicle_has_stock_batch", function ($join) {
                $join->on("dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id", "=", "dm_customer_invoice_has_stock_batch.dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id");
            })
            ->join('dm_delivery_vehicle', 'dm_delivery_vehicle.id', '=', 'dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id')
            ->where('dm_customer_invoice.invoice_status', '!=', 3); // drop removed invoices

        $dateFrom = $dateFromFormat1;
        $dateTo = $dateToFormat1;
        $dateFromFormat = ($dateFrom == "ANY" ? null : date("Y-m-d", strtotime($dateFrom)));
        $dateToFormat = ($dateTo == "ANY" ? null : date("Y-m-d", strtotime($dateTo)));

        if ($customer !== '0') {
            $query->where('dm_customer_invoice.cm_customers_id', $customer);
        }
        if ($invoiceType !== '0') {
            $query->where('dm_customer_invoice.invoice_type', $invoiceType);
        }
        if ($vehicle !== '0') {
            $query->where('dm_delivery_vehicle.vm_vehicles_id', $vehicle);
        }
        if ($salesRep !== '0') {
            $query->where('dm_delivery_vehicle.vm_sales_reps_id', $salesRep);
        }
        if ($drivers !== '0') {
            $query->where('dm_delivery_vehicle.vm_drivers_id', $drivers);
        }

        // if ($dateFromFormat !== null) {

        //     $query->whereDate('dm_customer_invoice.created_at', $dateFromFormat);
        // }

        if ($dateFromFormat !== null && $dateToFormat == null) {
            $query->whereDate('dm_customer_invoice.created_at', '>=', $dateFromFormat);
        }
        if ($dateToFormat !== null && $dateFromFormat == null) {
            $query->whereDate('dm_customer_invoice.created_at', '<=', $dateToFormat);
        }
        if ($dateToFormat !== null && $dateFromFormat != null) {
            $query->whereDate('dm_customer_invoice.created_at', '>=', $dateFromFormat);
            $query->whereDate('dm_customer_invoice.created_at', '<=', $dateToFormat);
        }

        $data = $query->groupBy('dm_customer_invoice.id')->get();

        $products = $query = DB::table('pm_product_sub_category')
            ->select('pm_product_sub_category.*')
            ->join('pm_stock_batch', 'pm_stock_batch.pm_product_sub_category_id', '=', 'pm_product_sub_category.id')
            ->join('dm_delivery_vehicle_has_stock_batch', 'dm_delivery_vehicle_has_stock_batch.pm_stock_batch_id', '=', 'pm_stock_batch.id')
            ->join('dm_delivery_vehicle', 'dm_delivery_vehicle.id', '=', 'dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id')
            ->join('vm_vehicles', 'vm_vehicles.id', '=', 'dm_delivery_vehicle.vm_vehicles_id')
            ->where('vm_vehicles.id', $vehicle)
            ->groupBy('pm_product_sub_category.id')
            ->get();

        return view('reports.dailyReport.dailySalesReportPrint', compact('data', 'products', 'dateFromFormat'));
    }


    // Collection Report
    public function adminCollectionReportIndex()
    {
        $vehicles = Vehicles::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        $salesRep = SaleRep::where('is_active', STATIC_DATA_MODEL::$Active)->get();

        return view('reports.Collection.adminCollectionReport', compact('vehicles', 'salesRep'));
    }


    // get Collection Report
    public function getCollectionReport(Request $request)
    {
        // Get the date filters from the request
        $daterangeFrom = $request->dateFrom;
        $daterangeTo = $request->dateTo;
        $dateFromFormat = date("Y-m-d", strtotime($daterangeFrom)) . " 00:00:00";
        $dateToFormat = date("Y-m-d", strtotime($daterangeTo)) . " 23:59:59";

        // Query 1 [to get Credit Invoice data with payment reocrds] ----------------------------------------------------------------------
        $SQL_Q1 = 'SELECT
                        `dm_customer_invoice`.`id` AS Invoice_ID,
                        `dm_customer_invoice`.`invoice_number` AS Invoice_No,
                        `dm_customer_invoice`.`invoice_type` AS Invoice_Type,
                        `dm_customer_invoice`.`created_at` AS Invoice_Date,
                        `dm_customer_invoice`.`return_price` AS Return_Price,
                        `dm_customer_invoice`.`net_price` AS Invoice_Price,
                        `dm_customer_invoice`.`discount`,
                        `dm_customer_invoice`.`display_discount`,
                        `dm_customer_invoice`.`special_discount`,
                        `dm_customer_invoice`.`custom_discount`,
                        `cm_customers`.`customer_name` AS Customer_Name,
                        `vm_vehicles`.`reg_number` AS Vehicle_No,
                        `payment_summary`.`sumPaymentAmount` AS Payment_Amount,
                        `payment_summary`.`paymentDate` AS Payment_Date
                    FROM `dm_customer_invoice`
                    JOIN `dm_customer_invoice_has_stock_batch`
                        ON `dm_customer_invoice_has_stock_batch`.`dm_customer_invoice_id` = `dm_customer_invoice`.`id`
                    JOIN `dm_delivery_vehicle_has_stock_batch`
                        ON `dm_delivery_vehicle_has_stock_batch`.`dm_delivery_vehicle_id` = `dm_customer_invoice_has_stock_batch`.`dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id`
                    JOIN `dm_delivery_vehicle`
                        ON `dm_delivery_vehicle`.`id` = `dm_delivery_vehicle_has_stock_batch`.`dm_delivery_vehicle_id`
                    JOIN `vm_vehicles`
                        ON `vm_vehicles`.`id` = `dm_delivery_vehicle`.`vm_vehicles_id`
                    JOIN `cm_customers`
                        ON `cm_customers`.`id` = `dm_customer_invoice`.`cm_customers_id`
                    JOIN ( SELECT
                            dm_customer_invoice_id,
                            SUM(amount) AS sumPaymentAmount,
                            MAX(created_at) AS paymentDate
                            FROM dm_payments
                            WHERE created_at BETWEEN "'.$dateFromFormat.'" AND "'.$dateToFormat.'"
                            GROUP BY dm_customer_invoice_id
                        ) AS payment_summary
                        ON `payment_summary`.`dm_customer_invoice_id` = `dm_customer_invoice`.`id`
                    WHERE `dm_customer_invoice`.`invoice_status` != '.STATIC_DATA_MODEL::$invoiceDeleted.'
                        AND `dm_customer_invoice`.`invoice_type` = '.STATIC_DATA_MODEL::$credit;

        // apply vehicle filter
        if ($request->vehicle !== '0') {
            $SQL_Q1 .= ' AND `dm_delivery_vehicle`.`vm_vehicles_id` = '.$request->vehicle;
        }
        // apply sales rep filter
        if ($request->salesRep !== '0') {
            $SQL_Q1 .= ' AND `dm_delivery_vehicle`.`vm_sales_reps_id` = '.$request->salesRep;
        }
        $SQL_Q1 .= ' GROUP BY `dm_customer_invoice`.`id`';


        // Query 2 [to get all type of Invoice data for the date range] ---------------------------------------------------------------------
        $SQL_Q2 = 'SELECT
                        `dm_customer_invoice`.`id` AS Invoice_ID,
                        `dm_customer_invoice`.`invoice_number` AS Invoice_No,
                        `dm_customer_invoice`.`invoice_type` AS Invoice_Type,
                        `dm_customer_invoice`.`created_at` AS Invoice_Date,
                        `dm_customer_invoice`.`return_price` AS Return_Price,
                        `dm_customer_invoice`.`net_price` AS Invoice_Price,
                        `dm_customer_invoice`.`discount`,
                        `dm_customer_invoice`.`display_discount`,
                        `dm_customer_invoice`.`special_discount`,
                        `dm_customer_invoice`.`custom_discount`,
                        `cm_customers`.`customer_name` AS Customer_Name,
                        `vm_vehicles`.`reg_number` AS Vehicle_No,
                        `dm_customer_invoice`.`net_price` AS Payment_Amount,
                        `dm_customer_invoice`.`created_at` AS Payment_Date
                    FROM `dm_customer_invoice`
                    JOIN `dm_customer_invoice_has_stock_batch`
                        ON `dm_customer_invoice_has_stock_batch`.`dm_customer_invoice_id` = `dm_customer_invoice`.`id`
                    JOIN `dm_delivery_vehicle_has_stock_batch`
                        ON `dm_delivery_vehicle_has_stock_batch`.`dm_delivery_vehicle_id` = `dm_customer_invoice_has_stock_batch`.`dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id`
                    JOIN `dm_delivery_vehicle`
                        ON `dm_delivery_vehicle`.`id` = `dm_delivery_vehicle_has_stock_batch`.`dm_delivery_vehicle_id`
                    JOIN `vm_vehicles`
                        ON `vm_vehicles`.`id` = `dm_delivery_vehicle`.`vm_vehicles_id`
                    JOIN `cm_customers`
                        ON `cm_customers`.`id` = `dm_customer_invoice`.`cm_customers_id`
                    WHERE `dm_customer_invoice`.`invoice_status` != '.STATIC_DATA_MODEL::$invoiceDeleted.'
                        AND `dm_customer_invoice`.`created_at` BETWEEN "'.$dateFromFormat.'" AND "'.$dateToFormat.'"';

        // apply vehicle filter
        if ($request->vehicle !== '0') {
            $SQL_Q2 .= ' AND `dm_delivery_vehicle`.`vm_vehicles_id` = '.$request->vehicle;
        }
        // apply sales rep filter
        if ($request->salesRep !== '0') {
            $SQL_Q2 .= ' AND `dm_delivery_vehicle`.`vm_sales_reps_id` = '.$request->salesRep;
        }
        $SQL_Q2 .= ' GROUP BY `dm_customer_invoice`.`id`';

        // execute the queries
        $InvoiceData_creditPayments = DB::select($SQL_Q1);
        $InvoiceData_all = DB::select($SQL_Q2);


        // calculations ------------------------------------------------------------------------------
        $cash_Sale = 0;
        $credit_Sale = 0;
        $cheque_Sale = 0;
        $total_Return = 0;
        $netTotal = 0;

        $amountGainByAdding_customDiscounts_CASH = 0;
        $amountGainByAdding_customDiscounts_CREDIT = 0;
        $amountGainByAdding_customDiscounts_CHEQUE = 0;

        foreach ($InvoiceData_all as $invoice) {
            $discount = $invoice->discount + $invoice->display_discount + $invoice->special_discount + $invoice->custom_discount;
            $returnPrice = $invoice->Return_Price;
            $total_Return += $returnPrice;

            if ($invoice->Invoice_Type == STATIC_DATA_MODEL::$cash) {
                $cash_Sale += ($invoice->Invoice_Price - $discount);
                if ($invoice->custom_discount != null && $invoice->custom_discount > 0) {
                    $amountGainByAdding_customDiscounts_CASH += $invoice->Invoice_Price - $invoice->custom_discount;
                }
            } elseif ($invoice->Invoice_Type == STATIC_DATA_MODEL::$credit) {
                $credit_Sale += ($invoice->Invoice_Price - $discount);
                if ($invoice->custom_discount != null && $invoice->custom_discount > 0) {
                    $amountGainByAdding_customDiscounts_CREDIT += $invoice->Invoice_Price - $invoice->custom_discount;
                }
            } elseif ($invoice->Invoice_Type == STATIC_DATA_MODEL::$cheque) {
                $cheque_Sale += ($invoice->Invoice_Price - $discount);
                if ($invoice->custom_discount != null && $invoice->custom_discount > 0) {
                    $amountGainByAdding_customDiscounts_CHEQUE += $invoice->Invoice_Price - $invoice->custom_discount;
                }
            }
        }

        $netTotal = ($cash_Sale + $credit_Sale + $cheque_Sale);

        return view('reports.Collection.ajaxCollectionReport.loadCollectionReport', compact(
            'InvoiceData_creditPayments',
            'cash_Sale',
            'credit_Sale',
            'cheque_Sale',
            'total_Return',
            'netTotal',
            'amountGainByAdding_customDiscounts_CASH',
            'amountGainByAdding_customDiscounts_CREDIT',
            'amountGainByAdding_customDiscounts_CHEQUE'
        ));
    }


    public function loadInvoicePaymentHistory(Request $request)
    {
        $invoiceId = $request->invoiceId;
        $invoiceDetails = customerInvoices::find($request->invoiceId);
        $invoicePayment = invoicePayments::where('dm_customer_invoice_id', $request->invoiceId)->get();
        return view('reports.SalesReport.ajaxSalesReport.ajaxLoadPaymentHistoryToModal', compact('invoicePayment', 'invoiceId', 'invoiceDetails'));
    }


    public function adminRouteWiseCreditReport(Request $request)
    {
        $routes = Routes::all();
        return view('reports.creditReportRouteWise.routeWiseCreditReport', compact('routes'));
    }

    // Route Wise Credit Report - SEARCH by Date & Route
    public function getCreditRouteReport(Request $request)
    {
        $dateFrom = $request->dateFrom;
        $dateTo = $request->dateTo;
        $routeSend = $request->route;
        $dateFromFormat = (is_null($dateFrom) ? null : date("Y-m-d", strtotime($dateFrom)));
        $dateToFormat = (is_null($dateTo) ? null : date("Y-m-d", strtotime($dateTo)));

        $query = DB::table('dm_customer_invoice')
            ->select('dm_customer_invoice.*')
            ->join('cm_customers', 'cm_customers.id', '=', 'dm_customer_invoice.cm_customers_id')
            ->join('cm_routes', 'cm_routes.id', '=', 'cm_customers.cm_routes_id')
            ->where('dm_customer_invoice.invoice_type', STATIC_DATA_MODEL::$credit)
            ->where('dm_customer_invoice.invoice_status', '!=', STATIC_DATA_MODEL::$invoiceCompleted)
            ->where('dm_customer_invoice.invoice_status', '!=', 3); // drop removed invoices

        if ($request->route !== '0') {
            $query->where('cm_routes.id', $request->route);
        }
        if ($request->dateFrom !== null && $request->dateTo == null) {
            $query->whereDate('dm_customer_invoice.created_at', '>=', $dateFromFormat);
        }
        if ($request->dateTo !== null && $request->dateFrom == null) {
            $query->whereDate('dm_customer_invoice.created_at', '<=', $dateToFormat);
        }
        if ($request->dateTo !== null && $request->dateFrom != null) {
            $query->whereDate('dm_customer_invoice.created_at', '>=', $dateFromFormat);
            $query->whereDate('dm_customer_invoice.created_at', '<=', $dateToFormat);
        }
        $data = $query->get();

        return view('reports.creditReportRouteWise.ajaxrouteReport.loadCreditRouteReport', compact('data', 'dateFromFormat', 'dateToFormat', 'routeSend'));
    }


    public function loadCreditReportPrint($dateFrom, $dateTo, $route)
    {
        $dateFrom = $dateFrom;
        $dateTo = $dateTo;
        $dateFromFormat = ($dateFrom == "ANY" ? null : date("Y-m-d", strtotime($dateFrom)));
        $dateToFormat = ($dateTo == "ANY" ? null : date("Y-m-d", strtotime($dateTo)));

        $query = DB::table('dm_customer_invoice')
            ->select('dm_customer_invoice.*')
            ->join('cm_customers', 'cm_customers.id', '=', 'dm_customer_invoice.cm_customers_id')
            ->join('cm_routes', 'cm_routes.id', '=', 'cm_customers.cm_routes_id')
            ->where('dm_customer_invoice.invoice_type', STATIC_DATA_MODEL::$credit)
            ->where('dm_customer_invoice.invoice_status', '!=', STATIC_DATA_MODEL::$invoiceCompleted)
            ->where('dm_customer_invoice.invoice_status', '!=', 3); // drop removed invoices

        if ($route !== '0') {
            $query->where('cm_routes.id', $route);
        }
        if ($dateFromFormat !== null && $dateToFormat == null) {
            $query->whereDate('dm_customer_invoice.created_at', '>=', $dateFromFormat);
        }
        if ($dateToFormat !== null && $dateFromFormat == null) {
            $query->whereDate('dm_customer_invoice.created_at', '<=', $dateToFormat);
        }
        if ($dateToFormat !== null && $dateFromFormat != null) {
            $query->whereDate('dm_customer_invoice.created_at', '>=', $dateFromFormat);
            $query->whereDate('dm_customer_invoice.created_at', '<=', $dateToFormat);
        }

        $data = $query->get();
        $routeName = "";

        if ($route !== '0') {
            $routeDes = Routes::find($route);
            $routeName = $routeDes->route_name;
        } else {
            $routeName = "ALL";
        }

        return view('reports.creditReportRouteWise.routeWiseCreditReportPrint', compact('data', 'routeName'));
    }


    // Route Wise Sales Report - INDEX
    public function adminRouteWiseSalesReportIndex(Request $request)
    {
        $routes = Routes::orderBy('route_name')->get();
        return view('reports.SalesReport.adminRouteWiseSalesReport', compact('routes'));
    }


    // Route Wise Sales Report - SEARCH by Date & Route
    public function getRouteWiseSalesReport(Request $request)
    {
        $dateFrom = $request->dateFrom;
        $dateTo = $request->dateTo;
        $route = $request->route;
        $dateFromFormat = (is_null($dateFrom) ? null : date("Y-m-d", strtotime($dateFrom)));
        $dateToFormat = (is_null($dateTo) ? null : date("Y-m-d", strtotime($dateTo)));

        //-- NEW QUERY ~ Invoice count including Zero values --//
        // Addition 24 hours to date range for SAME DATE filters
        $dateFromFormat2 = (is_null($dateFrom) ? null : date("Y-m-d H:i:s", strtotime($dateFrom)));
        $dateToFormat2 = (is_null($dateTo) ? null : date("Y-m-d H:i:s", strtotime($dateTo . '+23 hour' . '+59 minute' . '+59 second')));

        if ($request->route != "0" && $request->route != "" && $request->route != null) {
            $query = DB::table('cm_customers')
                ->select('cm_customers.id AS CustomerID',
                // DB::raw("SUM(dm_customer_invoice.invoice_price) AS TotalAmount"),
                DB::raw("SUM(dm_customer_invoice.invoice_price - dm_customer_invoice.discount - dm_customer_invoice.display_discount - dm_customer_invoice.special_discount - dm_customer_invoice.custom_discount) AS TotalAmount"),
                // DB::raw("Count(CASE WHEN dm_customer_invoice.created_at >= '$dateFromFormat2' AND dm_customer_invoice.created_at <= '$dateToFormat2' THEN 1 ELSE NULL END) AS BillCount")
                DB::raw('ifnull(count(dm_customer_invoice.id),0) as BillCount')
                )
                // ->select(DB::raw('SUM(dm_customer_invoice.invoice_price) AS TotalAmount'))
                ->leftJoin('dm_customer_invoice', 'dm_customer_invoice.cm_customers_id', '=', 'cm_customers.id')
                ->where('cm_customers.cm_routes_id', $route)
                // ->where('cm_customers.id', 1725)
                ->whereDate('dm_customer_invoice.created_at', '>=', $dateFromFormat2)
                ->whereDate('dm_customer_invoice.created_at', '<=', $dateToFormat2)
                ->where('cm_customers.is_active', STATIC_DATA_MODEL::$Active)
                ->where('dm_customer_invoice.invoice_status', '!=', 3) // drop removed invoices
                ->groupBy('cm_customers.id')
                ->get();

                // $XD = DB::select("select * from usdm_customer_invoiceers where cm_customers.cm_routes_id = 28");

            // $query2 = DB::table('cm_customers')
            //     ->select('cm_customers.id AS CustomerID', DB::raw("Count(CASE WHEN dm_customer_invoice.created_at >= '$dateFromFormat2' AND dm_customer_invoice.created_at <= '$dateToFormat2' THEN 1 ELSE NULL END) AS BillCount"))
            //     ->leftJoin('dm_customer_invoice', 'dm_customer_invoice.cm_customers_id', '=', 'cm_customers.id')
            //     ->where('cm_customers.cm_routes_id', $route)
            //     ->where('cm_customers.is_active', STATIC_DATA_MODEL::$Active)
            //     ->groupBy('cm_customers.id')
            //     ->get();


// ---------------------------------------------------------------WORKING-------------------------------------------------------------------------

                // $queryXX = DB::table('dm_customer_invoice')
                //     ->select('cm_customers.id', 'cm_customers.cm_routes_id', DB::raw('SUM(dm_customer_invoice.invoice_price) AS TotalAmount'))
                //     ->leftJoin('cm_customers', 'cm_customers.id', '=', 'dm_customer_invoice.cm_customers_id')
                //     ->where('cm_customers.is_active', STATIC_DATA_MODEL::$Active)
                //     ->where('cm_customers.cm_routes_id', $request->route)
                //     ->where('cm_customers.id', 1725)
                //     ->whereDate('dm_customer_invoice.created_at', '>=', $dateFromFormat2)
                //     ->whereDate('dm_customer_invoice.created_at', '<=', $dateToFormat2)
                //     ->groupBy('cm_customers.cm_routes_id', 'cm_customers.id')
                //     ->get();

// ------------------------------------------------------------------------------------------------------------------------------------------------


                    // SELECT
                    // SUM(
                    //   `dm_customer_invoice`.`invoice_price`
                    // ) AS `TOT_AMOUNT`,
                    // `cm_customers`.`cm_routes_id`,
                    // `cm_customers`.`id`,
                    // `cm_customers`.`customer_name`
                //   FROM
                //     `dm_customer_invoice`
                    // INNER JOIN `cm_customers`
                    //   ON (
                    //     `dm_customer_invoice`.`cm_customers_id` = `cm_customers`.`id`
                    //   )
                //   WHERE (
                    //   `cm_customers`.`cm_routes_id` = 28
                    //   AND `cm_customers`.`id` = 1725
                    //   AND `dm_customer_invoice`.`created_at` >= '2022-11-01'
                    //   AND `dm_customer_invoice`.`created_at` <= '2022-11-07'
                    // )
                //   GROUP BY `cm_customers`.`cm_routes_id`,
                    // `cm_customers`.`id`;




        //     $results = DB::select(DB::raw("SELECT * FROM some_table WHERE some_col = '$someVariable'"));
        //     $query3= DB::select(DB::raw("SELECT
        //                                         SUM(
        //                                         `dm_customer_invoice`.`invoice_price`
        //                                         ) AS `TOT`,
        //                                         `cm_customers`.`cm_routes_id`,
        //                                         `cm_customers`.`id`
        //                                     FROM
        //                                         dm_customer_invoice
        //                                         INNER JOIN cm_customers
        //                                         ON (
        //                                             dm_customer_invoice.cm_customers_id = cm_customers.`id`
        //                                         )
        //                                     WHERE (
        //                                         `cm_customers`.`cm_routes_id` = 28
        //                                         AND `cm_customers`.`id` = 1725
        //                                         AND `dm_customer_invoice`.`created_at` >= '2022-11-01'
        //                                         AND `dm_customer_invoice`.`created_at` <= '2022-11-30'
        //                                         )
        //                                     GROUP BY `cm_customers`.`cm_routes_id`,
        //                                         `cm_customers`.`id`; "));



        }

        // dd($XD);

        $data = $query;
        // $amountData = $query2;

        //-- Pure SQL Working QUERY --//
        // $SQL = DB::select("SELECT cm_customers.id AS Customer_ID, COUNT(CASE WHEN dm_customer_invoice.created_at >= '$dateFromFormat' AND dm_customer_invoice.created_at <= '$dateToFormat' THEN 1 ELSE NULL END) AS Bill_Count
        //                     FROM cm_customers
        //                     LEFT JOIN dm_customer_invoice
        //                         ON dm_customer_invoice.cm_customers_id = cm_customers.id
        //                     WHERE cm_customers.cm_routes_id = '$route'
        //                     GROUP BY cm_customers.id");

        //-- OLD QUERY ~ Count without zero values --//
        // $query = DB::table('dm_customer_invoice')
        //     ->select('cm_customers_id', DB::raw('count(*) as BillCount'))
        //     ->join('cm_customers', 'cm_customers.id', '=', 'dm_customer_invoice.cm_customers_id')
        //     ->join('cm_routes', 'cm_routes.id', '=', 'cm_customers.cm_routes_id')
        //     ->groupBy('cm_customers_id');

        // if ($request->route != '0') {
        //     $query->where('cm_routes.id', $request->route);
        // }
        // if ($request->dateFrom != null && $request->dateTo == null) {
        //     $query->whereDate('dm_customer_invoice.created_at', '>=', $dateFromFormat);
        // }
        // if ($request->dateTo != null && $request->dateFrom == null) {
        //     $query->whereDate('dm_customer_invoice.created_at', '<=', $dateToFormat);
        // }
        // if ($request->dateTo != null && $request->dateFrom != null) {
        //     $query->whereDate('dm_customer_invoice.created_at', '>=', $dateFromFormat);
        //     $query->whereDate('dm_customer_invoice.created_at', '<=', $dateToFormat);
        // }

        return view('reports.SalesReport.ajaxSalesReport.loadRouteWiseSalesReportDetails', compact('data', 'dateFromFormat', 'dateToFormat', 'route'));
    }


    // Route Wise Sales Report - VIEW all Invoices for a selected Customer
    public function viewInvoiceListModal(Request $request)
    {
        $customerID = $request->viewCustomerId;
        $dateFrom = $request->selectedDateFrom;
        $dateTo = $request->selectedDateTo;
        $route = $request->route;

        $viewQuery = DB::table('dm_customer_invoice')
            ->select('dm_customer_invoice.*')
            ->leftJoin('cm_customers', 'cm_customers.id', '=', 'dm_customer_invoice.cm_customers_id')
            ->where('cm_customers_id', '=', $customerID)
            ->where('dm_customer_invoice.invoice_status', '!=', 3); // drop removed invoices

        if ($request->route != "0" && $request->route != "" && $request->route != null) {
            $viewQuery->where('cm_routes_id', $request->route);
        }
        if ($request->selectedDateFrom != null && $request->selectedDateTo == null) {
            $viewQuery->whereDate('dm_customer_invoice.created_at', '>=', $dateFrom);
        }
        if ($request->selectedDateTo != null && $request->selectedDateFrom == null) {
            $viewQuery->whereDate('dm_customer_invoice.created_at', '<=', $dateTo);
        }
        if ($request->selectedDateTo != null && $request->selectedDateFrom != null) {
            $viewQuery->whereDate('dm_customer_invoice.created_at', '>=', $dateFrom);
            $viewQuery->whereDate('dm_customer_invoice.created_at', '<=', $dateTo);
        }

        $ViewData = $viewQuery->get();

        return view('reports.SalesReport.ajaxSalesReport.loadRouteWiseSalesReportsToViewModal', compact('ViewData', 'customerID'));
    }


    // Discount Report - INDEX
    public function adminDiscountReportIndex(Request $request)
    {
        $vehicles = Vehicles::orderBy('reg_number')->get();

        return view('reports.DiscountReport.discountReport', compact('vehicles'));
    }


    // Discount Report - SEARCH by Discount Type & Date
    public function getDiscountReport(Request $request)
    {
        // get request variables data
        $displayDiscount = $request->displayDiscount;
        $loyaltyDiscount = $request->loyaltyDiscount;
        $specialDiscount = $request->specialDiscount;
        $anyDiscount = $request->anyDiscount;
        $vehicle = $request->vehicle;
        $dateFrom = $request->dateFrom;
        $dateTo = $request->dateTo;
        $formated_dateFrom = date("Y-m-d", strtotime($dateFrom));
        $formated_dateTo = date("Y-m-d", strtotime($dateTo));

        $query = DB::table('dm_customer_invoice')
            ->distinct('dm_customer_invoice.id')
            ->select('dm_customer_invoice.*', 'dm_delivery_vehicle.vm_vehicles_id AS vehicleID', 'vm_vehicles.reg_number AS vehicleRegNumber', 'cm_customers.customer_name AS customerName')
            ->join('dm_customer_invoice_has_stock_batch', 'dm_customer_invoice_has_stock_batch.dm_customer_invoice_id', '=', 'dm_customer_invoice.id')
            ->join("dm_delivery_vehicle_has_stock_batch", "dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id", "=", "dm_customer_invoice_has_stock_batch.dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id")
            ->join('dm_delivery_vehicle', 'dm_delivery_vehicle.id', '=', 'dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id')
            ->join('vm_vehicles', 'vm_vehicles.id', '=', 'dm_delivery_vehicle.vm_vehicles_id')
            ->join('cm_customers', 'cm_customers.id', '=', 'dm_customer_invoice.cm_customers_id')
            ->where('dm_customer_invoice.invoice_status', '!=', 3); // drop removed invoices

        if ($displayDiscount == 'true' && $loyaltyDiscount == 'false' && $specialDiscount == 'false' && $anyDiscount == 'false') {
            $query->where('display_discount', '>', 0);
            $query->where('discount', '=', 0);
            $query->where('special_discount', '=', 0);
        }
        if ($displayDiscount == 'false' && $loyaltyDiscount == 'true' && $specialDiscount == 'false' && $anyDiscount == 'false') {
            $query->where('display_discount', '=', 0);
            $query->where('discount', '>', 0);
            $query->where('special_discount', '=', 0);
        }
        if ($displayDiscount == 'false' && $loyaltyDiscount == 'false' && $specialDiscount == 'true' && $anyDiscount == 'false') {
            $query->where('display_discount', '=', 0);
            $query->where('discount', '=', 0);
            $query->where('special_discount', '>', 0);
        }
        if ($displayDiscount == 'true' && $loyaltyDiscount == 'true' && $specialDiscount == 'true' && $anyDiscount == 'false') {
            $query->where('display_discount', '>', 0);
            $query->where('discount', '>', 0);
            $query->where('special_discount', '>', 0);
        }
        if ($anyDiscount == 'true') {
            $query->where(function ($qq) {
                $qq->where('display_discount', '>', 0)->orWhere('discount', '>', 0)->orWhere('special_discount', '>', 0);
            });
        }

        if ($vehicle != '0') {
            $query->where('dm_delivery_vehicle.vm_vehicles_id', $vehicle);
        }
        if ($request->dateFrom != null && $request->dateTo == null) {
            $query->whereDate('dm_customer_invoice.created_at', '>=', $formated_dateFrom);
        }
        if ($request->dateFrom == null && $request->dateTo != null) {
            $query->whereDate('dm_customer_invoice.created_at', '<=', $formated_dateTo);
        }
        if ($request->dateFrom != null && $request->dateTo != null) {
            $query->whereDate('dm_customer_invoice.created_at', '>=', $formated_dateFrom);
            $query->whereDate('dm_customer_invoice.created_at', '<=', $formated_dateTo);
        }

        $invoiceCollection = $query->get();

        return view('reports.DiscountReport.ajaxDiscountReport.loadDiscountReportDetails', compact('invoiceCollection'));
    }

}
