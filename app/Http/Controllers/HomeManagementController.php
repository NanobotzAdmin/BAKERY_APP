<?php

namespace App\Http\Controllers;

use App\Driver;
use App\Routes;
use App\SaleRep;
use App\Customer;
use App\Vehicles;
use Carbon\Carbon;
use App\STATIC_DATA_MODEL;
use App\DeliveryVehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class HomeManagementController extends Controller
{
    public function admindashboardIndex()
    {
        $userRole = session('user_type');
        if ($userRole == '3') {
            $vehicle = "";
            $driver = "";
            $route = "";
            $shops = '';
            $invoiceCustomers = "";
            $userId = session('logged_user_id');
            $salesRep = SaleRep::where('um_user_id', $userId)->first();
            $delivery = DeliveryVehicle::where([['vm_sales_reps_id', $salesRep->id], ['status', 1]])->first();
            if (empty($delivery)) {
                $vehicle  = '';
                $driver = "";
                $route = "";
                $shops = '';
                $invoiceCustomers = "";
                $deliveryId = "";
            } else {
                $deliveryId = $delivery->id;
                $vehicle = Vehicles::find($delivery->vm_vehicles_id);
                $driver = Driver::find($delivery->vm_drivers_id);
                $route = Routes::find($delivery->cm_routes_id);
                $shops = Customer::where('cm_routes_id', $route->id)->where('is_active', 1)->get();

                $invoiceCustomers = DB::table('dm_customer_invoice')
                    ->select('dm_customer_invoice.id', 'dm_customer_invoice.cm_customers_id')
                    ->distinct('dm_customer_invoice.id')
                    ->join('dm_customer_invoice_has_stock_batch', 'dm_customer_invoice_has_stock_batch.dm_customer_invoice_id', '=', 'dm_customer_invoice.id')

                    ->join("dm_delivery_vehicle_has_stock_batch", function ($join) {
                        $join->on("dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id", "=", "dm_customer_invoice_has_stock_batch.dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id");
                    })

                    ->join('dm_delivery_vehicle', 'dm_delivery_vehicle.id', '=', 'dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id')
                    ->where('dm_delivery_vehicle.id', $delivery->id)
                    ->groupBy('dm_customer_invoice.id')->pluck('dm_customer_invoice.cm_customers_id')->toArray();
            }

                return view('adminDashboard', compact('vehicle', 'driver', 'salesRep', 'route', 'shops', 'invoiceCustomers', 'deliveryId'));
        }
        return view('adminDashboard');
    }

    public function loadDeliveryModalDashboard(Request $request)
    {
        $customerID = $request->cusId;

        $invoiceObj = DB::table('dm_customer_invoice')
            ->select('dm_customer_invoice.id As invoId')
            ->distinct('dm_customer_invoice.id')
            ->join('dm_customer_invoice_has_stock_batch', 'dm_customer_invoice_has_stock_batch.dm_customer_invoice_id', '=', 'dm_customer_invoice.id')

            ->join("dm_delivery_vehicle_has_stock_batch", function ($join) {
                $join->on("dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id", "=", "dm_customer_invoice_has_stock_batch.dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id");
            })

            ->join('dm_delivery_vehicle', 'dm_delivery_vehicle.id', '=', 'dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id')
            ->where('dm_delivery_vehicle.id', $request->deliveryId)
            ->where('dm_customer_invoice.cm_customers_id', $request->cusId)
            ->groupBy('dm_customer_invoice.id')->get();

        ///////////////////////////////////////////


        $invoiceObjPayment = DB::table('dm_customer_invoice')
            ->select('dm_customer_invoice.id')
            ->distinct('dm_customer_invoice.id')
            ->join('dm_customer_invoice_has_stock_batch', 'dm_customer_invoice_has_stock_batch.dm_customer_invoice_id', '=', 'dm_customer_invoice.id')

            ->join("dm_delivery_vehicle_has_stock_batch", function ($join) {
                $join->on("dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id", "=", "dm_customer_invoice_has_stock_batch.dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id");
            })

            ->join('dm_delivery_vehicle', 'dm_delivery_vehicle.id', '=', 'dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id')
            ->where('dm_delivery_vehicle.vm_sales_reps_id', $request->repId)
            ->where('dm_customer_invoice.cm_customers_id', $request->cusId)
            ->groupBy('dm_customer_invoice.id')->pluck('dm_customer_invoice.id');


        $customerInvoicePayment = DB::table('dm_customer_invoice')
            ->select(DB::raw("SUM(dm_payments.amount) as sumPaymentAmount"), 'dm_customer_invoice.invoice_type as invoType', 'dm_customer_invoice.invoice_number as invoNum', 'dm_customer_invoice.created_at as invoDate')
            ->join('dm_payments', 'dm_payments.dm_customer_invoice_id', '=', 'dm_customer_invoice.id')
            ->where('dm_customer_invoice.invoice_type', STATIC_DATA_MODEL::$credit)
            ->whereDate('dm_payments.created_at', Carbon::today())
            ->whereIn('dm_customer_invoice.id', $invoiceObjPayment)
            ->groupBy('dm_customer_invoice.id')
            ->get();

        return view('ajaxDashboard.loadDeliveryDetailsTosalesRep', compact('invoiceObj', 'customerInvoicePayment', 'customerID'));
    }
}
