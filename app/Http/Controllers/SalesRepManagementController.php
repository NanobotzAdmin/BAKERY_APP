<?php

namespace App\Http\Controllers;

use App\User;
use App\SaleRep;
use App\UserLogin;
use Carbon\Carbon;
use App\STATIC_DATA_MODEL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesRepManagementController extends Controller
{

    public function adminSalesRepManagementIndex()
    {
        $saleRepList = SaleRep::all();
        return view('salesRep.salesrepmanagement', compact('saleRepList'));
    }

    // save sales Rep
    public function saveSaleRep(Request $request)
    {
        $this->validate($request, [
            'firstName' => 'required',
            'lastName' => 'required',
            'repNic' => 'required',
            'repContact' => 'required',
            'pass' => 'required|min:4',
            'confirmPass' => 'required|same:pass',
            'uname' => 'required',
        ], [
            'firstName.required' => 'The First Name field is required.',
            'lastName.required' => 'The Last Name field is required.',
            'repNic.required' => 'The NIC field is required.',
            'repContact.required' => 'The Contact Number field is required.',
            'pass.required' => 'The Password field is required.',
            'pass.min' => 'The Password must be at least 4 characters.',
            'confirmPass.required' => 'The Confirm Password field is required.',
            'confirmPass.same' => 'The Confirm Password must match the Password.',
            'uname.required' => 'The Username field is required.',
        ]);

        if (SaleRep::where('nic_no', request('repNic'))->exists()) {
            session()->flash('message', 'Nic already exits!!');
            session()->flash('flash_message_type', 'alert-danger');

            return redirect()->back();
        } else {
            $logged_user = session('logged_user_id');

            $userLogin = new UserLogin();
            $userLogin->user_name = $request->uname;
            $userLogin->password = $request->confirmPass;
            $userLogin->created_at = Carbon::now();
            $userLogin->updated_at = Carbon::now();
            $userLoginsaved = $userLogin->save();

            if ($userLoginsaved) {

                //Get last record user login
                $lastLogId = DB::table('um_user_login')->latest()->first();
                $user = new User();
                $user->first_name = $request->firstName;
                $user->last_name = $request->lastName;
                $user->is_active = STATIC_DATA_MODEL::$Active;
                $user->created_at =  Carbon::now();
                $user->updated_at = Carbon::now();
                $user->um_user_login_id = $lastLogId->id;
                $user->pm_user_role_id = 3;
                $usersaved = $user->save();

                //Get last record user
                $lastUserId = DB::table('um_user')->latest()->first();

                if ($usersaved) {

                    $saleRep = new SaleRep();
                    $saleRep->sales_rep_name = $request->firstName . ' ' . $request->lastName;
                    $saleRep->nic_no = $request->repNic;
                    $saleRep->contact_no = $request->repContact;

                    $saleRep->is_active = STATIC_DATA_MODEL::$Active;
                    $saleRep->created_at = Carbon::now();
                    $saleRep->updated_at = Carbon::now();
                    $saleRep->created_by = $logged_user;
                    $saleRep->um_user_id = $lastUserId->id;

                    $saleRepsaved = $saleRep->save();

                    if ($saleRepsaved) {
                        $lastSaleRepId = DB::table('vm_sales_reps')->latest()->first();
                        //Save user activity
                        $userActivity = new UserActivityManagementController();
                        $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "New sales Rep " . $lastSaleRepId->id . " Saved.");
                        session()->flash('message', 'Sales Rep Save success');
                        session()->flash('flash_message_type', 'alert-success');

                        return redirect()->back();
                    } else {
                        session()->flash('message', 'Sales Rep Save Failed');
                        session()->flash('flash_message_type', 'alert-danger');

                        return redirect()->back();
                    }
                } else {
                    session()->flash('message', 'Sales Rep Save Failed');
                    session()->flash('flash_message_type', 'alert-danger');

                    return redirect()->back();
                }
            } else {
                session()->flash('message', 'Sales Rep Save Failed');
                session()->flash('flash_message_type', 'alert-danger');

                return redirect()->back();
            }
        }
    }


    // load sales rep
    public function loadSaleRepDataToModal(Request $request)
    {
        $saleRep = SaleRep::find($request->RepId);
        $user = User::find($saleRep->um_user_id);
        $login = UserLogin::find($user->um_user_login_id);

        return view('salesRep.ajaxSaleRep.loadSaleRepDataToModal', compact('saleRep', 'login'));
    }

    // update sales rep
    public function updateSaleRep(Request $request)
    {
        $this->validate($request, [
            'MODAL_SALEREP_NAME' => 'required',
            'MODAL_SALEREP_NIC' => 'required',
            'MODAL_SALEREP_CONTACT' => 'required',
            'MODAL_SALEREP_PASSWORD' => 'required|min:4',
        ], [
            'MODAL_SALEREP_NAME.required' => 'The Sales Rep Name field is required.',
            'MODAL_SALEREP_NIC.required' => 'The NIC field is required.',
            'MODAL_SALEREP_CONTACT.required' => 'The Contact Number field is required.',
            'MODAL_SALEREP_PASSWORD.required' => 'The Password field is required.',
            'MODAL_SALEREP_PASSWORD.min' => 'The Password must be at least 4 characters.',
        ]);

        $checkSaleRepame = SaleRep::find($request->MODAL_SALEREP_ID);

        $SaleRepNicStatus = true;
        if (SaleRep::where('nic_no', request('MODAL_SALEREP_NIC'))->exists()) {

            if ($checkSaleRepame->nic_no == $request->MODAL_SALEREP_NIC) {
                $SaleRepNicStatus = true;
            } else {
                $SaleRepNicStatus = false;
                session()->flash('message', 'Sales Rep NIC already exits!!');
                session()->flash('flash_message_type', 'alert-danger');

                return redirect()->back();
            }
        }

        if ($SaleRepNicStatus) {
            $saleRepUpdate = SaleRep::find($request->MODAL_SALEREP_ID);
            $saleRepUpdate->sales_rep_name = $request->MODAL_SALEREP_NAME;
            $saleRepUpdate->nic_no = $request->MODAL_SALEREP_NIC;
            $saleRepUpdate->contact_no = $request->MODAL_SALEREP_CONTACT;
            $saleRepUpdate->updated_at = Carbon::now();

            $saleRepUpdateUpdatesaved = $saleRepUpdate->save();

            $user = User::find($checkSaleRepame->um_user_id);
            $login = UserLogin::find($user->um_user_login_id);

            $login->password = $request->MODAL_SALEREP_PASSWORD;
            $login->save();

            if (!$saleRepUpdateUpdatesaved) {
                session()->flash('message', 'Sales Rep Update Failed');
                session()->flash('flash_message_type', 'alert-danger');

                return redirect()->back();
            } else {

                //Save user activity
                $userActivity = new UserActivityManagementController();
                $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "Update Sales Rep " . $request->MODAL_SALEREP_ID . " Updated.");

                session()->flash('message', 'Sales Rep Update success');
                session()->flash('flash_message_type', 'alert-success');

                return redirect()->back();
            }
        }
    }

    // Delete sales rep
    public function deleteSalesRep($id)
    {
        $salesRep = SaleRep::find($id);

        if ($salesRep->is_active == STATIC_DATA_MODEL::$Active) {
            // if residential status is active
            $salesRepStatusUpdate = SaleRep::find($id);
            $salesRepStatusUpdate->is_active = STATIC_DATA_MODEL::$Inactive;
            $salesRepStatusUpdate->save();

            $userStatusUpdate = User::find($salesRep->um_user_id);
            $userStatusUpdate->is_active = STATIC_DATA_MODEL::$Inactive;
            $userStatusUpdate->save();

            //Save user activity
            $userActivity = new UserActivityManagementController();
            $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "Deactive SalesRep Status " . $id);

            session()->flash('message', 'SalesRep Status Deactivate Success!!');
            session()->flash('flash_message_type', 'alert-success');
        } else {
            // if residential status is inactive
            $salesRepStatusUpdate = SaleRep::find($id);
            $salesRepStatusUpdate->is_active = STATIC_DATA_MODEL::$Active;
            $salesRepStatusUpdate->save();

            $userStatusUpdate = User::find($salesRep->um_user_id);
            $userStatusUpdate->is_active = STATIC_DATA_MODEL::$Active;
            $userStatusUpdate->save();

            //Save user activity
            $userActivity = new UserActivityManagementController();
            $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "active SalesRep Status " . $id);

            session()->flash('message', 'SalesRep Status Active Success!!');
            session()->flash('flash_message_type', 'alert-success');
        }

        return redirect()->back();
    }
}
