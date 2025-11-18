<?php

namespace App\Http\Controllers;

use App\Driver;
use App\STATIC_DATA_MODEL;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DriverManagementController extends Controller
{
    // Index page
    public function adminDriverManagementIndex()
    {
        $driverList = Driver::all();
        return view('driver.driversmanagement', compact('driverList'));
    }


    //// save driver ///////
    public function saveDriver(Request $request)
    {
        $this->validate($request, [
            'driverName' => 'required',
            'licenceNo' => 'required',
            'expiryDate' => 'required',
            'contact' => 'required',
        ], [
            'driverName.required' => 'The driver name is required.',
            'licenceNo.required' => 'The license number is required.',
            'expiryDate.required' => 'The expiry date is required.',
            'contact.required' => 'The contact number is required.',
        ]);

        if (Driver::where('licence_no', request('licenceNo'))->exists()) {
            session()->flash('message', 'Licence No already exits!!');
            session()->flash('flash_message_type', 'alert-danger');

            return redirect()->back();
        } else {
            $logged_user = session('logged_user_id');

            $driver = new Driver();
            $driver->driver_name = $request->driverName;
            $driver->licence_no = $request->licenceNo;
            $driver->licence_expireration = $request->expiryDate;
            $driver->contact_number = $request->contact;

            $driver->is_active = STATIC_DATA_MODEL::$Active;
            $driver->created_at = Carbon::now();
            $driver->updated_at = Carbon::now();
            $driver->created_by = $logged_user;

            $driversaved = $driver->save();

            //Get last record user login
            $lastDriverId = DB::table('vm_drivers')->latest()->first();

            if (!$driversaved) {
                session()->flash('message', 'Driver Save Failed');
                session()->flash('flash_message_type', 'alert-danger');

                return redirect()->back();
            } else {
                //Save user activity
                $userActivity = new UserActivityManagementController();
                $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "New Driver " . $lastDriverId->id . " Saved.");

                session()->flash('message', 'Driver Save success');
                session()->flash('flash_message_type', 'alert-success');

                return redirect()->back();
            }
        }
    }


    ////////// load driver data to modal ////////////
    public function loadDriverDataToModal(Request $request)
    {
        $driverList = Driver::find($request->driverId);

        return view('driver.ajaxDriver.loadDriverDataToModal', compact('driverList'));
    }


    /////////// UPDATE DRIVER
    public function updateDriver(Request $request)
    {
        $this->validate($request, [
            'MODAL_DRIVER_NAME' => 'required',
            'MODAL_DRIVER_LICENCE_NO' => 'required',
            'MODAL_DRIVER_EXPIRY_DATE' => 'required',
            'MODAL_DRIVER_CONTACT' => 'required',
        ], [
            'MODAL_DRIVER_NAME.required' => 'The driver name field is mandatory.',
            'MODAL_DRIVER_LICENCE_NO.required' => 'The license number field cannot be empty.',
            'MODAL_DRIVER_EXPIRY_DATE.required' => 'Please provide the license expiry date.',
            'MODAL_DRIVER_CONTACT.required' => 'The contact number is required.',
        ]);

        $checkDriver = Driver::find($request->MODAL_DRIVER_UPDATE_ID);

        $driverLicenceStatus = true;
        if (Driver::where('licence_no', request('MODAL_DRIVER_LICENCE_NO'))->exists()) {

            if ($checkDriver->licence_no == $request->MODAL_DRIVER_LICENCE_NO) {
                $driverLicenceStatus = true;
            } else {
                $driverLicenceStatus = false;
                session()->flash('message', 'Driver Licence No already exits!!');
                session()->flash('flash_message_type', 'alert-danger');

                return redirect()->back();
            }

        }

        if ($driverLicenceStatus) {
            $driverUpdate = Driver::find($request->MODAL_DRIVER_UPDATE_ID);
            $driverUpdate->driver_name = $request->MODAL_DRIVER_NAME;
            $driverUpdate->licence_no = $request->MODAL_DRIVER_LICENCE_NO;
            $driverUpdate->licence_expireration = $request->MODAL_DRIVER_EXPIRY_DATE;
            $driverUpdate->contact_number = $request->MODAL_DRIVER_CONTACT;
            $driverUpdate->updated_at = Carbon::now();

            $driverUpdatesaved = $driverUpdate->save();

            if (!$driverUpdatesaved) {
                session()->flash('message', 'Driver Update Failed');
                session()->flash('flash_message_type', 'alert-danger');

                return redirect()->back();
            } else {
                //Save user activity
                $userActivity = new UserActivityManagementController();
                $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "Update Driver " . $request->MODAL_DRIVER_UPDATE_ID . " Updated.");

                session()->flash('message', 'Driver Update success');
                session()->flash('flash_message_type', 'alert-success');

                return redirect()->back();
            }
        }
    }


    public function deleteDriver($id)
    {
        $driver = Driver::find($id);

        if ($driver->is_active == STATIC_DATA_MODEL::$Active) {
            // if residential status is active
            $driverStatusUpdate = Driver::find($id);
            $driverStatusUpdate->is_active = STATIC_DATA_MODEL::$Inactive;

            $driverStatusUpdate->save();

            //Save user activity
            $userActivity = new UserActivityManagementController();
            $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "Deactive Driver Status " . $id);

            session()->flash('message', 'Driver Status Deactivate Success!!');
            session()->flash('flash_message_type', 'alert-success');
        } else {
            // if residential status is inactive
            $driverStatusUpdate = Driver::find($id);
            $driverStatusUpdate->is_active = STATIC_DATA_MODEL::$Active;

            $driverStatusUpdate->save();

            //Save user activity
            $userActivity = new UserActivityManagementController();
            $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "active Driver Status " . $id);

            session()->flash('message', 'Driver Status Active Success!!');
            session()->flash('flash_message_type', 'alert-success');
        }

        return redirect()->back();
    }
}
