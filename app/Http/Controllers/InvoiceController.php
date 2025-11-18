<?php

namespace App\Http\Controllers;

use App\User;
use App\SaleRep;
use App\Customer;
use App\Vehicles;
use Carbon\Carbon;
use App\VmVehicles;
use App\SubCategory;
use App\CustomerRack;
use App\MainCategory;
use App\invoicePayments;
use App\OrderTakingForm;
use App\customerInvoices;
use App\STATIC_DATA_MODEL;
use Illuminate\Http\Request;
use App\customerInvoiceHasStock;
use App\DeliveryVehicleHasStock;
use App\OrderTakingFormHasProduct;
use Illuminate\Support\Facades\DB;
use App\customerInvoiceHasDeletedStock;

class InvoiceController extends Controller
{
    public function adminCreateInvoiceIndex()
    {
        $logged_user = session('logged_user_id');

        // get Invoice data
        $InvoiceData = customerInvoices::where('created_by', $logged_user)
            ->whereDate('created_at', Carbon::today())
            ->where('dm_customer_invoice.invoice_status', '!=', 3) // drop removed invoices
            ->get();

        $deliveryVehicle = DB::table('dm_delivery_vehicle')
            ->select('dm_delivery_vehicle.unloading_rack_count', 'dm_delivery_vehicle.cm_routes_id')
            ->join('vm_sales_reps', 'vm_sales_reps.id', '=', 'dm_delivery_vehicle.vm_sales_reps_id')
            ->where('dm_delivery_vehicle.status', 1)
            ->where('vm_sales_reps.um_user_id', $logged_user)
            ->first();
        if (empty($deliveryVehicle)) {
            $deliveryUnloadingRack = "NO";
        } else {
            $deliveryUnloadingRack = $deliveryVehicle->unloading_rack_count;
        }

        // get Customers list
        // $customerList = Customer::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        $customerQuery = Customer::where('is_active', STATIC_DATA_MODEL::$Active);
        // if (!empty($deliveryVehicle)) {
        //     $customerQuery->where('cm_routes_id', $deliveryVehicle->cm_routes_id);
        // }
        $customerList = $customerQuery->orderBy('customer_name')->get();

        return view('distribution.adminCreateInvoice', compact('customerList', 'InvoiceData', 'deliveryUnloadingRack'));
    }


        public function adminCreateInvoiceNewIndex()
    {
        $logged_user = session('logged_user_id');

        // get Invoice data
        $InvoiceData = customerInvoices::where('created_by', $logged_user)
            ->whereDate('created_at', Carbon::today())
            ->where('dm_customer_invoice.invoice_status', '!=', 3) // drop removed invoices
            ->get();

        $deliveryVehicle = DB::table('dm_delivery_vehicle')
            ->select('dm_delivery_vehicle.unloading_rack_count', 'dm_delivery_vehicle.cm_routes_id')
            ->join('vm_sales_reps', 'vm_sales_reps.id', '=', 'dm_delivery_vehicle.vm_sales_reps_id')
            ->where('dm_delivery_vehicle.status', 1)
            ->where('vm_sales_reps.um_user_id', $logged_user)
            ->first();
        if (empty($deliveryVehicle)) {
            $deliveryUnloadingRack = "NO";
        } else {
            $deliveryUnloadingRack = $deliveryVehicle->unloading_rack_count;
        }

        // get Customers list
        // $customerList = Customer::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        $customerQuery = Customer::where('is_active', STATIC_DATA_MODEL::$Active);
        // if (!empty($deliveryVehicle)) {
        //     $customerQuery->where('cm_routes_id', $deliveryVehicle->cm_routes_id);
        // }
        $customerList = $customerQuery->orderBy('customer_name')->get();

        return view('distribution.adminCreateInvoiceNew', compact('customerList', 'InvoiceData', 'deliveryUnloadingRack'));
    }


    public function adminOrderTakeFormIndex()
    {
        $logged_user = session('logged_user_id');
        $user = User::find($logged_user);
        $categories = MainCategory::all();
        $products = SubCategory::where('is_active', 1)->get();
        $vehicles = Vehicles::select('vm_vehicles.id', 'vm_vehicles.reg_number')
            ->where('vm_vehicles.is_active', 1);

        if (!($user->pm_user_role_id == 1 || $user->pm_user_role_id == 2)) {
            $vehicles->join('dm_delivery_vehicle', 'vm_vehicles.id', '=', 'dm_delivery_vehicle.vm_vehicles_id')
                ->whereDate('dm_delivery_vehicle.created_at', Carbon::today());
        }

        $vehicles = $vehicles->get();

        return view('distribution.adminOrderTakeForm', compact('categories', 'products', 'vehicles'));
    }


    public function adminOrderTakeFormDetailsIndex()
    {
        $vehicles = Vehicles::select('id', 'reg_number')
            ->where('is_active', 1)
            ->get();

        $categories = MainCategory::select('id', 'main_category_name')
            ->where('is_active', 1)
            ->get();

        foreach ($categories as $category) {
            $category->subCategories = SubCategory::select('id', 'sub_category_name')
                ->where('pm_product_main_category_id', $category->id)
                ->where('is_active', 1)
                ->get();
        }

        return view('distribution.adminOrderTakeFormDetails', compact('vehicles', 'categories'));
    }

    // load delivery data to logeed user
    public function loadInvoiceDeliveryDataToTBL(Request $request)
    {
        $logged_user = session('logged_user_id');

        // $deliveryProductData = DB::table('vm_sales_reps')
        // ->select('dm_delivery_vehicle_has_stock_batch.*')
        // ->join('dm_delivery_vehicle', 'dm_delivery_vehicle.vm_sales_reps_id', '=', 'vm_sales_reps.id')
        // ->join('dm_delivery_vehicle_has_stock_batch', 'dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id', '=', 'dm_delivery_vehicle.id')
        // ->where('vm_sales_reps.um_user_id', $logged_user)
        // ->where('dm_delivery_vehicle.status', STATIC_DATA_MODEL::$deliveryLoaded)
        // ->get();
        //   $deliveryProductData = DeliveryVehicleHasStock::where('created_by',$logged_user)->get();

        // old
        // $deliveryProductData = DB::select("SELECT dm_delivery_vehicle_has_stock_batch.*
        //                                     FROM dm_delivery_vehicle
        //                                     INNER JOIN vm_sales_reps
        //                                     ON (dm_delivery_vehicle.vm_sales_reps_id = vm_sales_reps.id)
        //                                     INNER JOIN dm_delivery_vehicle_has_stock_batch
        //                                     ON (dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id = dm_delivery_vehicle.id)
        //                                     INNER JOIN pm_stock_batch
        //                                     ON (dm_delivery_vehicle_has_stock_batch.pm_stock_batch_id = pm_stock_batch.id)
        //                                     WHERE vm_sales_reps.um_user_id = '" . $logged_user . "' &&  dm_delivery_vehicle.status = '1'  order BY pm_stock_batch.pm_product_sub_category_id");

        // NEW sorted query
        $deliveryProductData = DB::select(" SELECT dm_delivery_vehicle_has_stock_batch.*
                                            FROM dm_delivery_vehicle
                                            INNER JOIN vm_sales_reps ON (dm_delivery_vehicle.vm_sales_reps_id = vm_sales_reps.id)
                                            INNER JOIN dm_delivery_vehicle_has_stock_batch ON (dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id = dm_delivery_vehicle.id)
                                            INNER JOIN pm_stock_batch ON (dm_delivery_vehicle_has_stock_batch.pm_stock_batch_id = pm_stock_batch.id)
                                            INNER JOIN pm_product_sub_category ON (pm_stock_batch.pm_product_sub_category_id = pm_product_sub_category.id)
                                            WHERE vm_sales_reps.um_user_id = ? AND dm_delivery_vehicle.status = '1'
                                            ORDER BY ISNULL(pm_product_sub_category.sequence_no), pm_product_sub_category.sequence_no ASC ",
            [$logged_user]
        );

        //new
        // $deliveryProductData = DB::select("SELECT dm_delivery_vehicle_has_stock_batch.*
        //                                     FROM dm_delivery_vehicle
        //                                     INNER JOIN vm_sales_reps
        //                                         ON (dm_delivery_vehicle.vm_sales_reps_id = vm_sales_reps.id)
        //                                     INNER JOIN dm_delivery_vehicle_has_stock_batch
        //                                         ON (dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id = dm_delivery_vehicle.id)
        //                                     INNER JOIN pm_stock_batch
        //                                         ON (dm_delivery_vehicle_has_stock_batch.pm_stock_batch_id = pm_stock_batch.id)
        //                                         WHERE vm_sales_reps.um_user_id = " . $logged_user . " AND  dm_delivery_vehicle.status = 1
        //                                     ORDER BY pm_stock_batch.pm_product_sub_category_id");
        // dd($deliveryProductData);
        return view($request->url, compact('deliveryProductData'));
    }


    public function loadGenerateInoviceModal(Request $request)
    {
        $page = $request->page;
        $is_forbiddenItemsAdded = $request->is_forbiddenItemsAdded;
        $totReturn = $request->totReturn;
        $totSubReal = $request->totSubReal;

        if ($page == 1) {
            return view('distribution.ajaxInvoice.ajaxInvoiceGenerateModal', compact('is_forbiddenItemsAdded', 'totSubReal', 'totReturn'));
        } else if ($page == 2) {
            return view('distribution.ajaxInvoice.ajaxInvoiceGenerateModal_new', compact('is_forbiddenItemsAdded', 'totSubReal', 'totReturn'));
        }

    }



    // ================================================================== C R E A T E   I N V O I C E =================================================================================
    // Generate INVOICE NUMBER
    private function generateInvoiceNumber()
    {
        $invoiceNumber = '';

        if (customerInvoices::count() == 0) {
            $invoiceNumber = "INV0001";
        } else {
            $lastInvoice = DB::table('dm_customer_invoice')->latest('id')->first();
            $last = substr($lastInvoice->invoice_number, 6);
            $invo = intval($last) + 1;
            $invoiceNumber = "INV000" . $invo;
        }

        return $invoiceNumber;
    }


    // MAIN SAVE funtion
    public function saveInvoiceData(Request $request)
    {
        DB::beginTransaction();

        try {
            $msg = '';
            $InvoiceId = 0;
            $status = 'false';

            $loggedUserId = session('logged_user_id');
            $invoiceNumber = customerInvoices::count() > 0 ? customerInvoices::latest('id')->first()->invoice_number : 'INV0001';

            $customerRackCount = CustomerRack::where('cm_customers_id', $request->customer)->first();
            $customer = Customer::find($request->customer);
            $currentMonth = date('m');

            $result = []; // Initialize an empty array to hold the result

            // save Invoice by calling Invoice Type handlers -------------------------------------------------------------------------------------------------
            if ($request->invoiceType == STATIC_DATA_MODEL::$credit) {
                $result = $this->handleCreditInvoice($request, $loggedUserId, $invoiceNumber, $currentMonth, $customerRackCount, $InvoiceId, $status, $msg);
            } elseif ($request->invoiceType == STATIC_DATA_MODEL::$cash) {
                $result = $this->handleCashInvoice($request, $loggedUserId, $invoiceNumber, $currentMonth, $customerRackCount, $InvoiceId, $status, $msg);
            } elseif ($request->invoiceType == STATIC_DATA_MODEL::$cheque) {
                $result = $this->handleChequeInvoice($request, $loggedUserId, $invoiceNumber, $currentMonth, $customerRackCount, $InvoiceId, $status, $msg);
            } else {
                DB::rollback();
                throw new \Exception('Invalid invoice type.');
            }
            // -----------------------------------------------------------------------------------------------------------------------------------------------

            $msgDB = 'success';

            // check $result is not null or empty, handle the result here
            if ($result) {
                $msg = $result['msg'];
                $InvoiceId = $result['InvoiceId'];
                $status = $result['status'];

                if ($status == "true") {
                    DB::commit();
                } else {
                    DB::rollback();
                }
            }

        } catch (\Exception $e) {
            $msg = 'error';
            $msgDB = $e->getMessage();
            DB::rollback();
        }

        return compact('msg', 'InvoiceId', 'status', 'msgDB');
    }


    // C R E D I T  =  (1)
    private function handleCreditInvoice($request, $loggedUserId, $invoiceNumber, $currentMonth, $customerRackCount, $InvoiceId, $status, $msg)
    {
        if (empty($customerRackCount)) {
            $status = 'rack';
            $msg = 'rack';
            return [
                'status' => $status,
                'msg' => $msg,
                'InvoiceId' => $InvoiceId
            ];
        } else {
            // generate new Invoice Number
            $invoiceNumber1 = $this->generateInvoiceNumber();

            $customerLastCreditBill = customerInvoices::where([['invoice_status', STATIC_DATA_MODEL::$invoicePending], ['invoice_type', STATIC_DATA_MODEL::$credit], ['cm_customers_id', $request->customer]])->orderBy('created_at', 'desc')->first();
            $invpoiceAmount = customerInvoices::where([['invoice_status', STATIC_DATA_MODEL::$invoicePending], ['invoice_type', STATIC_DATA_MODEL::$credit], ['cm_customers_id', $request->customer]])->whereRaw('MONTH(created_at) = ?', [$currentMonth])->get();

            // save Customer Invoice
            $invoiceCustomer = new customerInvoices();
            $invoiceCustomer->invoice_number = $invoiceNumber1;
            $invoiceCustomer->cm_customers_id = $request->customer;
            $invoiceCustomer->invoice_price = $request->subTotal;
            $invoiceCustomer->return_price = $request->totReturn;
            $invoiceCustomer->net_price = $request->subTotal;
            $invoiceCustomer->invoice_status = STATIC_DATA_MODEL::$invoicePending;
            $invoiceCustomer->given_rack_count = $request->givenRackCount;
            $invoiceCustomer->taken_rack_count = $request->takenRackCount;
            $invoiceCustomer->invoice_type = $request->invoiceType;
            $invoiceCustomer->total_amout_paid = 0.0;
            $invoiceCustomer->updated_at = Carbon::now();
            $invoiceCustomer->discount = $request->subDiscount;
            $invoiceCustomer->display_discount = $request->displayDiscount;
            $invoiceCustomer->special_discount = $request->specialDiscount;
            $invoiceCustomer->custom_discount = $request->customDiscount;
            $invoiceCustomer->custom_discount_percentage = $request->customDiscountPercentage;
            $invoiceCustomer->created_at = Carbon::now();
            $invoiceCustomer->created_by = $loggedUserId;
            $invoiceCustomer->updated_by = $loggedUserId;
            $invoiceCustomerSave = $invoiceCustomer->save();
            $lastCustomerInvoice = DB::table('dm_customer_invoice')->orderBY('id', 'desc')->first();

            $userActivity = new UserActivityManagementController();
            $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "New Customer Invoice - , Invoice Id - " . $lastCustomerInvoice->id . "Saved");

            if ($invoiceCustomerSave) {
                $output_record = $request->all();
                $output_record = $output_record['invoiceData'];
                $basic_details = $output_record['invoiceDetails'];

                // save - Customer Invoice Has Stock ---------------------------------------------------------
                foreach ($basic_details as $basic_detail) {
                    $qty = $basic_detail['qty'];
                    $returnQty = $basic_detail['returnQty'];
                    $unitPrice = $basic_detail['unitPrice'];
                    $stockId = $basic_detail['stockId'];
                    $realQty = floatval($qty) - floatval($returnQty);
                    $vehicleId = $basic_detail['vehicleId'];
                    $proId = $basic_detail['proId'];
                    $returnVal = $basic_detail['returnVal'];

                    if ($stockId != 0) {
                        $deliveryVehicle = DeliveryVehicleHasStock::where('pm_stock_batch_id', $stockId)->first();
                        $updateDelivertyStockQuantity = DeliveryVehicleHasStock::where([['pm_stock_batch_id', $stockId], ['dm_delivery_vehicle_id', $vehicleId]])->decrement('availbale_qty', $qty);
                    }

                    if ($stockId == 0) {
                        $stockId2 = null;
                    } else {
                        $stockId2 = $stockId;
                    }

                    $customerInvoiceStock = new customerInvoiceHasStock();
                    $customerInvoiceStock->dm_customer_invoice_id = $lastCustomerInvoice->id;
                    $customerInvoiceStock->dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id = $vehicleId;
                    $customerInvoiceStock->dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id = $stockId2;
                    $customerInvoiceStock->pm_product_sub_category_id = $proId;
                    $customerInvoiceStock->quantity = $qty;
                    $customerInvoiceStock->unit_price = $unitPrice;

                    if ($stockId == 0) {
                        $customerInvoiceStock->total_price = floatval($returnQty) * floatval($unitPrice);
                    } else {
                        $tot1 = floatval($qty) * floatval($unitPrice);
                        $tot2 = floatval($returnQty) * floatval($returnVal);
                        $sub = floatval($tot1) - floatval($tot2);
                        $customerInvoiceStock->total_price = $sub;
                    }
                    $customerInvoiceStock->return_qty = $returnQty;
                    $customerInvoiceStock->return_price = $returnVal;
                    $customerInvoiceStock->updated_at = Carbon::now();
                    $customerInvoiceStock->created_at = Carbon::now();
                    $customerInvoiceStockSave = $customerInvoiceStock->save();

                    $userActivity = new UserActivityManagementController();
                    $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "New Customer Has Invoice Stock - , Invoice Id - " . $lastCustomerInvoice->id . ", Stock batch Id  -" . $stockId . ", vehicle Id  -" . $deliveryVehicle->dm_delivery_vehicle_id . "Saved");
                }

                // update - Customer Has Rack Count  &  Delivery Vehicle ------------------------
                $updateRackCustomer = DB::table('pm_customers_has_rack_count')
                    ->where('cm_customers_id', $request->customer)
                    ->decrement('rack_count', $request->takenRackCount);

                $updateDeliveryVehicle = DB::table('dm_delivery_vehicle')
                    ->where('id', $request->deliveryIDToReturn)
                    ->increment('unloading_rack_count', $request->takenRackCount);

                $updateRackCustomer1 = DB::table('pm_customers_has_rack_count')
                    ->where('cm_customers_id', $request->customer)
                    ->increment('rack_count', $request->givenRackCount);

                $updateDeliveryVehicle1 = DB::table('dm_delivery_vehicle')
                    ->where('id', $request->deliveryIDToReturn)
                    ->decrement('unloading_rack_count', $request->givenRackCount);

                $InvoiceId = $lastCustomerInvoice->id;

                return [
                    'status' => 'true',
                    'msg' => 'success',
                    'InvoiceId' => $InvoiceId
                ];
            } else {
                return [
                    'status' => 'false',
                    'msg' => 'error',
                    'InvoiceId' => '0'
                ];
            }
        }
    }


    //  C A S H  =  (2)
    private function handleCashInvoice($request, $loggedUserId, $invoiceNumber, $currentMonth, $customerRackCount, $InvoiceId, $status, $msg)
    {
        if (empty($customerRackCount)) {
            $status = 'rack';
            $msg = 'rack';
            return [
                'status' => $status,
                'msg' => $msg,
                'InvoiceId' => $InvoiceId
            ];
        } else {
            // generate new Invoice Number
            $invoiceNumber1 = $this->generateInvoiceNumber();

            // save Customer Invoice
            $invoiceCustomer = new customerInvoices();
            $invoiceCustomer->invoice_number = $invoiceNumber1;
            $invoiceCustomer->cm_customers_id = $request->customer;
            $invoiceCustomer->invoice_price = $request->subTotal;
            $invoiceCustomer->return_price = $request->totReturn;
            $invoiceCustomer->net_price = $request->subTotal;
            $invoiceCustomer->invoice_status = STATIC_DATA_MODEL::$invoicePending;
            // $invoiceCustomer->invoice_status = STATIC_DATA_MODEL::$invoiceCompleted;
            $invoiceCustomer->invoice_type = $request->invoiceType;
            $invoiceCustomer->total_amout_paid = 0.0;
            // $invoiceCustomer->total_amout_paid = $request->subTotal - ($request->subDiscount + $request->displayDiscount);
            $invoiceCustomer->given_rack_count = $request->givenRackCount;
            $invoiceCustomer->taken_rack_count = $request->takenRackCount;
            $invoiceCustomer->discount = $request->subDiscount;
            $invoiceCustomer->display_discount = $request->displayDiscount;
            $invoiceCustomer->special_discount = $request->specialDiscount;
            $invoiceCustomer->custom_discount = $request->customDiscount;
            $invoiceCustomer->custom_discount_percentage = $request->customDiscountPercentage;
            $invoiceCustomer->created_at = Carbon::now();
            $invoiceCustomer->updated_at = Carbon::now();
            $invoiceCustomer->created_by = $loggedUserId;
            $invoiceCustomer->updated_by = $loggedUserId;
            $invoiceCustomerSave = $invoiceCustomer->save();
            $lastCustomerInvoice = DB::table('dm_customer_invoice')->orderBY('id', 'desc')->first();

            $userActivity = new UserActivityManagementController();
            $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "New Customer Invoice - , Invoice Id - " . $lastCustomerInvoice->id . "Saved");

            if ($invoiceCustomerSave) {
                $output_record = $request->all();
                $output_record = $output_record['invoiceData'];
                $basic_details = $output_record['invoiceDetails'];

                // save - Customer Invoice Has Stock ---------------------------------------------------------
                foreach ($basic_details as $basic_detail) {
                    $rowOfDatas = $basic_detail;
                    $qty = $rowOfDatas['qty'];
                    $returnQty = $rowOfDatas['returnQty'];
                    $unitPrice = $rowOfDatas['unitPrice'];
                    $stockId = $rowOfDatas['stockId'];
                    $realQty = floatval($qty) - floatval($returnQty);
                    $vehicleId = $rowOfDatas['vehicleId'];
                    $proId = $rowOfDatas['proId'];
                    $returnVal = $rowOfDatas['returnVal'];

                    $saveDeliveryvehicle = $vehicleId;

                    if ($stockId != 0) {
                        $deliveryVehicle = DeliveryVehicleHasStock::where('pm_stock_batch_id', $stockId)->first();
                        $updateDelivertyStockQuantity = DeliveryVehicleHasStock::where([['pm_stock_batch_id', $stockId], ['dm_delivery_vehicle_id', $vehicleId]])->decrement('availbale_qty', $qty);
                    }

                    if ($stockId == 0) {
                        $stockId2 = null;
                    } else {
                        $stockId2 = $stockId;
                    }

                    $customerInvoiceStock = new customerInvoiceHasStock();
                    $customerInvoiceStock->dm_customer_invoice_id = $lastCustomerInvoice->id;
                    $customerInvoiceStock->dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id = $vehicleId;
                    $customerInvoiceStock->dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id = $stockId2;
                    $customerInvoiceStock->pm_product_sub_category_id = $proId;
                    $customerInvoiceStock->quantity = $qty;
                    $customerInvoiceStock->unit_price = $unitPrice;

                    if ($stockId == 0) {
                        $customerInvoiceStock->total_price = floatval($returnQty) * floatval($unitPrice);
                    } else {
                        $tot1 = floatval($qty) * floatval($unitPrice);
                        $tot2 = floatval($returnQty) * floatval($returnVal);
                        $sub = floatval($tot1) - floatval($tot2);
                        $customerInvoiceStock->total_price = $sub;
                    }

                    $customerInvoiceStock->return_qty = $returnQty;
                    $customerInvoiceStock->return_price = $returnVal;
                    $customerInvoiceStock->updated_at = Carbon::now();
                    $customerInvoiceStock->created_at = Carbon::now();

                    $customerInvoiceStockSave = $customerInvoiceStock->save();

                    $userActivity = new UserActivityManagementController();
                    $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "New Customer Has Invoice Stock - , Invoice Id - " . $lastCustomerInvoice->id . ", Stock batch Id  -" . $stockId . ", vehicle Id  -" . $deliveryVehicle->dm_delivery_vehicle_id . "Saved");
                }


                // ---------------------------------------------- save payment ----------------------------------------------------------------------------------
                $receptNumber = '';

                // Gets only the last record, fixing the memory error.
                $lastRecept = DB::table('dm_payments')->latest('id')->first();

                if (!$lastRecept) {
                    $receptNumber = "REC0001";
                } else {
                    // number generation method.
                    $last = substr($lastRecept->receipt_no, 6);
                    $invo = intval($last) + 1;
                    $receptNumber = "REC000" . $invo;
                }

                $customerInvoice = customerInvoices::find($lastCustomerInvoice->id);
                // get Total of added Discounts
                $netDiscount = floatval($customerInvoice->discount) + floatval($customerInvoice->display_discount) + floatval($customerInvoice->special_discount) + floatval($customerInvoice->custom_discount);
                $netSub = floatval($customerInvoice->net_price) - floatval($netDiscount);
                $updateNetTotal = $customerInvoice->total_amout_paid + $netSub;

                if (invoicePayments::where('receipt_no', $receptNumber)->exists()) {
                    $msg = 'already';
                    return compact('msg');
                } else if (floatval($updateNetTotal) > floatval($netSub)) {
                    $msg = 'totalExceed';
                    return compact('msg');
                } else {
                    $chequeNo = null;
                    $chequeDate = null;

                    $payment = new invoicePayments();
                    $payment->receipt_no = $receptNumber;
                    $payment->payment_date = Carbon::now();
                    $payment->type = "cash";
                    $payment->amount = $netSub;
                    $payment->dm_delivery_vehicle_id = $saveDeliveryvehicle;
                    $payment->cheque_number = $chequeNo;
                    $payment->cheque_date = $chequeDate;
                    $payment->is_returned = 0;
                    $payment->dm_customer_invoice_id = $lastCustomerInvoice->id;
                    $payment->is_active = STATIC_DATA_MODEL::$Active;
                    $payment->created_at = Carbon::now();
                    $payment->updated_at = Carbon::now();
                    $payment->created_by = $loggedUserId;
                    $payment->updated_by = $loggedUserId;

                    $paymentsaved = $payment->save();

                    $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "Invoice payment saved, invoice ID: " . $lastCustomerInvoice->id . " & Payment ID:" . $payment->id);

                    $customerInvoice->total_amout_paid = $updateNetTotal;

                    // final status check with bcmath
                    $totalDiscount = '0.00';
                    // Add each discount to the total
                    $totalDiscount = bcadd($totalDiscount, (string)$customerInvoice->discount, 2);
                    $totalDiscount = bcadd($totalDiscount, (string)$customerInvoice->display_discount, 2);
                    $totalDiscount = bcadd($totalDiscount, (string)$customerInvoice->special_discount, 2);
                    $totalDiscount = bcadd($totalDiscount, (string)$customerInvoice->custom_discount, 2);
                    // calculate the net subtotal by subtracting the total discount
                    $final_netSub = bcsub((string)$customerInvoice->net_price, $totalDiscount, 2);

                    if (bccomp($final_netSub, $customerInvoice->total_amout_paid, 2) == 0) {
                        $customerInvoice->invoice_status = STATIC_DATA_MODEL::$invoiceCompleted;
                    }
                    $customerInvoice->save();
                }
                // ------------------------------------------------- end of save payment ----------------------------------------------------------------------------------

                // update - Customer Has Rack Count  &  Delivery Vehicle ------------------------
                $updateRackCustomer = DB::table('pm_customers_has_rack_count')
                    ->where('cm_customers_id', $request->customer)
                    ->decrement('rack_count', $request->takenRackCount);

                $updateDeliveryVehicle = DB::table('dm_delivery_vehicle')
                    ->where('id', $request->deliveryIDToReturn)
                    ->increment('unloading_rack_count', $request->takenRackCount);

                $updateRackCustomer1 = DB::table('pm_customers_has_rack_count')
                    ->where('cm_customers_id', $request->customer)
                    ->increment('rack_count', $request->givenRackCount);

                $updateDeliveryVehicle1 = DB::table('dm_delivery_vehicle')
                    ->where('id', $request->deliveryIDToReturn)
                    ->decrement('unloading_rack_count', $request->givenRackCount);

                $InvoiceId = $lastCustomerInvoice->id;

                return [
                    'status' => 'true',
                    'msg' => 'success',
                    'InvoiceId' => $InvoiceId
                ];
            } else {
                return [
                    'status' => 'false',
                    'msg' => 'error',
                    'InvoiceId' => '0'
                ];
            }
        }
    }


    // C H E Q U E  =  (3)
    private function handleChequeInvoice($request, $loggedUserId, $invoiceNumber, $currentMonth, $customerRackCount, $InvoiceId, $status, $msg)
    {
        if (empty($customerRackCount)) {
            $status = 'rack';
            $msg = 'rack';
            return [
                'status' => $status,
                'msg' => $msg,
                'InvoiceId' => $InvoiceId
            ];
        } else {
            // generate new Invoice Number
            $invoiceNumber1 = $this->generateInvoiceNumber();

            // save Customer Invoice
            $invoiceCustomer = new customerInvoices();
            $invoiceCustomer->invoice_number = $invoiceNumber1;
            $invoiceCustomer->cm_customers_id = $request->customer;
            $invoiceCustomer->invoice_price = $request->subTotal;
            $invoiceCustomer->return_price = $request->totReturn;
            $invoiceCustomer->net_price = $request->subTotal;
            $invoiceCustomer->invoice_status = STATIC_DATA_MODEL::$invoiceCompleted;
            $invoiceCustomer->invoice_type = $request->invoiceType;
            $invoiceCustomer->total_amout_paid = $request->subTotal - ($request->subDiscount + $request->displayDiscount + $request->specialDiscount);
            $invoiceCustomer->given_rack_count = $request->givenRackCount;
            $invoiceCustomer->taken_rack_count = $request->takenRackCount;
            $invoiceCustomer->discount = $request->subDiscount;
            $invoiceCustomer->display_discount = $request->displayDiscount;
            $invoiceCustomer->special_discount = $request->specialDiscount;
            $invoiceCustomer->custom_discount = $request->customDiscount;
            $invoiceCustomer->custom_discount_percentage = $request->customDiscountPercentage;
            $invoiceCustomer->created_at = Carbon::now();
            $invoiceCustomer->updated_at = Carbon::now();
            $invoiceCustomer->created_by = $loggedUserId;
            $invoiceCustomer->updated_by = $loggedUserId;

            $invoiceCustomerSave = $invoiceCustomer->save();

            $lastCustomerInvoice = DB::table('dm_customer_invoice')->orderBY('id', 'desc')->first();

            $userActivity = new UserActivityManagementController();
            $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "New Customer Invoice - , Invoice Id - " . $lastCustomerInvoice->id . "Saved");

            if ($invoiceCustomerSave) {
                $output_record = $request->all();
                $output_record = $output_record['invoiceData'];
                $basic_details = $output_record['invoiceDetails'];

                // save - Customer Invoice Has Stock ---------------------------------------------------------
                foreach ($basic_details as $basic_detail) {
                    $rowOfDatas = $basic_detail;
                    $qty = $rowOfDatas['qty'];
                    $returnQty = $rowOfDatas['returnQty'];
                    $unitPrice = $rowOfDatas['unitPrice'];
                    $stockId = $rowOfDatas['stockId'];
                    $realQty = floatval($qty) - floatval($returnQty);
                    $vehicleId = $rowOfDatas['vehicleId'];
                    $proId = $rowOfDatas['proId'];
                    $returnVal = $rowOfDatas['returnVal'];

                    if ($stockId != 0) {
                        $deliveryVehicle = DeliveryVehicleHasStock::where('pm_stock_batch_id', $stockId)->first();
                        $updateDelivertyStockQuantity = DeliveryVehicleHasStock::where([['pm_stock_batch_id', $stockId], ['dm_delivery_vehicle_id', $vehicleId]])->decrement('availbale_qty', $qty);
                    }

                    if ($stockId == 0) {
                        $stockId2 = null;
                    } else {
                        $stockId2 = $stockId;
                    }

                    $customerInvoiceStock = new customerInvoiceHasStock();
                    $customerInvoiceStock->dm_customer_invoice_id = $lastCustomerInvoice->id;
                    $customerInvoiceStock->dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id = $vehicleId;
                    $customerInvoiceStock->dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id = $stockId2;
                    $customerInvoiceStock->pm_product_sub_category_id = $proId;
                    $customerInvoiceStock->quantity = $qty;
                    $customerInvoiceStock->unit_price = $unitPrice;

                    if ($stockId == 0) {
                        $customerInvoiceStock->total_price = floatval($returnQty) * floatval($unitPrice);
                    } else {
                        $tot1 = floatval($qty) * floatval($unitPrice);
                        $tot2 = floatval($returnQty) * floatval($returnVal);
                        $sub = floatval($tot1) - floatval($tot2);
                        $customerInvoiceStock->total_price = $sub;
                    }

                    $customerInvoiceStock->return_qty = $returnQty;
                    $customerInvoiceStock->return_price = $returnVal;
                    $customerInvoiceStock->updated_at = Carbon::now();
                    $customerInvoiceStock->created_at = Carbon::now();

                    $customerInvoiceStockSave = $customerInvoiceStock->save();

                    $userActivity = new UserActivityManagementController();
                    $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "New Customer Has Invoice Stock - , Invoice Id - " . $lastCustomerInvoice->id . ", Stock batch Id  -" . $stockId . ", vehicle Id  -" . $deliveryVehicle->dm_delivery_vehicle_id . "Saved");
                }

                // update - Customer Has Rack Count  &  Delivery Vehicle ------------------------
                $updateRackCustomer = DB::table('pm_customers_has_rack_count')
                    ->where('cm_customers_id', $request->customer)
                    ->decrement('rack_count', $request->takenRackCount);

                $updateDeliveryVehicle = DB::table('dm_delivery_vehicle')
                    ->where('id', $request->deliveryIDToReturn)
                    ->increment('unloading_rack_count', $request->takenRackCount);

                $updateRackCustomer1 = DB::table('pm_customers_has_rack_count')
                    ->where('cm_customers_id', $request->customer)
                    ->increment('rack_count', $request->givenRackCount);

                $updateDeliveryVehicle1 = DB::table('dm_delivery_vehicle')
                    ->where('id', $request->deliveryIDToReturn)
                    ->decrement('unloading_rack_count', $request->givenRackCount);

                $InvoiceId = $lastCustomerInvoice->id;

                return [
                    'status' => 'true',
                    'msg' => 'success',
                    'InvoiceId' => $InvoiceId
                ];
            } else {
                return [
                    'status' => 'false',
                    'msg' => 'error',
                    'InvoiceId' => '0'
                ];
            }
        }
    }


    //  missing PAYMENTS inserting method  by  RAZOR  (additional funtion)
    public function addMissingPaymentForInvoices()
    {
        $addedPaymentsCount = 0;
        $page = 1;

        do {
            // Get customer invoices that meet the specified criteria using pagination
            $customerInvoices = customerInvoices::where('created_at', '>=', '2024-02-24 21:15:00')
                ->where('created_at', '<', '2024-03-01 00:00:00')
                ->where('invoice_type', STATIC_DATA_MODEL::$cash)
                // ->get();
                ->paginate(100, ['*'], 'page', $page); // get page by page to avoid memory exceeding

            foreach ($customerInvoices as $invoice) {
                if (!invoicePayments::where('dm_customer_invoice_id', $invoice->id)->exists()) {
                    // Generate receipt number
                    $lastRecept = DB::table('dm_payments')->latest('id')->first();
                    $last = substr($lastRecept->receipt_no, 6);
                    $invo = intval($last) + 1;
                    $receptNumber = "REC000" . $invo;

                    // Get Delivery vehicle
                    $customerInvoiceHasStock = customerInvoiceHasStock::where('dm_customer_invoice_id', $invoice->id)->first();

                    // save new payment
                    $payment = new invoicePayments();
                    $payment->receipt_no = $receptNumber;
                    $payment->payment_date = $invoice->created_at;
                    $payment->type = 'cash';
                    $payment->amount = $invoice->total_amout_paid;
                    if ($customerInvoiceHasStock) {
                        $payment->dm_delivery_vehicle_id = $customerInvoiceHasStock->dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id;
                    } else {
                        $payment->dm_delivery_vehicle_id = null;
                    }
                    $payment->cheque_number = null;
                    $payment->cheque_date = null;
                    $payment->is_returned = 0;
                    $payment->dm_customer_invoice_id = $invoice->id;
                    if ($invoice->invoice_status == STATIC_DATA_MODEL::$invoiceDeleted) {
                        $payment->is_active = STATIC_DATA_MODEL::$Payment_Rejected;
                    } else {
                        $payment->is_active = STATIC_DATA_MODEL::$Payment_Active;
                    }
                    $payment->created_at = $invoice->created_at;
                    $payment->updated_at = Carbon::now();
                    $payment->created_by = $invoice->created_by;
                    $payment->updated_by = $invoice->created_by;

                    // Save the new payment record and increment addedPaymentsCount if successful
                    if ($payment->save()) {
                        $addedPaymentsCount++;
                    }
                }
            }

            $page++;

        } while ($customerInvoices->hasMorePages());

        // Construct the response message
        $message = $addedPaymentsCount > 0 ? "New Payment Records added successfully !!! Record count : $addedPaymentsCount" : "No payments were added.";

        // Return a JSON response with the message
        return response()->json(['message' => $message]);
    }

    // Invoices missing data fixing Queries

    //  ----  (1) RUN this first ----
    // UPDATE `dm_customer_invoice`
    // SET `total_amout_paid` = `invoice_price` - (`discount` + `display_discount`)
    // WHERE
    // `created_at` >= '2024-02-24 21:15:00' AND
    // `created_at` < '2024-03-01 00:00:00' AND
    // `invoice_type` = 2;


    //  ----  (2) RUN this second ----
    // UPDATE `dm_customer_invoice`
    // SET
    // `invoice_status` = 1
    // WHERE
    // `created_at` >= '2024-02-24 21:15:00' AND
    // `created_at` < '2024-03-01 00:00:00' AND
    // `invoice_type` = 2 AND
    // `invoice_status` = 0;

    // ================================================================== End  of  C R E A T E   I N V O I C E  Section =================================================================================



    // Create INVOICE -- old method
    // public function saveInvoiceData(Request $request)
    // {
    //     $status = 'false';
    //     $stockId2 = '';
    //     $saveDeliveryvehicle = "";

    //     $logged_user = session('logged_user_id');
    //     $CustomerRackCount = CustomerRack::where('cm_customers_id', $request->customer)->first();
    //     $customer = Customer::find($request->customer);

    //     $currentMonth = date('m');
    //     $invoiceNumber1 = '';
    //     $invoiceNumber = customerInvoices::get();

    //     if ($invoiceNumber->count() == 0) {
    //         $invoiceNumber1 = "INV0001";
    //     } else {
    //         $lastInvoice = DB::table('dm_customer_invoice')->latest('id')->first();
    //         $last = substr($lastInvoice->invoice_number, 6);
    //         $invo = intval($last) + 1;
    //         $invoiceNumber1 = "INV000" . $invo;
    //     }

    //     // Invoice Type - CREDIT
    //     if ($request->invoiceType == STATIC_DATA_MODEL::$credit) {
    //         DB::beginTransaction();
    //         try {
    //             if (empty($CustomerRackCount)) {
    //                 $status = 'rack';
    //                 $msg = 'rack';
    //             } else {
    //                 $customerLastCreditBill = customerInvoices::where([['invoice_status', STATIC_DATA_MODEL::$invoicePending], ['invoice_type', STATIC_DATA_MODEL::$credit], ['cm_customers_id', $request->customer]])->orderBy('created_at', 'desc')->first();
    //                 $invpoiceAmount = customerInvoices::where([['invoice_status', STATIC_DATA_MODEL::$invoicePending], ['invoice_type', STATIC_DATA_MODEL::$credit], ['cm_customers_id', $request->customer]])->whereRaw('MONTH(created_at) = ?', [$currentMonth])->get();

    //                 //  if($invpoiceAmount->count() == 0){
    //                 $invoiceCustomer = new customerInvoices();
    //                 $invoiceCustomer->invoice_number = $invoiceNumber1;
    //                 $invoiceCustomer->cm_customers_id = $request->customer;
    //                 $invoiceCustomer->invoice_price = $request->subTotal;
    //                 $invoiceCustomer->return_price = $request->totReturn;
    //                 $invoiceCustomer->net_price = $request->subTotal;
    //                 $invoiceCustomer->discount = 0.0;
    //                 $invoiceCustomer->invoice_status = STATIC_DATA_MODEL::$invoicePending;
    //                 $invoiceCustomer->given_rack_count = $request->givenRackCount;
    //                 $invoiceCustomer->taken_rack_count = $request->takenRackCount;
    //                 $invoiceCustomer->invoice_type = $request->invoiceType;
    //                 $invoiceCustomer->total_amout_paid = 0.0;
    //                 $invoiceCustomer->updated_at = Carbon::now();
    //                 $invoiceCustomer->discount =  $request->subDiscount;
    //                 $invoiceCustomer->display_discount =  $request->displayDiscount;
    //                 $invoiceCustomer->created_at = Carbon::now();
    //                 $invoiceCustomer->created_by = $logged_user;
    //                 $invoiceCustomer->updated_by = $logged_user;
    //                 $invoiceCustomerSave = $invoiceCustomer->save();
    //                 $lastCustomerInvoice = DB::table('dm_customer_invoice')->orderBY('id', 'desc')->first();

    //                 $userActivity = new UserActivityManagementController();
    //                 $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "New Customer Invoice - , Invoice Id - " . $lastCustomerInvoice->id . "Saved");

    //                 if ($invoiceCustomerSave) {

    //                     $output_record = $request->all();
    //                     $output_record = $output_record['invoiceData'];
    //                     $basic_details = $output_record['invoiceDetails'];

    //                     for ($index = 0; $index < count($basic_details); $index++) {

    //                         $rowOfDatas = $basic_details[$index];
    //                         $qty = $rowOfDatas['qty'];
    //                         $returnQty = $rowOfDatas['returnQty'];
    //                         $unitPrice = $rowOfDatas['unitPrice'];
    //                         $stockId = $rowOfDatas['stockId'];
    //                         $realQty = floatval($qty) - floatval($returnQty);
    //                         $vehicleId = $rowOfDatas['vehicleId'];
    //                         $proId = $rowOfDatas['proId'];
    //                         $returnVal = $rowOfDatas['returnVal'];

    //                         if ($stockId != 0) {
    //                             $deliveryVehicle = DeliveryVehicleHasStock::where('pm_stock_batch_id', $stockId)->first();
    //                             $updateDelivertyStockQuantity = DeliveryVehicleHasStock::where([['pm_stock_batch_id', $stockId], ['dm_delivery_vehicle_id', $vehicleId]])->decrement('availbale_qty', $qty);
    //                         }

    //                         if ($stockId == 0) {
    //                             $stockId2 = null;
    //                         } else {
    //                             $stockId2 = $stockId;
    //                         }

    //                         $customerInvoiceStock = new customerInvoiceHasStock();
    //                         $customerInvoiceStock->dm_customer_invoice_id = $lastCustomerInvoice->id;
    //                         $customerInvoiceStock->dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id = $vehicleId;
    //                         $customerInvoiceStock->dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id = $stockId2;
    //                         $customerInvoiceStock->pm_product_sub_category_id = $proId;
    //                         $customerInvoiceStock->quantity = $qty;
    //                         $customerInvoiceStock->unit_price = $unitPrice;

    //                         if ($stockId == 0) {
    //                             $customerInvoiceStock->total_price = floatval($returnQty) * floatval($unitPrice);
    //                         } else {
    //                             $tot1 = floatval($qty) * floatval($unitPrice);
    //                             $tot2 = floatval($returnQty) * floatval($returnVal);
    //                             $sub = floatval($tot1) - floatval($tot2);
    //                             $customerInvoiceStock->total_price = $sub;
    //                         }
    //                         $customerInvoiceStock->return_qty = $returnQty;
    //                         $customerInvoiceStock->return_price = $returnVal;
    //                         $customerInvoiceStock->updated_at = Carbon::now();
    //                         $customerInvoiceStock->created_at = Carbon::now();
    //                         $customerInvoiceStockSave = $customerInvoiceStock->save();

    //                         //Save user activity
    //                         $userActivity = new UserActivityManagementController();
    //                         $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "New Customer Has Invoice Stock - , Invoice Id - " . $lastCustomerInvoice->id . ", Stock batch Id  -" . $stockId . ", vehicle Id  -" . $deliveryVehicle->dm_delivery_vehicle_id . "Saved");
    //                     }

    //                     $updateRackCustomer = DB::table('pm_customers_has_rack_count')
    //                         ->where('cm_customers_id', $request->customer)
    //                         ->decrement('rack_count', $request->takenRackCount);

    //                     $updateDeliveryVehicle = DB::table('dm_delivery_vehicle')
    //                         ->where('id', $request->deliveryIDToReturn)
    //                         ->increment('unloading_rack_count', $request->takenRackCount);

    //                     $updateRackCustomer1 = DB::table('pm_customers_has_rack_count')
    //                         ->where('cm_customers_id', $request->customer)
    //                         ->increment('rack_count', $request->givenRackCount);

    //                     $updateDeliveryVehicle1 = DB::table('dm_delivery_vehicle')
    //                         ->where('id', $request->deliveryIDToReturn)
    //                         ->decrement('unloading_rack_count', $request->givenRackCount);

    //                     $msg = 'success';
    //                     $InvoiceId = $lastCustomerInvoice->id;
    //                 } else {
    //                     $msg = "error";
    //                     $InvoiceId = '0';
    //                 }

    //                 $status = 'true';
    //                 $msgDB = "success";
    //             }
    //             DB::commit();
    //             return compact('msg', 'InvoiceId', 'status', 'msgDB');
    //         } catch (\Exception $e) {
    //             $msg = 'error';
    //             $status = 'false';
    //             $msgDB = $e->getMessage();
    //             $InvoiceId = 0;

    //             DB::rollback();
    //             return compact('msg', 'InvoiceId', 'status', 'msgDB');
    //         }

    //         // Invoice Type - CASH
    //     } else if ($request->invoiceType == STATIC_DATA_MODEL::$cash) {
    //         DB::beginTransaction();
    //         try {
    //             if (empty($CustomerRackCount)) {
    //                 $status = 'rack';
    //                 $msg = 'rack';
    //             } else {
    //                 $invoiceCustomer = new customerInvoices();
    //                 $invoiceCustomer->invoice_number = $invoiceNumber1;
    //                 $invoiceCustomer->cm_customers_id = $request->customer;
    //                 $invoiceCustomer->invoice_price = $request->subTotal;
    //                 $invoiceCustomer->return_price = $request->totReturn;
    //                 $invoiceCustomer->net_price = $request->subTotal;
    //                 $invoiceCustomer->discount = 0;
    //                 $invoiceCustomer->invoice_status = STATIC_DATA_MODEL::$invoicePending;
    //                 $invoiceCustomer->invoice_type = $request->invoiceType;
    //                 $invoiceCustomer->total_amout_paid = 0.0;
    //                 $invoiceCustomer->given_rack_count = $request->givenRackCount;
    //                 $invoiceCustomer->taken_rack_count = $request->takenRackCount;
    //                 $invoiceCustomer->discount =  $request->subDiscount;
    //                 $invoiceCustomer->display_discount =  $request->displayDiscount;
    //                 $invoiceCustomer->created_at = Carbon::now();
    //                 $invoiceCustomer->updated_at = Carbon::now();
    //                 $invoiceCustomer->created_by = $logged_user;
    //                 $invoiceCustomer->updated_by = $logged_user;
    //                 $invoiceCustomerSave = $invoiceCustomer->save();
    //                 $lastCustomerInvoice = DB::table('dm_customer_invoice')->orderBY('id', 'desc')->first();

    //                 $userActivity = new UserActivityManagementController();
    //                 $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "New Customer Invoice - , Invoice Id - " . $lastCustomerInvoice->id . "Saved");

    //                 if ($invoiceCustomerSave) {

    //                     $output_record = $request->all();
    //                     $output_record = $output_record['invoiceData'];
    //                     $basic_details = $output_record['invoiceDetails'];

    //                     for ($index = 0; $index < count($basic_details); $index++) {
    //                         $rowOfDatas = $basic_details[$index];
    //                         $qty = $rowOfDatas['qty'];
    //                         $returnQty = $rowOfDatas['returnQty'];
    //                         $unitPrice = $rowOfDatas['unitPrice'];
    //                         $stockId = $rowOfDatas['stockId'];
    //                         $realQty = floatval($qty) - floatval($returnQty);
    //                         $vehicleId = $rowOfDatas['vehicleId'];
    //                         $proId = $rowOfDatas['proId'];
    //                         $returnVal = $rowOfDatas['returnVal'];

    //                         $saveDeliveryvehicle =  $vehicleId;

    //                         if ($stockId != 0) {
    //                             $deliveryVehicle = DeliveryVehicleHasStock::where('pm_stock_batch_id', $stockId)->first();
    //                             $updateDelivertyStockQuantity = DeliveryVehicleHasStock::where([['pm_stock_batch_id', $stockId], ['dm_delivery_vehicle_id', $vehicleId]])->decrement('availbale_qty', $qty);
    //                         }

    //                         if ($stockId == 0) {
    //                             $stockId2 = null;
    //                         } else {
    //                             $stockId2 = $stockId;
    //                         }

    //                         $customerInvoiceStock = new customerInvoiceHasStock();
    //                         $customerInvoiceStock->dm_customer_invoice_id = $lastCustomerInvoice->id;
    //                         $customerInvoiceStock->dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id = $vehicleId;
    //                         $customerInvoiceStock->dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id = $stockId2;
    //                         $customerInvoiceStock->pm_product_sub_category_id = $proId;
    //                         $customerInvoiceStock->quantity = $qty;
    //                         $customerInvoiceStock->unit_price = $unitPrice;

    //                         if ($stockId == 0) {
    //                             $customerInvoiceStock->total_price = floatval($returnQty) * floatval($unitPrice);
    //                         } else {
    //                             $tot1 = floatval($qty) * floatval($unitPrice);
    //                             $tot2 = floatval($returnQty) * floatval($returnVal);
    //                             $sub = floatval($tot1) - floatval($tot2);
    //                             $customerInvoiceStock->total_price = $sub;
    //                         }

    //                         $customerInvoiceStock->return_qty = $returnQty;
    //                         $customerInvoiceStock->return_price = $returnVal;
    //                         $customerInvoiceStock->updated_at = Carbon::now();
    //                         $customerInvoiceStock->created_at = Carbon::now();

    //                         $customerInvoiceStockSave = $customerInvoiceStock->save();

    //                         //Save user activity
    //                         $userActivity = new UserActivityManagementController();
    //                         $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "New Customer Has Invoice Stock - , Invoice Id - " . $lastCustomerInvoice->id . ", Stock batch Id  -" . $stockId . ", vehicle Id  -" . $deliveryVehicle->dm_delivery_vehicle_id . "Saved");
    //                     }

    //                     $recNo = invoicePayments::get();
    //                     $receptNumber = '';
    //                     $receptNumber2 = invoicePayments::get();

    //                     if ($receptNumber2->count() == 0) {
    //                         $receptNumber = "REC0001";
    //                     } else {
    //                         $lastRecept = DB::table('dm_payments')->latest('id')->first();
    //                         $last = substr($lastRecept->receipt_no, 6);
    //                         $invo = intval($last) + 1;
    //                         $receptNumber = "REC000" . $invo;
    //                     }

    //                     $nextRecNo = "REC000" . $recNo->count();
    //                     $customerInvoice = customerInvoices::find($lastCustomerInvoice->id);
    //                     $netDiscount = floatval($customerInvoice->discount) +  floatval($customerInvoice->display_discount);
    //                     $netSub = floatval($customerInvoice->net_price) - floatval($netDiscount);
    //                     $updateNetTotal = $customerInvoice->total_amout_paid + $netSub;

    //                     if (invoicePayments::where('receipt_no', $receptNumber)->exists()) {
    //                         $msg = 'already';
    //                         return compact('msg');
    //                     } else if (floatval($updateNetTotal) > floatval($netSub)) {
    //                         $msg = 'totalExceed';
    //                         return compact('msg');
    //                     } else {

    //                         $logged_user = session('logged_user_id');

    //                         $chequeNo = null;
    //                         $chequeDate = null;

    //                         $payment = new invoicePayments();
    //                         $payment->receipt_no = $receptNumber;
    //                         $payment->payment_date = Carbon::now();
    //                         $payment->type = "cash";
    //                         $payment->amount = $netSub;
    //                         $payment->dm_delivery_vehicle_id = $saveDeliveryvehicle;
    //                         $payment->cheque_number = $chequeNo;
    //                         $payment->cheque_date = $chequeDate;
    //                         $payment->is_returned = 0;
    //                         $payment->dm_customer_invoice_id = $lastCustomerInvoice->id;
    //                         $payment->is_active = STATIC_DATA_MODEL::$Active;
    //                         $payment->created_at = Carbon::now();
    //                         $payment->updated_at = Carbon::now();
    //                         $payment->created_by = $logged_user;
    //                         $payment->updated_by = $logged_user;

    //                         $paymentsaved = $payment->save();

    //                         //Get last record user login
    //                         $laspaymentId = DB::table('dm_payments')->orderBY('id', 'desc')->first();

    //                         // Save user activity
    //                         $userActivity = new UserActivityManagementController();
    //                         $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "Invoice payment saved, invoice No: " . $request->invoice . "PaymentId-" . $laspaymentId->id);

    //                         // Update invoice subtotal
    //                         $customerInvoice->update(['total_amout_paid' => $updateNetTotal], ['updated_at', Carbon::now()]);

    //                         $cusInvo = customerInvoices::find($lastCustomerInvoice->id);
    //                         $netDiscountCus = floatval($cusInvo->discount) +  floatval($cusInvo->display_discount);
    //                         $cusInvoNetSub = floatval($cusInvo->net_price) - floatval($netDiscountCus);

    //                         if (floatval($cusInvoNetSub) == floatval($cusInvo->total_amout_paid)) {
    //                             $cusInvo->invoice_status = STATIC_DATA_MODEL::$invoiceCompleted;
    //                             $cusInvo->save();
    //                         }
    //                     }

    //                     $updateRackCustomer = DB::table('pm_customers_has_rack_count')
    //                         ->where('cm_customers_id', $request->customer)
    //                         ->decrement('rack_count', $request->takenRackCount);

    //                     $updateDeliveryVehicle = DB::table('dm_delivery_vehicle')
    //                         ->where('id', $request->deliveryIDToReturn)
    //                         ->increment('unloading_rack_count', $request->takenRackCount);

    //                     $updateRackCustomer1 = DB::table('pm_customers_has_rack_count')
    //                         ->where('cm_customers_id', $request->customer)
    //                         ->increment('rack_count', $request->givenRackCount);

    //                     $updateDeliveryVehicle1 = DB::table('dm_delivery_vehicle')
    //                         ->where('id', $request->deliveryIDToReturn)
    //                         ->decrement('unloading_rack_count', $request->givenRackCount);

    //                     $msg = 'success';
    //                     $InvoiceId = $lastCustomerInvoice->id;
    //                 } else {
    //                     $msg = "error";
    //                     $InvoiceId = '0';
    //                 }
    //                 $status = 'true';
    //                 $msgDB = "success";
    //             }
    //             DB::commit();

    //             return compact('msg', 'InvoiceId', 'status', 'msgDB');
    //         } catch (\Exception $e) {
    //             $msg = 'error';
    //             $status = 'false';
    //             $msgDB = $e->getMessage();
    //             $InvoiceId = 0;

    //             DB::rollback();

    //             return compact('msg', 'InvoiceId', 'status', 'msgDB');
    //         }

    //         // Invoice Type - CHEQUE
    //     } else {
    //         DB::beginTransaction();
    //         try {
    //             if (empty($CustomerRackCount)) {
    //                 $status = 'rack';
    //                 $msg = 'rack';
    //             } else {
    //                 $invoiceCustomer = new customerInvoices();
    //                 $invoiceCustomer->invoice_number = $invoiceNumber1;
    //                 $invoiceCustomer->cm_customers_id = $request->customer;
    //                 $invoiceCustomer->invoice_price = $request->subTotal;
    //                 $invoiceCustomer->return_price = $request->totReturn;
    //                 $invoiceCustomer->net_price = $request->subTotal;
    //                 $invoiceCustomer->discount = 0;
    //                 $invoiceCustomer->invoice_status = STATIC_DATA_MODEL::$invoiceCompleted;
    //                 $invoiceCustomer->invoice_type = $request->invoiceType;
    //                 // $invoiceCustomer->total_amout_paid = $request->subTotal;
    //                 $invoiceCustomer->total_amout_paid = $request->subTotal - ($request->subDiscount + $request->displayDiscount);
    //                 $invoiceCustomer->given_rack_count = $request->givenRackCount;
    //                 $invoiceCustomer->taken_rack_count = $request->takenRackCount;
    //                 $invoiceCustomer->discount =  $request->subDiscount;
    //                 $invoiceCustomer->display_discount =  $request->displayDiscount;
    //                 $invoiceCustomer->created_at = Carbon::now();
    //                 $invoiceCustomer->updated_at = Carbon::now();
    //                 $invoiceCustomer->created_by = $logged_user;
    //                 $invoiceCustomer->updated_by = $logged_user;

    //                 $invoiceCustomerSave = $invoiceCustomer->save();

    //                 // Get last record
    //                 $lastCustomerInvoice = DB::table('dm_customer_invoice')->orderBY('id', 'desc')->first();
    //                 // Save user activity
    //                 $userActivity = new UserActivityManagementController();
    //                 $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "New Customer Invoice - , Invoice Id - " . $lastCustomerInvoice->id . "Saved");

    //                 if ($invoiceCustomerSave) {
    //                     $output_record = $request->all();
    //                     $output_record = $output_record['invoiceData'];
    //                     $basic_details = $output_record['invoiceDetails'];

    //                     for ($index = 0; $index < count($basic_details); $index++) {
    //                         $rowOfDatas = $basic_details[$index];
    //                         $qty = $rowOfDatas['qty'];
    //                         $returnQty = $rowOfDatas['returnQty'];
    //                         $unitPrice = $rowOfDatas['unitPrice'];
    //                         $stockId = $rowOfDatas['stockId'];
    //                         $realQty = floatval($qty) - floatval($returnQty);
    //                         $vehicleId = $rowOfDatas['vehicleId'];
    //                         $proId = $rowOfDatas['proId'];
    //                         $returnVal = $rowOfDatas['returnVal'];

    //                         if ($stockId != 0) {
    //                             $deliveryVehicle = DeliveryVehicleHasStock::where('pm_stock_batch_id', $stockId)->first();
    //                             $updateDelivertyStockQuantity = DeliveryVehicleHasStock::where([['pm_stock_batch_id', $stockId], ['dm_delivery_vehicle_id', $vehicleId]])->decrement('availbale_qty', $qty);
    //                         }

    //                         if ($stockId == 0) {
    //                             $stockId2 = null;
    //                         } else {
    //                             $stockId2 = $stockId;
    //                         }

    //                         $customerInvoiceStock = new customerInvoiceHasStock();
    //                         $customerInvoiceStock->dm_customer_invoice_id = $lastCustomerInvoice->id;
    //                         $customerInvoiceStock->dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id = $vehicleId;
    //                         $customerInvoiceStock->dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id = $stockId2;
    //                         $customerInvoiceStock->pm_product_sub_category_id = $proId;
    //                         $customerInvoiceStock->quantity = $qty;
    //                         $customerInvoiceStock->unit_price = $unitPrice;

    //                         if ($stockId == 0) {
    //                             $customerInvoiceStock->total_price = floatval($returnQty) * floatval($unitPrice);
    //                         } else {
    //                             $tot1 = floatval($qty) * floatval($unitPrice);
    //                             $tot2 = floatval($returnQty) * floatval($returnVal);
    //                             $sub = floatval($tot1) - floatval($tot2);
    //                             $customerInvoiceStock->total_price = $sub;
    //                         }

    //                         $customerInvoiceStock->return_qty = $returnQty;
    //                         $customerInvoiceStock->return_price = $returnVal;
    //                         $customerInvoiceStock->updated_at = Carbon::now();
    //                         $customerInvoiceStock->created_at = Carbon::now();

    //                         $customerInvoiceStockSave = $customerInvoiceStock->save();

    //                         //Save user activity
    //                         $userActivity = new UserActivityManagementController();
    //                         $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "New Customer Has Invoice Stock - , Invoice Id - " . $lastCustomerInvoice->id . ", Stock batch Id  -" . $stockId . ", vehicle Id  -" . $deliveryVehicle->dm_delivery_vehicle_id . "Saved");
    //                     }

    //                     $updateRackCustomer = DB::table('pm_customers_has_rack_count')
    //                         ->where('cm_customers_id', $request->customer)
    //                         ->decrement('rack_count', $request->takenRackCount);

    //                     $updateDeliveryVehicle = DB::table('dm_delivery_vehicle')
    //                         ->where('id', $request->deliveryIDToReturn)
    //                         ->increment('unloading_rack_count', $request->takenRackCount);

    //                     $updateRackCustomer1 = DB::table('pm_customers_has_rack_count')
    //                         ->where('cm_customers_id', $request->customer)
    //                         ->increment('rack_count', $request->givenRackCount);

    //                     $updateDeliveryVehicle1 = DB::table('dm_delivery_vehicle')
    //                         ->where('id', $request->deliveryIDToReturn)
    //                         ->decrement('unloading_rack_count', $request->givenRackCount);

    //                     $msg = 'success';
    //                     $InvoiceId = $lastCustomerInvoice->id;
    //                 } else {
    //                     $msg = "error";
    //                     $InvoiceId = '0';
    //                 }
    //                 $status = 'true';
    //                 $msgDB = "success";
    //             }
    //             DB::commit();
    //             return compact('msg', 'InvoiceId', 'status', 'msgDB');
    //         } catch (\Exception $e) {
    //             $msg = 'error';
    //             $status = 'false';
    //             $msgDB = $e->getMessage();
    //             $InvoiceId = 0;

    //             DB::rollback();
    //             return compact('msg', 'InvoiceId', 'status', 'msgDB');
    //         }
    //     }
    // }


    public function loadInvoicesToCustomer(Request $request)
    {
        $logged_user = session('logged_user_id');
        $invoices = customerInvoices::where([['cm_customers_id', $request->customer], ['created_by', $logged_user], ['invoice_status', STATIC_DATA_MODEL::$invoicePending]])->whereDate('created_at', Carbon::today())->get();

        return view($request->url, compact('invoices'));
    }


    public function loadInvoiceData(Request $request)
    {
        $invoiceData = customerInvoices::find($request->invoiceId);
        $invoicePayments = invoicePayments::where([["dm_customer_invoice_id", $request->invoiceId], ["is_active", STATIC_DATA_MODEL::$Active]])->whereDate('created_at', Carbon::today())->get();

        return view($request->url, compact('invoiceData', 'invoicePayments'));
    }


    public function adminInvoicePrint(Request $request)
    {
        $invoiceData = customerInvoiceHasStock::where('dm_customer_invoice_id', $request->invoiceId)->get();
        $invoice = customerInvoices::find($request->invoiceId);
        $customer = Customer::find($invoice->cm_customers_id);
        $invoiceId = $request->invoiceId;

        return view($request->url, compact('invoiceData', 'invoiceId', 'customer'));
    }

    public function loadRejectedInvoiceDataModal(Request $request)
    {
        $invoiceData = customerInvoiceHasDeletedStock::where('dm_customer_invoice_id', $request->invoiceId)->get();
        $invoice = customerInvoices::find($request->invoiceId);
        $customer = Customer::find($invoice->cm_customers_id);
        $invoiceId = $request->invoiceId;

        return view($request->url, compact('invoiceData', 'invoiceId', 'customer'));
    }


    public function loadInvoicePrintPage($id)
    {
        $invoiceData = customerInvoiceHasStock::where('dm_customer_invoice_id', $id)->get();
        $invoiceCustomer = customerInvoices::find($id);

        return view("distribution.payments.invoice.invoice", compact('invoiceData', 'invoiceCustomer'));
    }


    public function viewReturnModal(Request $request)
    {
        $deliveryId = $request->deliverVehicle;
        $products = SubCategory::where('is_active', STATIC_DATA_MODEL::$Active)->get();

        return view($request->url, compact('products', 'deliveryId'));
    }


    public function checkCreditBilAvailability(Request $request)
    {
        $invoiceCustomer = customerInvoices::where([['cm_customers_id', $request->customer], ['invoice_status', STATIC_DATA_MODEL::$invoicePending], ['invoice_type', STATIC_DATA_MODEL::$credit]])->get();
        $customer = $request->customer;
        $invoiceCustomerAllCreditBills = customerInvoices::where([['cm_customers_id', $request->customer], ['invoice_type', STATIC_DATA_MODEL::$credit]])->where('invoice_status', '!=', 3)->get();
        $customerCreditStatus = false;
        $customerAllCreditStatus = false;
        $invoiceCount = count($invoiceCustomer);

        if (count($invoiceCustomer) > 0) {
            $customerDetails = Customer::find($request->customer);
            $customerCreditStatus = true;
        } else {
            $customerCreditStatus = false;
        }

        if (count($invoiceCustomerAllCreditBills) > 0) {
            $customerAllCreditStatus = true;
            // return view("distribution.ajaxInvoice.ajaxLoadCreditCustomerModal",compact('customerCreditStatus','customer','invoiceCustomer'));
        } else {
            $customerAllCreditStatus = false;
        }
        $rack = '';

        $CustomerRackCount = CustomerRack::where('cm_customers_id', $request->customer)->first();
        if (empty($CustomerRackCount)) {
            $rack = "NO";
        } else {
            $rack = $CustomerRackCount->rack_count;
        }

        // Get selected customer's max credit amount
        $customer_max_credit_amount = DB::table('cm_customers')->select('max_credit_amount')->where('id', '=', $customer)->value('max_credit_amount');
        $maxCreditAmount = $customer_max_credit_amount;

        return compact('customerCreditStatus', 'customer', 'customerAllCreditStatus', 'rack', 'maxCreditAmount');
    }


    public function loadCustomercreditModal(Request $request)
    {
        $invoiceCustomer = customerInvoices::where([['cm_customers_id', $request->customer], ['invoice_status', STATIC_DATA_MODEL::$invoicePending], ['invoice_type', STATIC_DATA_MODEL::$credit]])->get();
        $customer = Customer::find($request->customer);

        return view("distribution.ajaxInvoice.ajaxLoadCreditCustomerModal", compact('customer', 'invoiceCustomer'));
    }


    public function loadAllCreditBillstoTBL(Request $request)
    {
        $invoiceCustomer = customerInvoices::where([['cm_customers_id', $request->customer], ['invoice_type', STATIC_DATA_MODEL::$credit]])->where('invoice_status', '!=', 3)->get();

        return view("distribution.ajaxInvoice.ajaxLoadAllCreditBillsToTBL", compact('invoiceCustomer'));
    }


    public function validateInvoiceType(Request $request)
    {
        $invoiceCustomer = customerInvoices::where([['cm_customers_id', $request->customer], ['invoice_status', STATIC_DATA_MODEL::$invoicePending], ['invoice_type', STATIC_DATA_MODEL::$credit]])->get();
        $msg = '';
        $invoiceCount = count($invoiceCustomer);
        $customer = Customer::find($request->customer);
        $customerCreditStatus = false;

        if ($request->invoiceType == STATIC_DATA_MODEL::$credit) {

            if ((int) $invoiceCount >= (int) $customer->max_credit_bills) {
                $customerCreditStatus = false;
                $msg = 'creditBillExceed';
            } else {

                $dt = Carbon::now();
                $date = $dt->format('Y-m-d');

                $invoiceCustomerGetOldestDate = customerInvoices::where([['cm_customers_id', $request->customer], ['invoice_status', STATIC_DATA_MODEL::$invoicePending], ['invoice_type', STATIC_DATA_MODEL::$credit]])->orderBy('created_at', 'asc')->first();

                if (empty($invoiceCustomerGetOldestDate)) {
                    $msg = 'success';
                    $customerCreditStatus = true;
                } else {

                    $invoiceDateFormat = date('Y-m-d', strtotime($invoiceCustomerGetOldestDate->created_at));

                    $to = Carbon::createFromFormat('Y-m-d', $date);
                    $from = Carbon::createFromFormat('Y-m-d', $invoiceDateFormat);
                    $diff_in_days = $to->diffInDays($from);

                    if ((int) $diff_in_days > (int) $customer->max_credit_bill_availability) {
                        $customerCreditStatus = false;
                        $msg = 'creditBillAvailabilityExceed';
                    } else {
                        $msg = 'success';
                        $customerCreditStatus = true;
                    }
                }
            }
        } else {
            $invoiceCustomerGetOldestDate = customerInvoices::where([['cm_customers_id', $request->customer], ['invoice_status', STATIC_DATA_MODEL::$invoicePending], ['invoice_type', STATIC_DATA_MODEL::$credit]])->orderBy('created_at', 'asc')->first();

            /// diffrerence between dates
            $dt = Carbon::now();
            $date = $dt->format('Y-m-d');

            if (empty($invoiceCustomerGetOldestDate)) {
                $msg = 'success';
                $customerCreditStatus = true;
            } else {

                $invoiceDateFormat = date('Y-m-d', strtotime($invoiceCustomerGetOldestDate->created_at));

                $to = Carbon::createFromFormat('Y-m-d', $date);
                $from = Carbon::createFromFormat('Y-m-d', $invoiceDateFormat);
                $diff_in_days = $to->diffInDays($from);

                if ((int) $diff_in_days > (int) $customer->max_credit_bill_availability) {
                    $customerCreditStatus = false;
                    $msg = 'creditBillAvailabilityExceed';
                } else {
                    $customerCreditStatus = true;
                    $msg = 'success';
                }
            }
        }

        return compact('customerCreditStatus', 'msg');
    }


    public function loadInvoicePaymentDetais(Request $request)
    {
        $invoiceData = customerInvoices::find($request->invoice);

        return view("distribution.ajaxInvoice.ajaxLoadinvoicePayments", compact('invoiceData'));
    }


    public function getInvoices(Request $request)
    {
        $daterange = $request->dateSelect;
        $dateFromFormat = date("Y-m-d", strtotime($daterange));

        $query = DB::table('dm_customer_invoice')
            ->select('dm_customer_invoice.id As InvoiceId')
            ->distinct('dm_customer_invoice.id')
            ->join('dm_customer_invoice_has_stock_batch', 'dm_customer_invoice_has_stock_batch.dm_customer_invoice_id', '=', 'dm_customer_invoice.id')
            ->join("dm_delivery_vehicle_has_stock_batch", function ($join) {
                $join->on("dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id", "=", "dm_customer_invoice_has_stock_batch.dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id");
            })
            ->join('dm_delivery_vehicle', 'dm_delivery_vehicle.id', '=', 'dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id')
            ->where('dm_customer_invoice.invoice_status', '!=', 3); // drop removed invoice

        if ($request->vehicle !== '0') {
            $query->where('dm_delivery_vehicle.vm_vehicles_id', $request->vehicle);
        }
        if ($request->dateSelect !== null) {
            $query->whereDate('dm_customer_invoice.created_at', $dateFromFormat);
        }

        $data = $query->groupBy('dm_customer_invoice.id')->orderBy('dm_customer_invoice.id')->get();

        return view("invoices.ajaxInvoices.ajax_loadInvoices", compact('data'));
    }


    public function adminviewInvoicesIndex(Request $request)
    {
        $vehicles = Vehicles::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        return view("invoices.viewInvoices", compact('vehicles'));
    }


    public function loadInvicesDataToModal(Request $request)
    {
        $invoiceData = customerInvoices::find($request->invoiceId);
        $customer = Customer::find($invoiceData->cm_customers_id);
        $payment = invoicePayments::where('dm_customer_invoice_id', $request->invoiceId)->get();

        return view('distribution.payments.ajaxPayments.LoadpaymentReverseModal', compact('invoiceData', 'customer', 'payment'));
    }





    // Remove Invoice (new)  went wrong *********************************************************************************************
    // public function removeInvoice(Request $request)
    // {
    //     DB::beginTransaction();
    //     try {
    //         // get selected Invoice object
    //         $selectedInvoice_OBJ = customerInvoices::find($request->invoiceId);
    //         $selectedInvoice_OBJ->invoice_status = STATIC_DATA_MODEL::$delete;
    //         $selectedInvoice_OBJ->save();

    //         // get Invoice Payment list
    //         $invoicePayments_list = invoicePayments::where('dm_customer_invoice_id', $request->invoiceId)->get();

    //         if (!$invoicePayments_list->isEmpty()) {
    //             foreach ($invoicePayments_list as $payments) {
    //                 $payments->is_active = STATIC_DATA_MODEL::$delete;
    //                 $payments->save();
    //             }
    //         }

    //         // get  Customer Invoice Has Stock - list
    //         $getStockBatches = customerInvoiceHasStock::where('dm_customer_invoice_id', $request->invoiceId)->get();

    //         if (!$getStockBatches->isEmpty()) {
    //             foreach ($getStockBatches as $stockBatches) {
    //                 if ($stockBatches->dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id != null) {
    //                     $deliveryStockCount = DeliveryVehicleHasStock::where([
    //                         ['dm_delivery_vehicle_id', $stockBatches->dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id],
    //                         ['pm_stock_batch_id', $stockBatches->dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id]
    //                     ])->first();
    //                     $deliveryAvailable = floatval($stockBatches->quantity) + floatval($deliveryStockCount->availbale_qty);
    //                     $updateDelivery = DeliveryVehicleHasStock::where([
    //                         ['dm_delivery_vehicle_id', $stockBatches->dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id],
    //                         ['pm_stock_batch_id', $stockBatches->dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id]
    //                     ])->update(['availbale_qty' => $deliveryAvailable]);

    //                     // Save user activity
    //                     $userActivity = new UserActivityManagementController();
    //                     $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "Update Delivery Vehicle Available - dm_delivery_vehicle_id = " . $stockBatches->dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id . ", Stock ID = " . $stockBatches->dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id . ", Updated Qty = " . $stockBatches->quantity);

    //                     // Save user activity
    //                     $userActivity = new UserActivityManagementController();
    //                     $userActivity->saveActivity(STATIC_DATA_MODEL::$delete, "Removed Customer Has Invoice - dm_delivery_vehicle_id = " . $stockBatches->dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id . ", Stock ID = " . $stockBatches->dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id . ", Return Qty = " . $stockBatches->return_qty . ", Invoice ID = " . $request->invoiceId);
    //                 } else {
    //                     // Save user activity
    //                     $userActivity = new UserActivityManagementController();
    //                     $userActivity->saveActivity(STATIC_DATA_MODEL::$delete, "Removed Customer Has Invoice - dm_delivery_vehicle_id = " . $stockBatches->dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id . ", Stock Id = NULL , Return Qty = " . $stockBatches->return_qty . ", Invoice ID = " . $request->invoiceId);
    //                 }
    //             }
    //         }

    //         DB::commit();
    //         $msg = 'success';
    //         return compact('msg');
    //     } catch (\Exception $e) {
    //         $msg = 'error';
    //         $msgDB = $e->getMessage();
    //         DB::rollback();
    //         return compact('msg', 'msgDB');
    //     }
    // }






    // Delete Invoice (OLD) original -------------------------------------------------------------------------------------------------------------
    // public function deleteInvoice(Request $request)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $invoDataDelete = customerInvoices::find($request->invoiceId);
    //         $invoicePayments = invoicePayments::where('dm_customer_invoice_id', $request->invoiceId)->get();

    //         if (count($invoicePayments) > 0) {

    //             foreach ($invoicePayments as $payments) {
    //                 //Save user activity
    //                 $userActivity = new UserActivityManagementController();
    //                 $userActivity->saveActivity(STATIC_DATA_MODEL::$delete, "Delete Payment - , payment Id - " . $payments->id . ", Price -" . $payments->amount);
    //             }
    //             $deletePayments = invoicePayments::where('dm_customer_invoice_id', $request->invoiceId)->delete();
    //         }

    //         $getStockBatches = customerInvoiceHasStock::where('dm_customer_invoice_id', $request->invoiceId)->get();

    //         foreach ($getStockBatches as $stockBatches) {
    //             if ($stockBatches->dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id !== null) {
    //                 $deliveryStockCount = DeliveryVehicleHasStock::where([['dm_delivery_vehicle_id', $stockBatches->dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id], ['pm_stock_batch_id', $stockBatches->dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id]])->first();
    //                 $deliveryAvailable = floatval($stockBatches->quantity) + floatval($deliveryStockCount->availbale_qty);
    //                 $updateDelivery = DeliveryVehicleHasStock::where([['dm_delivery_vehicle_id', $stockBatches->dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id], ['pm_stock_batch_id', $stockBatches->dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id]])->update(['availbale_qty' => $deliveryAvailable]);

    //                 //Save user activity
    //                 $userActivity = new UserActivityManagementController();
    //                 $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "Update Delivery Vehicle Available - ,dm_delivery_vehicle_id - " . $stockBatches->dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id . ", Stock Id -" . $stockBatches->dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id . ",Updated Qty - " . $stockBatches->quantity);
    //                 //Save user activity
    //                 $userActivity = new UserActivityManagementController();
    //                 $userActivity->saveActivity(STATIC_DATA_MODEL::$delete, "Delete Customer Has Invoice - ,dm_delivery_vehicle_id - " . $stockBatches->dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id . ", Stock Id -" . $stockBatches->dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id . " ,return Qty - " . $stockBatches->return_qty . " ,Invoice ID -" . $request->invoiceId);
    //             } else {
    //                 //Save user activity
    //                 $userActivity = new UserActivityManagementController();
    //                 $userActivity->saveActivity(STATIC_DATA_MODEL::$delete, "Delete Customer Has Invoice - ,dm_delivery_vehicle_id - " . $stockBatches->dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id . ", Stock Id - NULL  ,return Qty - " . $stockBatches->return_qty . " ,Invoice ID -" . $request->invoiceId);
    //             }
    //         }

    //         $deleteInvoices = customerInvoiceHasStock::where('dm_customer_invoice_id', $request->invoiceId)->delete();

    //         if ($deleteInvoices) {
    //             $deleteInvoice = customerInvoices::where('id', $request->invoiceId)->delete();
    //             //Save user activity
    //             $userActivity = new UserActivityManagementController();
    //             $userActivity->saveActivity(STATIC_DATA_MODEL::$delete, "Delete Invoice - ,Invoice Id - " . $request->invoiceId . "," . $invoDataDelete->invoice_number . "," . $invoDataDelete->cm_customers_id . "," . $invoDataDelete->invoice_number . "," . $invoDataDelete->created_at . "," . $invoDataDelete->invoice_number . "," . $invoDataDelete->invoice_price . "," . $invoDataDelete->return_price . "," . $invoDataDelete->net_price . "," . $invoDataDelete->discount . "," . $invoDataDelete->invoice_status . "," . $invoDataDelete->invoice_type . "," . $invoDataDelete->updated_at . "," . $invoDataDelete->total_amout_paid . "," . $invoDataDelete->created_by . "," . $invoDataDelete->updated_by);
    //         }

    //         if ($deleteInvoice) {
    //             $msg = 'success';
    //         } else {
    //             $msg = 'error';
    //         }

    //         DB::commit();
    //         return compact('msg');
    //     } catch (\Exception $e) {
    //         $msg = 'error';
    //         $msgDB = $e->getMessage();
    //         DB::rollback();
    //         return compact('msg', 'msgDB');
    //     }
    //     return compact('msg');
    // }




    // ********************** DELETE INVOICE NEW NEW *****************************
    public function removeInvoice(Request $request)
    {
        $logged_user = session('logged_user_id');

        DB::beginTransaction();
        try {
            $invoDataDelete = customerInvoices::find($request->invoiceId);
            $invoicePayments = invoicePayments::where('dm_customer_invoice_id', $request->invoiceId)->get();

            if (count($invoicePayments) > 0) {
                foreach ($invoicePayments as $payments) {
                    //Save user activity
                    $userActivity = new UserActivityManagementController();
                    $userActivity->saveActivity(STATIC_DATA_MODEL::$delete, "Removed Payment - , payment Id - " . $payments->id . ", Price -" . $payments->amount);
                }
                // DELETE "Invoice Payments" -- change the status to DELETE (3)
                $deletePayments = invoicePayments::where('dm_customer_invoice_id', $request->invoiceId)->update([
                    'is_active' => STATIC_DATA_MODEL::$delete,
                    'updated_at' => Carbon::now(),
                    'updated_by' => $logged_user
                ]);
            }

            //get "Customer Invoice Has Stock Batch"  list
            $getStockBatches = customerInvoiceHasStock::where('dm_customer_invoice_id', $request->invoiceId)->get();

            // update "Delivery Vehicle Has Stock Batch" records
            foreach ($getStockBatches as $stockBatches) {
                if ($stockBatches->dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id !== null) {
                    $deliveryStockCount = DeliveryVehicleHasStock::where([['dm_delivery_vehicle_id', $stockBatches->dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id], ['pm_stock_batch_id', $stockBatches->dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id]])->first();
                    $deliveryAvailable = floatval($stockBatches->quantity) + floatval($deliveryStockCount->availbale_qty);
                    $updateDelivery = DeliveryVehicleHasStock::where([['dm_delivery_vehicle_id', $stockBatches->dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id], ['pm_stock_batch_id', $stockBatches->dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id]])->update(['availbale_qty' => $deliveryAvailable]);

                    //Save user activity
                    $userActivity = new UserActivityManagementController();
                    $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "Updated Delivery Vehicle Available - ,dm_delivery_vehicle_id - " . $stockBatches->dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id . ", Stock Id -" . $stockBatches->dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id . ",Updated Qty - " . $stockBatches->quantity);
                    //Save user activity
                    $userActivity = new UserActivityManagementController();
                    $userActivity->saveActivity(STATIC_DATA_MODEL::$delete, "Removed Customer Has Invoice - ,dm_delivery_vehicle_id - " . $stockBatches->dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id . ", Stock Id -" . $stockBatches->dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id . " ,return Qty - " . $stockBatches->return_qty . " ,Invoice ID -" . $request->invoiceId);
                } else {
                    //Save user activity
                    $userActivity = new UserActivityManagementController();
                    $userActivity->saveActivity(STATIC_DATA_MODEL::$delete, "Removed Customer Has Invoice - ,dm_delivery_vehicle_id - " . $stockBatches->dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id . ", Stock Id - NULL  ,return Qty - " . $stockBatches->return_qty . " ,Invoice ID -" . $request->invoiceId);
                }
            }

            // save deleted stock batches to archive table before delete
            foreach ($getStockBatches as $stockBatches) {
                $customerInvoiceHasDeletedStock = customerInvoiceHasDeletedStock::create([
                    'dm_customer_invoice_id' => $stockBatches->dm_customer_invoice_id,
                    'dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id' => $stockBatches->dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id,
                    'dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id' => $stockBatches->dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id,
                    'pm_product_sub_category_id' => $stockBatches->pm_product_sub_category_id,
                    'quantity' => $stockBatches->quantity,
                    'unit_price' => $stockBatches->unit_price,
                    'total_price' => $stockBatches->total_price,
                    'return_qty' => $stockBatches->return_qty,
                    'return_price' => $stockBatches->return_price,
                    'updated_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                ]);
            }

            // DELETE "Customer Invoice Has Stock Batch" table records -- permanent delete
            $deleteInvoices = customerInvoiceHasStock::where('dm_customer_invoice_id', $request->invoiceId)->delete();

            if ($deleteInvoices) {
                // DELETE "Invoice" -- change invoice_status to DELETE (3)
                $removeInvoice = customerInvoices::where('id', $request->invoiceId)->update([
                    'invoice_status' => STATIC_DATA_MODEL::$invoiceDeleted,
                    'updated_at' => Carbon::now(),
                    'updated_by' => $logged_user
                ]);

                //Save user activity
                $userActivity = new UserActivityManagementController();
                $userActivity->saveActivity(STATIC_DATA_MODEL::$delete, "Removed Invoice - ,Invoice Id - " . $request->invoiceId . "," . $invoDataDelete->invoice_number . "," . $invoDataDelete->cm_customers_id . "," . $invoDataDelete->invoice_number . "," . $invoDataDelete->created_at . "," . $invoDataDelete->invoice_number . "," . $invoDataDelete->invoice_price . "," . $invoDataDelete->return_price . "," . $invoDataDelete->net_price . "," . $invoDataDelete->discount . "," . $invoDataDelete->invoice_status . "," . $invoDataDelete->invoice_type . "," . $invoDataDelete->updated_at . "," . $invoDataDelete->total_amout_paid . "," . $invoDataDelete->created_by . "," . $invoDataDelete->updated_by);
            }

            if ($removeInvoice) {
                $msg = 'success';
            } else {
                $msg = 'error';
            }

            DB::commit();
            return compact('msg');
        } catch (\Exception $e) {
            $msg = 'error';
            $msgDB = $e->getMessage();
            DB::rollback();
            return compact('msg', 'msgDB');
        }
        return compact('msg');
    }

    public function saveOrder(Request $request)
    {
        DB::beginTransaction();
        try {
            $logged_user = session('logged_user_id');
            // Get SalesRep object
            $salesRep_OBJ = SaleRep::where('um_user_id', $logged_user)->first();
            if (!$salesRep_OBJ) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sales representative not found.'
                ]);
            }

            // Create new order taking form
            $orderForm = new OrderTakingForm();
            $orderForm->vm_vehicles_id = $request->vehicle_id;
            $orderForm->vm_sales_reps_id = $salesRep_OBJ->id;
            $orderForm->needed_date = $request->order_date;
            $orderForm->status = STATIC_DATA_MODEL::$orderTakePending;
            $orderForm->created_by = $logged_user;
            $orderForm->updated_by = $logged_user;
            $orderForm->created_at = Carbon::now();
            $orderForm->updated_at = Carbon::now();

            // Save order form first
            $orderForm->save();

            // Save order products
            foreach ($request->quantities as $productId => $quantity) {
                // if ($quantity > 0) {
                $orderProduct = new OrderTakingFormHasProduct();
                $orderProduct->dm_order_taking_form_id = $orderForm->id;
                $orderProduct->pm_product_sub_category_id = $productId;
                $orderProduct->order_qty = $quantity;
                $orderProduct->status = STATIC_DATA_MODEL::$orderTakePending;
                $orderProduct->created_by = $logged_user;
                $orderProduct->updated_by = $logged_user;
                $orderProduct->created_at = Carbon::now();
                $orderProduct->updated_at = Carbon::now();
                $orderProduct->save();
                // }
            }

            // Save user activity
            $userActivity = new UserActivityManagementController();
            $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "Created Order Taking Form - Order ID: " . $orderForm->id);

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Order has been saved successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong while saving the order: ' . $e->getMessage()
            ]);
        }
    }

    public function checkExistingOrder(Request $request)
    {
        try {
            $vehicleId = $request->vehicle_id;
            $orderDate = $request->order_date;

            // Check for existing order
            $existingOrder = OrderTakingForm::where('vm_vehicles_id', $vehicleId)
                ->whereDate('needed_date', $orderDate)
                ->where('status', STATIC_DATA_MODEL::$orderTakePending)
                ->first();

            if ($existingOrder) {
                // Get all products for this order
                $orderProducts = OrderTakingFormHasProduct::where('dm_order_taking_form_id', $existingOrder->id)
                    ->get()
                    ->map(function ($product) {
                        return [
                            'product_id' => $product->pm_product_sub_category_id,
                            'quantity' => $product->order_qty
                        ];
                    })
                    ->pluck('quantity', 'product_id')
                    ->toArray();

                return response()->json([
                    'status' => 'success',
                    'exists' => true,
                    'products' => $orderProducts
                ]);
            }

            return response()->json([
                'status' => 'success',
                'exists' => false,
                'products' => []
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error checking existing order: ' . $e->getMessage()
            ]);
        }
    }

    public function checkOrderDetails(Request $request)
    {
        try {
            $orderDate = $request->input('order_date');


            // Get all orders for the selected date
            $orders = OrderTakingForm::with('products')
                ->where('needed_date', $orderDate)
                ->where('status', STATIC_DATA_MODEL::$orderTakePending)
                ->get();

            $productQuantities = [];

            foreach ($orders as $order) {
                foreach ($order->products as $product) {
                    if (!isset($productQuantities[$product->pm_product_sub_category_id])) {
                        $productQuantities[$product->pm_product_sub_category_id] = [];
                    }
                    $productQuantities[$product->pm_product_sub_category_id][$order->vm_vehicles_id] = $product->order_qty;
                }
            }

            return response()->json([
                'status' => 'success',
                'orders' => $productQuantities
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function loadOrderDetailsAll(Request $request)
    {
        try {
            $orderDate = $request->input('order_date');
            $vehicles = Vehicles::where('is_active', 1)->get();
            $categories = MainCategory::with([
                'subCategories' => function ($query) {
                    $query->where('is_active', 1);
                }
            ])->where('is_active', 1)->get();

            // Get orders for the selected date
            $orders = OrderTakingForm::with('products')
                ->where('needed_date', $orderDate)
                ->where('status', STATIC_DATA_MODEL::$orderTakePending)
                ->get();

            // Organize orders by product and vehicle
            $orderDetails = [];
            foreach ($orders as $order) {
                foreach ($order->products as $product) {
                    if (!isset($orderDetails[$product->pm_product_sub_category_id])) {
                        $orderDetails[$product->pm_product_sub_category_id] = [];
                    }
                    $orderDetails[$product->pm_product_sub_category_id][$order->vm_vehicles_id] = $product->order_qty;
                }
            }

            return view('distribution.ajaxInvoice.ajaxOrderDetailsAll', compact('vehicles', 'categories', 'orderDetails'));
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function loadOrderDetailsSingle(Request $request)
    {
        try {
            $orderDate = $request->input('order_date');
            $vehicleId = $request->input('vehicle');
            $selectedVehicle = Vehicles::find($vehicleId);
            $categories = MainCategory::with([
                'subCategories' => function ($query) {
                    $query->where('is_active', 1);
                }
            ])->where('is_active', 1)->get();

            // Get orders for the selected date and vehicle
            $orders = OrderTakingForm::with('products')
                ->where('needed_date', $orderDate)
                ->where('vm_vehicles_id', $vehicleId)
                ->where('status', STATIC_DATA_MODEL::$orderTakePending)
                ->get();

            // Organize orders by product
            $orderDetails = [];
            foreach ($orders as $order) {
                foreach ($order->products as $product) {
                    $orderDetails[$product->pm_product_sub_category_id] = $product->order_qty;
                }
            }

            return view('distribution.ajaxInvoice.ajaxOrderDetailsSingle', compact('selectedVehicle', 'categories', 'orderDetails'));
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

}
