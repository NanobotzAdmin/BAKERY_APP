<?php

namespace App\Http\Controllers;

use App\User;
use App\Routes;
use App\SaleRep;
use App\Customer;
use App\UserRole;
use Carbon\Carbon;
use App\CustomerRack;
use App\STATIC_DATA_MODEL;
use App\DeliveryVehicle;
use App\customerInvoices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerManagementController extends Controller
{
    public function adminCustomerManagementIndex()
    {
        // get logged User
        $userId = session('logged_user_id');
        $LoggedUser = User::find($userId);
        $salesRep = SaleRep::where('um_user_id', $userId)->first();
        $routes = Routes::where('is_active', STATIC_DATA_MODEL::$Active)->get();

        // return view('customer.customermanagement', compact('LoggedUser', 'customerList', 'routes', 'assignedRoute', 'GG'));
        return view('customer.customermanagement', compact('LoggedUser', 'salesRep', 'routes'));
    }

    // load all Customers Details
    public function loadAllCustomersDetails()
    {
        // get logged User
        $userId = session('logged_user_id');
        $LoggedUser = User::find($userId);
        $salesRep = SaleRep::where('um_user_id', $userId)->first();
        $customerList = Customer::all();
        $assignedRoute = "";
        $GG = 0;

        if (!empty($salesRep) && $LoggedUser->pm_user_role_id == 3) {  // is Sales Rep
            $deliveryVehicle = DeliveryVehicle::where([['vm_sales_reps_id', $salesRep->id], ['status', 1]])->first();
            if (!empty($deliveryVehicle)) {
                $assignedRoute = Routes::find($deliveryVehicle->cm_routes_id);
                if (!empty($assignedRoute)) {
                    $customerList = Customer::where('cm_routes_id', $assignedRoute->id)->where('is_active', 1)->get();
                    $GG = 1;
                }
            }
        }

        return view('customer.ajaxCustomerManagement.loadCustomersToTable', compact('LoggedUser', 'customerList', 'assignedRoute', 'GG'));
    }


    public function adminCustomerRegistrationIndex()
    {
        // get logged User
        $userId = session('logged_user_id');
        $LoggedUser = User::find($userId);
        $routes = Routes::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        return view('customer.saveCustomer', compact('LoggedUser', 'routes'));
    }


    // Save Customer function
    public function saveCustomer(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'contactPerson' => 'required',
            'contactNo' => 'required',
            'deliveryRoute' => 'required|not_in:0',
            'maxCreditBills' => 'required',
            'maxCreditAmount' => 'numeric',
            'maxCreditAvailability' => 'required|numeric',
        ]);

        $customerCreditBills = '';
        $customerCreditAmount = '';
        // if($request->maxCreditBills == ''){
        //     $customerCreditBills= -1;

        // }else{
        //     $customerCreditBills =  $request->maxCreditBills;
        // }

        // if($request->maxCreditAmount == ''){
        //     $customerCreditAmount= -1;

        // }else{
        //     $customerCreditAmount =  $request->maxCreditAmount;
        // }

        $checkDeliveryRoute = true;

        if (Customer::where('customer_name', request('name'))->exists()) {
            session()->flash('message', 'Name already exits!!');
            session()->flash('flash_message_type', 'alert-danger');

            return redirect()->back();
        } else {

            if ($request->routeOrder != '') {
                if (Customer::where([['cm_routes_id', request('deliveryRoute')], ['route_order', request('routeOrder')]])->exists()) {
                    $checkDeliveryRoute = false;
                } else {
                    $checkDeliveryRoute = true;
                }
            } else {
                $checkDeliveryRoute = true;
            }

            if ($checkDeliveryRoute) {
                $logged_user = session('logged_user_id');

                $Customer = new Customer();
                $Customer->customer_name = $request->name;
                $Customer->address = $request->address;
                $Customer->contact_person = $request->contactPerson;
                $Customer->contact_number = $request->contactNo;

                $Customer->email_address = $request->email;
                $Customer->is_active = STATIC_DATA_MODEL::$Active;
                $Customer->created_at = Carbon::now();
                $Customer->updated_at = Carbon::now();
                $Customer->um_user_id = $logged_user;
                $Customer->max_credit_bills = $request->maxCreditBills;
                $Customer->max_credit_bill_availability = $request->maxCreditAvailability;
                $Customer->max_credit_amount = $request->maxCreditAmount;
                $Customer->max_discount = -1;
                $Customer->cm_routes_id = $request->deliveryRoute;
                $Customer->route_order = $request->routeOrder;
                $Customer->latitude = $request->latitude;
                $Customer->longitude = $request->longitude;
                $Customer->location_link = $request->location_link;
                $Customersaved = $Customer->save();

                //Get last record user login
                $lastCusId = DB::table('cm_customers')->latest()->first();

                $customerRack = new CustomerRack();
                $customerRack->cm_customers_id = $lastCusId->id;
                $customerRack->store_rack_count_id = 1;
                $customerRack->rack_count = 0;
                $customerRack->created_at = Carbon::now();
                $customerRack->updated_at = Carbon::now();
                $customerRack->created_by = $logged_user;
                $customerSave = $customerRack->save();

                if (!$Customersaved) {
                    session()->flash('message', '<i class="fa fa-exclamation-circle" style="color: red;"></i> <b>Customer save failed</b> <br> &nbsp;&nbsp;&nbsp; <i>Something went wrong! Customer save error..</i>');
                    session()->flash('flash_message_type', 'alert-danger');

                    return redirect()->back();
                } else {
                    //Save user activity
                    $userActivity = new UserActivityManagementController();
                    $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "New User " . $lastCusId->id . " Saved.");

                    session()->flash('message', '<i class="fa fa-check-circle"></i> <b>Customer create successful</b> <br> &nbsp;&nbsp;&nbsp; <i>New customer details have been successfully saved.</i>');
                    session()->flash('flash_message_type', 'alert-success');
                    return redirect()->back();
                }
            } else {
                session()->flash('message', '<i class="fa fa-exclamation-circle" style="color:orange;"></i> <b>Customer save stopped</b> <br> &nbsp;&nbsp;&nbsp; <i>Customer Route Number already assign for this route!</i>');
                session()->flash('flash_message_type', 'alert-warning');
                return redirect()->back();
            }
        }
    }


    // load specific customer
    public function loadCusDataToModal(Request $request)
    {
        $LoggedUser = User::find(session('logged_user_id'));
        $customer = Customer::find($request->cusID);
        return view('customer.ajaxCustomerManagement.loadCustomerDataToModal', compact('LoggedUser', 'customer'));
    }


    // Update Customer function
    public function updateCustomer(Request $request)
    {
        $this->validate($request, [
            'MODAL_NAME' => 'required',
            'MODAL_CONTACT_PERSON' => 'required',
            'MODAL_CONTACT' => 'required',
            'MODAL_Bill_Availability' => 'required|numeric',
        ]);

        $checkCustomerName = Customer::find($request->MODALcusUpdateId);

        $customerStatus = true;
        if (Customer::where('customer_name', request('MODAL_NAME'))->exists()) {
            if ($checkCustomerName->customer_name == $request->MODAL_NAME) {
                $customerStatus = true;
            } else {
                $customerStatus = false;
                session()->flash('message', 'Customer Name already exits!!');
                session()->flash('flash_message_type', 'alert-danger');
                return redirect()->back();
            }
        }

        if ($customerStatus) {
            $customerUpdate = Customer::find($request->MODALcusUpdateId);
            $customerUpdate->customer_name = $request->MODAL_NAME;
            $customerUpdate->address = $request->MODAL_ADDDRESS;
            $customerUpdate->contact_person = $request->MODAL_CONTACT_PERSON;
            $customerUpdate->contact_number = $request->MODAL_CONTACT;
            $customerUpdate->email_address = $request->MODAL_EMAIL;
            $customerUpdate->max_credit_bill_availability = $request->MODAL_Bill_Availability;
            $customerUpdate->max_credit_bills = $request->MODAL_Bill_Credit;
            $customerUpdate->max_credit_amount = $request->MODAL_Credit_Amount;
            $customerUpdate->cm_routes_id = $request->routeUpdateModal;
            $customerUpdate->latitude = $request->MODAL_Latitude;
            $customerUpdate->longitude = $request->MODAL_Longitude;
            $customerUpdate->location_link = $request->MODAL_Location_Link;

            $customerUpdate->updated_at = Carbon::now();
            $CustomerUpdatesaved = $customerUpdate->save();

            if (!$CustomerUpdatesaved) {
                session()->flash('message', 'Customer Update Failed');
                session()->flash('flash_message_type', 'alert-danger');
                return redirect()->back();
            } else {
                //Save user activity
                $userActivity = new UserActivityManagementController();
                $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "Update User " . $request->MODALcusUpdateId . " Updated.");

                session()->flash('message', '<i class="fa fa-check-circle"></i> <b>Customer update successful</b> <br> &nbsp;&nbsp;&nbsp; <i>Customer details have been successfully updated.</i>');
                session()->flash('flash_message_type', 'alert-success');
                return redirect()->back();
            }
        }
    }


    public function customerdelete(Request $request)
    {
        $customerID = $request->cusID;
        // get Customer Object
        $customer_Obj = Customer::find($customerID);

        // if Customer status is Active
        if ($customer_Obj->is_active == STATIC_DATA_MODEL::$Active) {
            // check customer has Pending CREDIT Invoices
            $invoiceCustomer = customerInvoices::where([['cm_customers_id', $customer_Obj->id], ['invoice_status', STATIC_DATA_MODEL::$invoicePending], ['invoice_type', STATIC_DATA_MODEL::$credit]])->get();

            if (count($invoiceCustomer) > 0) {
                $msg = 'Pending Credit Invoices exists';
            } else {
                $customerStatusUpdate = Customer::find($customer_Obj->id);
                $customerStatusUpdate->is_active = STATIC_DATA_MODEL::$Inactive;
                $customerStatusUpdate->save();
                //Save user activity
                $userActivity = new UserActivityManagementController();
                $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "Deactive Customer Status " . $customer_Obj->id);
                $msg = 'Customer Status Deactivated';
            }
        // if Customer status is Deactive
        } else {
            $customerStatusUpdate = Customer::find($customer_Obj->id);
            $customerStatusUpdate->is_active = STATIC_DATA_MODEL::$Active;
            $customerStatusUpdate->save();
            //Save user activity
            $userActivity = new UserActivityManagementController();
            $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "active Customer Status " . $customer_Obj->id);
            $msg = 'Customer Status Activated';
        }
        return compact('msg');
    }

    public function viewCustomerRackModel(Request $request)
    {
        $CustomerRackCount = CustomerRack::where('cm_customers_id', $request->cusId)->first();
        $cusId = $request->cusId;
        return view('customer.ajaxCustomerManagement.loadCustomerRackModel', compact('CustomerRackCount', 'cusId'));
    }

    public function cutomerRackCountUpdate(Request $request)
    {
        $CustomerRackCount = CustomerRack::where('cm_customers_id', $request->cusId)->first();
        $msg = '';
        $logged_user = session('logged_user_id');
        if (empty($CustomerRackCount)) {
            $customerRack = new CustomerRack();
            $customerRack->cm_customers_id = $request->cusId;
            $customerRack->store_rack_count_id = 1;
            $customerRack->rack_count = $request->rackCount;
            $customerRack->created_at = Carbon::now();
            $customerRack->updated_at = Carbon::now();
            $customerRack->created_by = $logged_user;
            $customerSave = $customerRack->save();
        } else {
            $customerSave = CustomerRack::where('cm_customers_id', $request->cusId)->update([
                'rack_count' => $request->rackCount,
                'updated_at' => Carbon::now(),
            ]);
        }
        if ($customerSave) {
            $msg = 'success';
        } else {
            $msg = 'error';
        }

        return compact('msg');
    }

    public function customerJson()
    {
        $customers = Customer::all();
        return response()->json($customers, 200);
    }
    public function deliveryJson()
    {
        $delivery = DB::table('dm_delivery_vehicle')
            ->select('dm_delivery_vehicle_has_stock_batch.*', 'dm_delivery_vehicle.*')
            ->join('dm_delivery_vehicle_has_stock_batch', 'dm_delivery_vehicle_has_stock_batch.dm_delivery_vehicle_id', '=', 'dm_delivery_vehicle.id')
            ->get();
        return response()->json($delivery, 200);
    }
}
