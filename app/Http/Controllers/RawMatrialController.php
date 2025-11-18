<?php

namespace App\Http\Controllers;

use App\STATIC_DATA_MODEL;
use App\RawMaterials;
use App\SubCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RawMatrialController extends Controller
{
    public function adminRawMaterialManagementIndex()
    {
        $materialList = RawMaterials::all();
        $products = SubCategory::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        return view('rawMaterials.addNewMaterials', compact('materialList', 'products'));
    }

    public function loadReorderNotifications()
    {
        $materialList = RawMaterials::whereColumn('reorder_count', '>=', 'available_count')->get();
        $notificationCount = count($materialList);

        return view('notification.reorderProAjax.reorderProNotification', compact('materialList', 'notificationCount'));
    }

    // load raw material data to modal //////////

    public function loadMaterialDataToModal(Request $request)
    {
        $rawMaterial = RawMaterials::find($request->materialID);
        $subCategory = SubCategory::where('is_active', STATIC_DATA_MODEL::$Active)->get();

        return view($request->url, compact('rawMaterial', 'subCategory'));
    }

    /// Save Raw Materials

    public function saveMaterial(Request $request)
    {

        $this->validate($request, [
            'materialName' => 'required',
            'product' => 'required|not_in:0',
            'availableCount' => 'required',
            'reorderCount' => 'required',

        ]);

        if (RawMaterials::where(['material_name' => $request->materialName])->exists()) {
            session()->flash('message', 'Material Name already exits!!');
            session()->flash('flash_message_type', 'alert-danger');

            return redirect()->back();
        } else {

            $logged_user = session('logged_user_id');

            $material = new RawMaterials();
            $material->material_name = $request->materialName;
            $material->pm_product_sub_category_id = $request->product;
            $material->is_active = STATIC_DATA_MODEL::$Active;
            $material->created_at = Carbon::now();
            $material->updated_at = Carbon::now();
            $material->added_date = Carbon::now();
            $material->available_count = $request->availableCount;
            $material->reorder_count = $request->reorderCount;
            $material->added_by = $logged_user;

            $materialsaved = $material->save();

            //Get last record user login
            $lastMaterialId = \DB::table('pm_raw_materials')->latest()->first();

            if (!$materialsaved) {
                session()->flash('message', 'Raw Material Save Failed');
                session()->flash('flash_message_type', 'alert-danger');

                return redirect()->back();
            } else {

                //Save user activity
                $userActivity = new UserActivityManagementController();
                $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "New Material " . $lastMaterialId->id . " Saved.");

                session()->flash('message', 'Raw Material Save success');
                session()->flash('flash_message_type', 'alert-success');

                return redirect()->back();
            }

        }
    }

    public function updateMaterial(Request $request)
    {

        $this->validate($request, [
            'Modal_Material_Name' => 'required',
            'Modal_Reorder_Count' => 'required',

        ]);

        $checkDuplicates = RawMaterials::find($request->Modal_Material_Id);
        $checkStatus = false;
        if (RawMaterials::where(['material_name' => $request->Modal_Material_Name])->exists()) {

            if ($checkDuplicates->material_name == $request->Modal_Material_Name) {
                $checkStatus = true;
            } else {
                $checkStatus = false;
            }

        } else {

            $checkStatus = true;
        }

        if ($checkStatus) {

            $updateMaterial = RawMaterials::find($request->Modal_Material_Id);
            $updateMaterial->material_name = $request->Modal_Material_Name;

            $updateMaterial->reorder_count = $request->Modal_Reorder_Count;
            $updateMaterial->updated_at = Carbon::now();
            $updateMaterialStatus = $updateMaterial->save();

            if ($updateMaterialStatus) {

//Save user activity
                $userActivity = new UserActivityManagementController();
                $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "Update Raw Material " . $request->Modal_Material_Id . " Updated.");

                session()->flash('message', 'Raw MAterial Update success');
                session()->flash('flash_message_type', 'alert-success');

                return redirect()->back();

            } else {
                session()->flash('message', 'Raw Material Update Failed');
                session()->flash('flash_message_type', 'alert-danger');

                return redirect()->back();

            }

        } else {

            session()->flash('message', 'Material name alredy exist');
            session()->flash('flash_message_type', 'alert-danger');

            return redirect()->back();
        }

    }

    public function loadReorderProducts()
    {
        $materialList = RawMaterials::whereColumn('reorder_count', '>=', 'available_count')->get();

        return view('rawMaterials.ajaxMaterial.loadReorderProducts', compact('materialList'));
    }

    public function updateRawQuanity(Request $request)
    {

        $this->validate($request, [
            'modalAvailableQty' => 'required',
        ]);

        $incrementdeliveryAvailable = \DB::table('pm_raw_materials')
            ->where('id', $request->materialModalId)
            ->increment('available_count', $request->modalAvailableQty);

        if (!$incrementdeliveryAvailable) {
            session()->flash('message', 'Raw Material Update Failed');
            session()->flash('flash_message_type', 'alert-danger');

            return redirect()->back();
        } else {

            //Save user activity
            $userActivity = new UserActivityManagementController();
            $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "Update Material Available Qty " . $request->materialModalId . " Saved.");

            session()->flash('message', 'Raw Material Update success');
            session()->flash('flash_message_type', 'alert-success');

            return redirect()->back();
        }

    }

    public function loadQuantityUpdateModalMaerials(Request $request)
    {
        $materialID = $request->materialID;
        return view('rawMaterials.ajaxMaterial.loadRawMaterialUpdateModal', compact('materialID'));
    }


    public function updateMaterialQtyNext(Request $request){

        $raw = RawMaterials::find($request->batchId);


        $raw->available_count = (float)$request->qty;
        $rawSave = $raw->save();
        if(!$rawSave){
            $msg = 'error';
        }else{
           $msg = "success";
        }




    return compact('msg');
    }
}
