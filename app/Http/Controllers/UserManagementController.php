<?php

namespace App\Http\Controllers;

use App\User;
use App\Driver;
use App\SaleRep;
use App\UserRole;
use App\UserLogin;
use Carbon\Carbon;
use App\ComponentHistory;
use App\STATIC_DATA_MODEL;
use Illuminate\Http\Request;
use App\UserHasInterfaceComponent;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{
    public function adminUserManagementIndex(Request $request)
    {
        $userRoles = UserRole::where([['is_active', STATIC_DATA_MODEL::$Active], ['id', '!=', 1]])->get();
        $users = User::where('pm_user_role_id', '!=', 1)->get();

        return view('user.userManagement', compact('userRoles', 'users'));
    }

    // save new user
    public function saveUser(Request $request)
    {
        // logged user id
        $logged_user = session('logged_user_id');
        $varientTbl = json_decode($request->hid_va_tbl, true);

        $this->validate($request, [
            'pass' => 'required|min:4',
            'confirmPass' => 'required|same:pass',
            'userRole' => 'required|not_in:0',
        ]);

        if (User::where(['first_name' => $request->fname, 'last_name' => $request->lname])->exists()) {
            session()->flash('message', 'Full Name already exits!!');
            session()->flash('flash_message_type', 'alert-danger');

            return redirect()->back();
        } else {
            $userLogin = new UserLogin();
            $userLogin->user_name = $request->uname;
            $userLogin->password = $request->confirmPass;
            $userLogin->created_at = Carbon::now();
            $userLogin->updated_at = Carbon::now();
            $userLoginsaved = $userLogin->save();
            //Get last record user login
            $lastLogId = DB::table('um_user_login')->latest()->first();

            $user = new User();
            $user->first_name = $request->fname;
            $user->last_name = $request->lname;
            $user->is_active = STATIC_DATA_MODEL::$Active;
            $user->created_at =  Carbon::now();
            $user->updated_at = Carbon::now();
            $user->um_user_login_id = $lastLogId->id;
            $user->pm_user_role_id = $request->userRole;

            if (in_array("1", $varientTbl)) {
                $user->credit_allowed = 1;
            } else {
                $user->credit_allowed = 0;
            }

            if (in_array("2", $varientTbl)) {
                $user->cash_allowed = 1;
            } else {
                $user->cash_allowed = 0;
            }

            if (in_array("3", $varientTbl)) {
                $user->cheque_allowed = 1;
            } else {
                $user->cheque_allowed = 0;
            }

            $usersaved = $user->save();
            $lastUserId = DB::table('um_user')->latest()->first();

            if ($request->userRole == 4) {

                $driver = new Driver();
                $driver->driver_name = $request->fname . ' ' . $request->lname;
                $driver->licence_no = NULL;
                $driver->licence_expireration = NULL;
                $driver->contact_number = NULL;

                $driver->is_active = STATIC_DATA_MODEL::$Active;
                $driver->created_at = Carbon::now();
                $driver->updated_at = Carbon::now();
                $driver->created_by = $logged_user;

                $driversaved = $driver->save();
                //Get last record user login
                $lastDriverId = DB::table('vm_drivers')->latest()->first();
                //Save user activity
                $userActivity = new UserActivityManagementController();
                $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "New Driver " . $lastDriverId->id . " Saved.");
            } else if ($request->userRole == 3) {
                $saleRep = new SaleRep();
                $saleRep->sales_rep_name = $request->fname . ' ' . $request->lname;
                $saleRep->nic_no = NULL;
                $saleRep->contact_no = NULL;

                $saleRep->is_active = STATIC_DATA_MODEL::$Active;
                $saleRep->created_at = Carbon::now();
                $saleRep->updated_at = Carbon::now();
                $saleRep->created_by = $logged_user;
                $saleRep->um_user_id = $lastUserId->id;

                $saleRepsaved = $saleRep->save();
                $lastSaleRepId = DB::table('vm_sales_reps')->latest()->first();
                //Save user activity
                $userActivity = new UserActivityManagementController();
                $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "New sales Rep " . $lastSaleRepId->id . " Saved.");
            }

            if (!$userLoginsaved &&  !$usersaved) {
                session()->flash('message', 'User Registration Failed');
                session()->flash('flash_message_type', 'alert-danger');

                return redirect()->back();
            } else {
                ////////////// SAVE PRIVILAGES ////////
                $lastUser = User::getLastRecord();

                //get interface components according to privilage
                $interfaceComponents = DB::table('pm_user_role_has_interface_components')
                    ->where('pm_user_role_id', request('userRole'))
                    ->get();
                //Save user has interface components
                foreach ($interfaceComponents as $components) {
                    //get interface details
                    $interfaceDetails = DB::table('pm_interface_topic')
                        ->select('pm_interface_components.components_name', 'pm_interfaces.interface_name', 'pm_interfaces.id')
                        ->join('pm_interfaces', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
                        ->join('pm_interface_components', 'pm_interfaces.id', '=', 'pm_interface_components.pm_interfaces_id')
                        ->where('pm_interface_components.id', $components->pm_interface_components_id)
                        ->get();

                    $privilageUser = new UserHasInterfaceComponent();
                    $privilageUser->um_user_id = $lastUser->id;
                    $privilageUser->pm_interface_components_id = $components->pm_interface_components_id;
                    $privilageUser->created_at = Carbon::now();
                    $privilageUser->updated_at = Carbon::now();
                    $privilageUser->created_by = $logged_user;
                    $privilageUser->save();

                    foreach ($interfaceDetails as $details) {
                        $componentHistory = new ComponentHistory();
                        $componentHistory->interface_name = $details->interface_name;
                        $componentHistory->component_name = $details->components_name;
                        $componentHistory->is_added = STATIC_DATA_MODEL::$add;
                        $componentHistory->is_removed = STATIC_DATA_MODEL::$remove;
                        $componentHistory->pm_interface_components_id = $components->pm_interface_components_id;
                        $componentHistory->pm_user_role_id = NULL;
                        $componentHistory->um_user_id = $lastUser->id;
                        $componentHistory->created_by = $logged_user;
                        $componentHistory->updated_at = Carbon::now();
                        $componentHistory->created_at = Carbon::now();
                        $componentHistory->save();
                    }

                    //Save user activity
                    $userActivity = new UserActivityManagementController();
                    $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "Component according to user , userId - " . $lastUser->id . ", componentId -" . $components->pm_interface_components_id . "Saved");
                }
                ///////// END SAVE PRIVILAGES /////////

                //Save user activity
                $userActivity = new UserActivityManagementController();
                $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "New User " . $lastUser->id . " Saved.");

                session()->flash('message', 'User Registration success');
                session()->flash('flash_message_type', 'alert-success');

                return redirect()->back();
            }
        }
    }


    public function viewUserDataToModal(Request $request)
    {
        //get specific UserData
        $userList = User::getUserData('id', $request->userId);
        $userLogin = UserLogin::find($userList->um_user_login_id);
        $userRoles = UserRole::where([['is_active', STATIC_DATA_MODEL::$Active], ['id', '!=', 1]])->get();

        return view('user.ajaxUserManagement.loadUserDataToModal', compact('userList', 'userRoles', 'userLogin'));
    }

    public function updateUserdata(Request $request)
    {
        $updateStatus = 'false';
        $varientTbl = json_decode($request->hid_va_tbl, true);

        $userData =  User::find($request->userId);
        // logged user id
        $logged_user = session('logged_user_id');

        if ($request->fname != '' && $request->lname != '') {
            if (User::where(['first_name' => $request->fname, 'last_name' => $request->lname])->exists()) {
                $FullNameDb = $userData->first_name . $userData->last_name;
                $previewFullName = $request->fname . $request->lname;
                if ($FullNameDb == $previewFullName) {
                    $updateStatus = 'true';
                } else {
                    $updateStatus = 'false';
                }
            } else {
                $updateStatus = 'true';
            }
        } else {
            $updateStatus = 'true';
        }

        if ($updateStatus) {
            $user = User::find($request->userId);
            $userLogin = UserLogin::find($user->um_user_login_id);

            $user->first_name = $request->fname;
            $user->last_name = $request->lname;
            $userLogin->password = $request->password;
            $user->pm_user_role_id = $request->userRole;
            if (in_array("1", $varientTbl)) {
                $user->credit_allowed = 1;
            } else {
                $user->credit_allowed = 0;
            }

            if (in_array("2", $varientTbl)) {
                $user->cash_allowed = 1;
            } else {
                $user->cash_allowed = 0;
            }

            if (in_array("3", $varientTbl)) {
                $user->cheque_allowed = 1;
            } else {
                $user->cheque_allowed = 0;
            }
            $user->updated_at =  Carbon::now();
            $usersaved = $user->save();
            $userLoginSaved = $userLogin->save();

            if (!$usersaved || !$userLoginSaved) {
                $msg = 'error';
                return compact('msg');
            } else {
                ////// UPDATE PRIVILAGES BEGIN ///////
                if ($userData->pm_user_role_id != $request->userRole) {
                    //delete current user has interface components
                    //    \DB::table('um_user_has_interface_components')
                    //    ->where('um_user_id',$request->userId)
                    //    ->delete();

                    //get interface components according to privilage
                    $interfaceComponents = DB::table('pm_user_role_has_interface_components')
                        ->where('pm_user_role_id', $userData->pm_user_role_id)
                        ->get();

                    //Save user has interface components
                    foreach ($interfaceComponents as $components) {
                        //get interface details
                        $interfaceDetails = DB::table('pm_interface_topic')
                            ->select('pm_interface_components.components_name', 'pm_interfaces.interface_name', 'pm_interfaces.id')
                            ->join('pm_interfaces', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
                            ->join('pm_interface_components', 'pm_interfaces.id', '=', 'pm_interface_components.pm_interfaces_id')
                            ->where('pm_interface_components.id', $components->pm_interface_components_id)
                            ->get();

                        //delete current user has interface components
                        DB::table('um_user_has_interface_components')
                            ->where('um_user_id', $request->userId)
                            ->where('pm_interface_components_id', $components->pm_interface_components_id)
                            ->delete();

                        foreach ($interfaceDetails as $details) {

                            $componentHistory = new ComponentHistory();
                            $componentHistory->interface_name = $details->interface_name;
                            $componentHistory->component_name = $details->components_name;
                            $componentHistory->is_added = STATIC_DATA_MODEL::$remove;
                            $componentHistory->is_removed = STATIC_DATA_MODEL::$add;
                            $componentHistory->pm_interface_components_id = $components->pm_interface_components_id;
                            $componentHistory->pm_user_role_id = NULL;
                            $componentHistory->um_user_id = $request->userId;
                            $componentHistory->created_by = $logged_user;
                            $componentHistory->updated_at = Carbon::now();
                            $componentHistory->created_at = Carbon::now();
                            $componentHistory->save();
                        }
                        //Save user activity
                        $userActivity = new UserActivityManagementController();
                        $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "Component according to user , userId - " . $request->userId . ", componentId -" . $components->pm_interface_components_id . "rempved");
                    }

                    //get interface components according to privilage
                    $interfaceComponents2 = DB::table('pm_user_role_has_interface_components')
                        ->where('pm_user_role_id',  $request->userRole)
                        ->get();

                    //Save user has interface components
                    foreach ($interfaceComponents2 as $components2) {
                        //get interface details
                        $interfaceDetails2 = DB::table('pm_interface_topic')
                            ->select('pm_interface_components.components_name', 'pm_interfaces.interface_name', 'pm_interfaces.id')
                            ->join('pm_interfaces', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
                            ->join('pm_interface_components', 'pm_interfaces.id', '=', 'pm_interface_components.pm_interfaces_id')
                            ->where('pm_interface_components.id', $components2->pm_interface_components_id)
                            ->get();

                        $privilageUser = new UserHasInterfaceComponent();
                        $privilageUser->um_user_id = $request->userId;
                        $privilageUser->pm_interface_components_id = $components2->pm_interface_components_id;
                        $privilageUser->created_at = Carbon::now();
                        $privilageUser->updated_at = Carbon::now();
                        $privilageUser->created_by = $logged_user;
                        $privilageUser->save();

                        foreach ($interfaceDetails2 as $details2) {
                            $componentHistory2 = new ComponentHistory();
                            $componentHistory2->interface_name = $details2->interface_name;
                            $componentHistory2->component_name = $details2->components_name;
                            $componentHistory2->is_added = STATIC_DATA_MODEL::$remove;
                            $componentHistory2->is_removed = STATIC_DATA_MODEL::$add;
                            $componentHistory2->pm_interface_components_id = $components2->pm_interface_components_id;
                            $componentHistory2->pm_user_role_id = NULL;
                            $componentHistory2->um_user_id = $request->userId;
                            $componentHistory2->created_by = $logged_user;
                            $componentHistory2->updated_at = Carbon::now();
                            $componentHistory2->created_at = Carbon::now();
                            $componentHistory->save();
                        }
                        //Save user activity
                        $userActivity = new UserActivityManagementController();
                        $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "Component according to user , userId - " . $request->userId . ", componentId -" . $components->pm_interface_components_id . "Saved");
                    }
                }


                $msg = 'success';
                return compact('msg');
            }
        } else {
            $msg = 'fullNameError';
            return compact('msg');
        }
    }


    public function deleteUser($id)
    {
        $user = User::find($id);

        if ($user->is_active == STATIC_DATA_MODEL::$Active) {
            // if residential status is active
            $userStatusUpdate = User::find($id);
            $userStatusUpdate->is_active = STATIC_DATA_MODEL::$Inactive;
            $userStatusUpdate->save();

            if ($user->pm_user_role_id == STATIC_DATA_MODEL::$userrole_salesRep) {
                $salesRep = SaleRep::where("um_user_id", $id)->first();
                $salesRep->is_active = STATIC_DATA_MODEL::$Inactive;
                $salesRep->save();
            }

            //Save user activity
            $userActivity = new UserActivityManagementController();
            $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "Inactive User Status " . $id);

            session()->flash('message', 'User Status Deactivate Success!!');
            session()->flash('flash_message_type', 'alert-success');
        } else {
            // if residential status is inactive
            $userStatusUpdate = User::find($id);
            $userStatusUpdate->is_active = STATIC_DATA_MODEL::$Active;
            $userStatusUpdate->save();

            if ($user->pm_user_role_id == STATIC_DATA_MODEL::$userrole_salesRep) {
                $salesRep = SaleRep::where("um_user_id", $id)->first();
                $salesRep->is_active = STATIC_DATA_MODEL::$Active;
                $salesRep->save();
            }

            //Save user activity
            $userActivity = new UserActivityManagementController();
            $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "active User Status " . $id);

            session()->flash('message', 'User Status Active Success!!');
            session()->flash('flash_message_type', 'alert-success');
        }

        return redirect()->back();
    }
}
