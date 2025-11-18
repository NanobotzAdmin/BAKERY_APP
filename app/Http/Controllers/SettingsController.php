<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use App\STATIC_DATA_MODEL;
use App\CommissionSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    public function indexCommissionSettings()
    {
        $commissionSettings_list = CommissionSettings::all();
        return view('settingsManagement.commissionSettings', compact('commissionSettings_list'));
    }


    // save Commission Settings
    public function saveCommissionSettings(Request $request)
    {
        // Validations
        $validator = Validator::make($request->all(), [
            'min_sales_amount' => 'required',
            'max_sales_amount' => 'required',
            'commission_rate' => 'required'
        ], [
            'min_sales_amount.required' => 'Please enter Minimum Sales Amount.',
            'max_sales_amount.required' => 'Please enter Maximum Sales Amount.',
            'commission_rate.required' => 'Please enter Commission Rate.'
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
                'type' => 'validation'
            ], 422);
        }

        try {
            // logged user id
            $logged_user = session('logged_user_id');
            $logged_user_OBJ = User::find($logged_user);

            $commissionSettings_OBJ = new CommissionSettings();
            $commissionSettings_OBJ->min_sales_amount = $request->min_sales_amount;
            $commissionSettings_OBJ->max_sales_amount = $request->max_sales_amount;
            $commissionSettings_OBJ->commission_rate = $request->commission_rate;
            $commissionSettings_OBJ->is_active = STATIC_DATA_MODEL::$Active;
            $commissionSettings_OBJ->created_at = Carbon::now();
            $commissionSettings_OBJ->updated_at = Carbon::now();
            $commissionSettings_OBJ->created_by = $logged_user;
            $commissionSettings_OBJ->save();

            // Save user activity
            $userActivity = new UserActivityManagementController();
            $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "New Commission Setting, ID: {$commissionSettings_OBJ->id} was Saved by USER ID: {$logged_user_OBJ->id} ({$logged_user_OBJ->first_name} {$logged_user_OBJ->last_name}).");

            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'New Commission Settings successfully saved.',
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            // Log the error or handle exceptions as needed
            return response()->json([
                'status' => 'error',
                'message' => 'Commission Settings save failed. ' . $e->getMessage(),
                'type' => 'exception'
            ], 500);
        }
    }


    // load Commission Settings data to modal
    public function loadCommissionSettingsToUpdateModal(Request $request)
    {
        $commissionSettings_OBJ = CommissionSettings::find($request->ConfigID);
        return view('settingsManagement.ajaxSettingsManagement.ajaxCommissionSettings', compact('commissionSettings_OBJ'));
    }


    public function updateCommissionSettings(Request $request)
    {
        // Validations
        $validator = Validator::make($request->all(), [
            'min_sales_amount' => 'required',
            'max_sales_amount' => 'required',
            'commission_rate' => 'required'
        ], [
            'min_sales_amount.required' => 'Please enter Minimum Sales Amount.',
            'max_sales_amount.required' => 'Please enter Maximum Sales Amount.',
            'commission_rate.required' => 'Please enter Commission Rate.'
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
                'type' => 'validation'
            ], 422);
        }

        try {
            // logged user id
            $logged_user = session('logged_user_id');
            $logged_user_OBJ = User::find($logged_user);

            // Find Commission Settings by id
            $commissionSettings_OBJ = CommissionSettings::findOrFail($request->ConfigID);

            // Update attributes
            $commissionSettings_OBJ->min_sales_amount = $request->min_sales_amount;
            $commissionSettings_OBJ->max_sales_amount = $request->max_sales_amount;
            $commissionSettings_OBJ->commission_rate = $request->commission_rate;
            $commissionSettings_OBJ->updated_at = Carbon::now();

            // Save the updates
            $commissionSettings_OBJ->save();

            // Save user activity
            $userActivity = new UserActivityManagementController();
            $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "Commission Settings, ID: {$commissionSettings_OBJ->id} was Updated by USER ID: {$logged_user_OBJ->id} ({$logged_user_OBJ->first_name} {$logged_user_OBJ->last_name}).");

            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Commission Settings successfully updated.',
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            // Log the error or handle exceptions as needed
            return response()->json([
                'status' => 'error',
                'message' => 'Commission Settings update failed. ' . $e->getMessage(),
                'type' => 'exception'
            ], 500);
        }
    }





    public function statusChangeCommissionSettings(Request $request)
    {
        // logged user id
        $logged_user = session('logged_user_id');
        $logged_user_OBJ = User::find($logged_user);

        $commissionSettings_OBJ = CommissionSettings::find($request->ConfigID);

        // if Commission Setting status is Active
        if ($commissionSettings_OBJ->is_active == STATIC_DATA_MODEL::$Active) {
            $commissionSettings_OBJ->is_active = STATIC_DATA_MODEL::$Inactive;
            $commissionSettings_OBJ->save();
            //Save user activity
            $userActivity = new UserActivityManagementController();
            $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "Commission Setting, ID: {$commissionSettings_OBJ->id} was Deactivated by USER ID: {$logged_user_OBJ->id} ({$logged_user_OBJ->first_name} {$logged_user_OBJ->last_name}).");
            $msg = 'Deactivated';
        // if Commission Setting status is Deactive
        } else {
            $commissionSettings_OBJ->is_active = STATIC_DATA_MODEL::$Active;
            $commissionSettings_OBJ->save();
            //Save user activity
            $userActivity = new UserActivityManagementController();
            $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "Commission Setting, ID: {$commissionSettings_OBJ->id} was Activated by USER ID: {$logged_user_OBJ->id} ({$logged_user_OBJ->first_name} {$logged_user_OBJ->last_name}).");
            $msg = 'Activated';
        }
        return compact('msg');
    }
}
