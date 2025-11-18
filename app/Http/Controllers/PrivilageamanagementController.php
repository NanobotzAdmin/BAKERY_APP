<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\STATIC_DATA_MODEL;
use Carbon\Carbon;
use App\UserRole;
use App\InterfaceTopics;
use App\Interfaces;
use App\InterfaceComponents;
use App\UserRoleHasInterfaceComponent;
use App\ComponentHistory;
use App\User;
use App\UserHasInterfaceComponent;

class PrivilageamanagementController extends Controller
{
   public function adminPrivilageManagementIndex(){
 $userRoleList = UserRole::where('is_active',STATIC_DATA_MODEL::$Active)->get();
 $interfaceTopicList = InterfaceTopics::all();

 return view('privilages.privillagemanagement',compact('userRoleList','interfaceTopicList'));
   }


////////   user role - load interfaces to inteface topics ////////////

public function loadInterfacesToInterfaceTopics(Request $request){

    $interfaceList = Interfaces::where('pm_interface_topic_id',$request->InterfaceTopic)->get();
    $userRole = $request->userRole;
    return view('privilages.ajaxUserRole.loadInterfacesToInterfaceTopics',compact('interfaceList','userRole'));

}


/////////  user role - Load Components to interface //////////////

public function loadComponentsToInteface(Request $request){

  $componentList = InterfaceComponents::where('pm_interfaces_id',$request->interfaceId)->get();
  $userRole = $request->userRoleId;
  return view('privilages.ajaxUserRole.loadComponentsToInterface',compact('componentList','userRole'));

}

///////// user role - save components ////////////////

public function saveDeleteUserRoleComponent(Request $request){

    global $msg;

     // logged user id
     $logged_user = session('logged_user_id');


      //get interface details
      $interfaceDetails = \DB::table('pm_interface_topic')
      ->select('pm_interface_components.components_name', 'pm_interfaces.interface_name','pm_interfaces.id')
      ->join('pm_interfaces', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
      ->join('pm_interface_components', 'pm_interfaces.id', '=', 'pm_interface_components.pm_interfaces_id')
      ->where('pm_interface_components.id', $request->componentID)

       ->first();

// check already  user Role  has components
    if ($request->checkStatus === 'N') {


        if (UserRoleHasInterfaceComponent::where('pm_interface_components_id', request('componentID'))
                        ->where('pm_user_role_id', request('userRoleId'))
                        ->exists()
        ) {

            $msg = 'User role has already assigned privilage';
            return compact('msg');
        } else {





            $privilageUserRole = new UserRoleHasInterfaceComponent();
            $privilageUserRole->pm_interface_components_id = $request->componentID;
            $privilageUserRole->pm_user_role_id =  $request->userRoleId;
            $privilageUserRole->created_at = Carbon::now();
            $privilageUserRole->updated_at = Carbon::now();
            $privilageUserRole->created_by = $logged_user;

            $privilageUserRole->save();

            $componentHistory = new ComponentHistory();
            $componentHistory->interface_name = $interfaceDetails->interface_name;
            $componentHistory->component_name = $interfaceDetails->components_name;
            $componentHistory->is_added = STATIC_DATA_MODEL::$add;
            $componentHistory->is_removed = STATIC_DATA_MODEL::$remove;
            $componentHistory->pm_interface_components_id = $request->componentID;
            $componentHistory->pm_user_role_id = $request->userRoleId;
            $componentHistory->um_user_id = NULL;
            $componentHistory->created_by = $logged_user;
            $componentHistory->updated_at = Carbon::now();
            $componentHistory->created_at = Carbon::now();
            $componentHistory->save();



            //Save user activity

//Save user activity
$userActivity = new UserActivityManagementController();
$userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "Component according to user role , userRoleId - " . $request->userRoleId . ", componentId -" . $request->componentID . "Saved");


          $user = User::where('pm_user_role_id',$request->userRoleId)->get();


            foreach ($user as $userDetails) {
                $privilageUser = new UserHasInterfaceComponent();
                $privilageUser->um_user_id = $userDetails->id;
                $privilageUser->pm_interface_components_id = $request->componentID;

                $privilageUser->created_at = Carbon::now();
                $privilageUser->updated_at = Carbon::now();

                $privilageUser->created_by = $logged_user;
                $privilageUser->save();


               //Save user activity
$userActivity = new UserActivityManagementController();
$userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "Component according to user , userId - " . $userDetails->id . ", componentId -" . $request->componentID . "Saved");

            }

            $roleId = $interfaceDetails->id;
            $userRole = $request->userRoleId;
            $msg = 'save success';
            return compact('msg', 'roleId','userRole');
        }
    } else {

        \DB::table('pm_user_role_has_interface_components')
                ->where('pm_interface_components_id', request('componentID'))
                ->where('pm_user_role_id', request('userRoleId'))
                ->delete();




                $componentHistory = new ComponentHistory();
                $componentHistory->interface_name = $interfaceDetails->interface_name;
                $componentHistory->component_name = $interfaceDetails->components_name;
                $componentHistory->is_added = STATIC_DATA_MODEL::$remove;
                $componentHistory->is_removed = STATIC_DATA_MODEL::$add;
                $componentHistory->pm_interface_components_id = $request->componentID;
                $componentHistory->pm_user_role_id = $request->userRoleId;
                $componentHistory->um_user_id = NULL;
                $componentHistory->created_by = $logged_user;
                $componentHistory->updated_at = Carbon::now();
                $componentHistory->created_at = Carbon::now();
                $componentHistory->save();

                //Save user activity
$userActivity = new UserActivityManagementController();
$userActivity->saveActivity(STATIC_DATA_MODEL::$delete, "Component according to user role , userRoleId - " . $request->userRoleId . ", componentId -" . $request->componentID . "Deleted");



        $AllUsers = User::where('pm_user_role_id',$request->userRoleId)->get();


        foreach ($AllUsers as $users) {

            \DB::table('um_user_has_interface_components')
                    ->where('um_user_id', $users->id)
                    ->where('pm_interface_components_id', request('componentID'))
                    ->delete();


                        //Save user activity
$userActivity = new UserActivityManagementController();
$userActivity->saveActivity(STATIC_DATA_MODEL::$delete, "Component according to user , userId - " . $users->id . ", componentId -" . $request->componentID . "Deleted");

        }

        $roleId = $interfaceDetails->id;
        $userRole = $request->userRoleId;
        $msg = 'delete success';
        return compact('msg', 'roleId','userRole');

    }

}




}
