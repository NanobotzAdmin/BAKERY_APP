<?php

namespace App\Http\Controllers;

use App\User;
use App\Driver;
use App\Routes;
use App\SaleRep;
use App\Vehicles;
use App\StoreRack;
use Carbon\Carbon;
use App\StockBatch;
use App\MainCategory;
use App\STATIC_DATA_MODEL;
use App\DeliveryVehicle;
use App\invoicePayments;
use Illuminate\Http\Request;
use App\DeliveryVehicleHasStock;
use Illuminate\Support\Facades\DB;

class VehicleManagementController extends Controller
{

    // view vehicle page
    public function adminVehicleManagementIndex()
    {
        $vehicleList = Vehicles::all();
        return view('vehicle.vehicalmanagement', compact('vehicleList'));
    }

    //save vehicle
    public function saveVehicle(Request $request)
    {

        $this->validate($request, [
            'RegiNo' => 'required',
            'EngineNo' => 'required',
            'ChasiNo' => 'required',

        ]);

        if (Vehicles::where('reg_number', request('RegiNo'))->exists()) {
            session()->flash('message', 'Registration No already exits!!');
            session()->flash('flash_message_type', 'alert-danger');

            return redirect()->back();
        } else if (Vehicles::where('engine_number', request('EngineNo'))->exists()) {

            session()->flash('message', 'Engine No already exits!!');
            session()->flash('flash_message_type', 'alert-danger');

            return redirect()->back();
        } else if (Vehicles::where('chassis_number', request('ChasiNo'))->exists()) {
            session()->flash('message', 'Chassis No already exits!!');
            session()->flash('flash_message_type', 'alert-danger');

            return redirect()->back();
        } else {
            $logged_user = session('logged_user_id');

            $Vehicle = new Vehicles();
            $Vehicle->reg_number = $request->RegiNo;
            $Vehicle->engine_number = $request->EngineNo;
            $Vehicle->chassis_number = $request->ChasiNo;
            $Vehicle->is_active = STATIC_DATA_MODEL::$Active;
            $Vehicle->created_at = Carbon::now();
            $Vehicle->updated_at = Carbon::now();
            $Vehicle->created_by = $logged_user;

            $Vehiclesaved = $Vehicle->save();

            //Get last record user login
            $lastVehicleId = DB::table('vm_vehicles')->latest()->first();

            if (!$Vehiclesaved) {
                session()->flash('message', 'Vehicle Save Failed');
                session()->flash('flash_message_type', 'alert-danger');

                return redirect()->back();
            } else {

                //Save user activity
                $userActivity = new UserActivityManagementController();
                $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "New Vehicle " . $lastVehicleId->id . " Saved.");

                session()->flash('message', 'Vehicle Save success');
                session()->flash('flash_message_type', 'alert-success');

                return redirect()->back();
            }
        }
    }

    // load specific vehicle data to modal
    public function loadVehicleDataToModal(Request $request)
    {
        $vehicle = Vehicles::find($request->vehicleID);

        return view('vehicle.ajaxVehicleManagement.loadVehicleDataToModal', compact('vehicle'));
    }

    // update vehicle
    public function updateVehicle(Request $request)
    {

        $this->validate($request, [
            'MODAL_REGI_NO' => 'required',
            'MODAL_ENGINE_NO' => 'required',
            'MODAL_CHASI_NO' => 'required',

        ]);

        $checkVehiclestatus = Vehicles::find($request->MODAL_VEHICLE_UPDATE_ID);

        $vehicleRegiNoStatus = true;
        $vehicleEngineNoStatus = true;
        $vehicleChassisNoStatus = true;
        if (Vehicles::where('reg_number', request('MODAL_REGI_NO'))->exists()) {

            if ($checkVehiclestatus->reg_number == $request->MODAL_REGI_NO) {
                $vehicleRegiNoStatus = true;
            } else {
                $vehicleRegiNoStatus = false;
                session()->flash('message', 'Registration No already exits!!');
                session()->flash('flash_message_type', 'alert-danger');

                return redirect()->back();
            }
        }

        if (Vehicles::where('engine_number', request('MODAL_ENGINE_NO'))->exists()) {
            if ($checkVehiclestatus->engine_number == $request->MODAL_ENGINE_NO) {
                $vehicleEngineNoStatus = true;
            } else {
                $vehicleEngineNoStatus = false;
                session()->flash('message', 'Engine No already exits!!');
                session()->flash('flash_message_type', 'alert-danger');

                return redirect()->back();
            }
        }

        if (Vehicles::where('chassis_number', request('MODAL_CHASI_NO'))->exists()) {
            if ($checkVehiclestatus->chassis_number == $request->MODAL_CHASI_NO) {
                $vehicleChassisNoStatus = true;
            } else {
                $vehicleChassisNoStatus = false;
                session()->flash('message', 'Chassis No already exits!!');
                session()->flash('flash_message_type', 'alert-danger');

                return redirect()->back();
            }
        }

        if ($vehicleRegiNoStatus && $vehicleEngineNoStatus && $vehicleChassisNoStatus) {
            $vehicleUpdate = Vehicles::find($request->MODAL_VEHICLE_UPDATE_ID);
            $vehicleUpdate->reg_number = $request->MODAL_REGI_NO;
            $vehicleUpdate->engine_number = $request->MODAL_ENGINE_NO;
            $vehicleUpdate->chassis_number = $request->MODAL_CHASI_NO;
            $vehicleUpdate->updated_at = Carbon::now();

            $vehicleUpdatesaved = $vehicleUpdate->save();

            if (!$vehicleUpdatesaved) {
                session()->flash('message', 'Vehicle Update Failed');
                session()->flash('flash_message_type', 'alert-danger');

                return redirect()->back();
            } else {

                //Save user activity
                $userActivity = new UserActivityManagementController();
                $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "Update Vehicle " . $request->MODAL_VEHICLE_UPDATE_ID . " Updated.");

                session()->flash('message', 'Vehicle Update success');
                session()->flash('flash_message_type', 'alert-success');

                return redirect()->back();
            }
        }
    }

    public function changeVehicleStatus(Request $request)
    {
        $vehicle = Vehicles::find($request->vehicle_id);
        
        if ($vehicle) {
            $vehicle->is_active = $request->status;
            $vehicle->updated_at = Carbon::now();
            $vehicle->save();
            
            //Save user activity
            $userActivity = new UserActivityManagementController();
            $statusText = $request->status == STATIC_DATA_MODEL::$Active ? "activated" : "deactivated";
            $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "Vehicle " . $vehicle->id . " " . $statusText . ".");
            
            return response()->json(['success' => true, 'message' => 'Status updated successfully']);
        }
        
        return response()->json(['success' => false, 'message' => 'Vehicle not found']);
    }

    ////// arrange delivery vehicle index view
    public function adminDeliveryVehicleManagementIndex(Request $request)
    {

        $vehicleList = Vehicles::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        $driverList = Driver::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        $salesRepList = SaleRep::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        $deliveryVehicles = DeliveryVehicle::where('status', '!=', STATIC_DATA_MODEL::$deliveryDeleted)->orderBy('id', 'DESC')->paginate(10);
        $routes = Routes::where('is_active', STATIC_DATA_MODEL::$Active)->get();

        return view('vehicle.arrangeVehicle.arrangeDeliveryVehical', compact('vehicleList', 'driverList', 'salesRepList', 'deliveryVehicles', 'routes'));
    }

    /////// save delivery Vehicle
    public function saveDeliveryVehicle(Request $request)
    {
        $this->validate($request, [
            'vehicle' => 'required|not_in:0',
            'driver' => 'required|not_in:0',
            'saleRep' => 'required|not_in:0',
            'deliveryRoute' => 'required|not_in:0',
            'deliveryDate' => 'required',
            'startMilage' => 'required'
        ]);

        $checkDeliveryVehicle = DeliveryVehicle::where([['vm_vehicles_id', $request->vehicle], ['vm_drivers_id', $request->driver], ['vm_sales_reps_id', $request->saleRep], ['status', STATIC_DATA_MODEL::$deliveryLoaded]])->get();

        //     if (DeliveryVehicle::where(['vm_vehicles_id' => $request->vehicle, 'vm_drivers_id' => $request->driver,'vm_sales_reps_id' => $request->saleRep])->exists()) {

        //         session()->flash('message', 'Delivery Vehicle already exits');
        //   session()->flash('flash_message_type', 'alert-danger');

        //   return redirect()->back();
        //     }else

        $countVehicle = DeliveryVehicle::where([['vm_vehicles_id', $request->vehicle], ['status', '!=', STATIC_DATA_MODEL::$deliveryCompleted], ['status', '!=', STATIC_DATA_MODEL::$deliveryDeleted]])->get();
        $coutDriver = DeliveryVehicle::where([['vm_drivers_id', $request->driver], ['status', '!=', STATIC_DATA_MODEL::$deliveryCompleted], ['status', '!=', STATIC_DATA_MODEL::$deliveryDeleted]])->get();
        $countSalesRep = DeliveryVehicle::where([['vm_sales_reps_id', $request->saleRep], ['status', '!=', STATIC_DATA_MODEL::$deliveryCompleted], ['status', '!=', STATIC_DATA_MODEL::$deliveryDeleted]])->get();

        if (count($countVehicle) > 0) {

            session()->flash('message', 'Delivery Vehicle alredy assigned !');
            session()->flash('flash_message_type', 'alert-danger');

            return redirect()->back();
        } else if (count($coutDriver) > 0) {

            session()->flash('message', 'Driver alredy assigned !');
            session()->flash('flash_message_type', 'alert-danger');

            return redirect()->back();
        } else if (count($countSalesRep) > 0) {

            session()->flash('message', 'Sales Rep alredy assigned !');
            session()->flash('flash_message_type', 'alert-danger');

            return redirect()->back();
        } else {

            if (count($checkDeliveryVehicle) != 0) {
                session()->flash('message', 'Delivery Vehicle already loaded !');
                session()->flash('flash_message_type', 'alert-danger');

                return redirect()->back();
            } else {

                $logged_user = session('logged_user_id');

                $Vehicle = new DeliveryVehicle();
                $Vehicle->vm_vehicles_id = $request->vehicle;
                $Vehicle->vm_drivers_id = $request->driver;
                $Vehicle->vm_sales_reps_id = $request->saleRep;
                $Vehicle->created_at = $request->deliveryDate;
                $Vehicle->created_by = $logged_user;
                $Vehicle->status = STATIC_DATA_MODEL::$deliveryPending;
                $Vehicle->updated_at = Carbon::now();
                $Vehicle->updated_by = $logged_user;

                $Vehicle->end_date = null;

                $Vehicle->start_milage = $request->startMilage;
                $Vehicle->end_milage = null;
                $Vehicle->cm_routes_id = $request->deliveryRoute;

                $Vehiclesaved = $Vehicle->save();

                //Get last record user login
                $lastVehicleId = DB::table('dm_delivery_vehicle')->latest()->first();

                if (!$Vehiclesaved) {
                    session()->flash('message', 'Delivery Vehicle saving failed !');
                    session()->flash('flash_message_type', 'alert-danger');

                    return redirect()->back();
                } else {

                    //Save user activity
                    $userActivity = new UserActivityManagementController();
                    $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "Delivery Vehicle " . $lastVehicleId->id . " Saved.");

                    session()->flash('message', 'Delivery Vehicle successfully saved.');
                    session()->flash('flash_message_type', 'alert-success');

                    return redirect()->back();
                }
            }
        }
    }

    public function loadItemsModal(Request $request)
    {
        $category = MainCategory::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        $vehicle = $request->deliveryId;
        $delivery = DeliveryVehicle::find($request->deliveryId);
        $vehicleRegNo = Vehicles::find($delivery->vm_vehicles_id);

        // $deliveryStock = DeliveryVehicleHasStock::where('dm_delivery_vehicle_id', $request->deliveryId)->get(); // old method

        $deliveryStock = DeliveryVehicleHasStock::where('dm_delivery_vehicle_id', $request->deliveryId) // new method
                                                ->join('pm_stock_batch', 'pm_stock_batch.id', '=', 'dm_delivery_vehicle_has_stock_batch.pm_stock_batch_id')
                                                ->join('pm_product_sub_category', 'pm_product_sub_category.id', '=', 'pm_stock_batch.pm_product_sub_category_id')
                                                ->orderByRaw('ISNULL(pm_product_sub_category.sequence_no), pm_product_sub_category.sequence_no ASC') // handles the NULL & NOT-NULL records (NOT-NULL records comes first)
                                                ->get();

        return view('vehicle.arrangeVehicle.ajaxDeliveryVehicle.loadVehicleDataModal', compact('category', 'vehicle', 'deliveryStock', 'vehicleRegNo'));
    }

    public function loadItemsModalUpdate(Request $request)
    {
        $category = MainCategory::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        $vehicle = $request->deliveryId;
        $delivery = DeliveryVehicle::find($request->deliveryId);
        $vehicleRegNo = Vehicles::find($delivery->vm_vehicles_id);

        // $deliveryStock = DeliveryVehicleHasStock::where('dm_delivery_vehicle_id', $request->deliveryId)->get(); // old method
        $deliveryStock = DeliveryVehicleHasStock::where('dm_delivery_vehicle_id', $request->deliveryId) // new method
                                                ->join('pm_stock_batch', 'pm_stock_batch.id', '=', 'dm_delivery_vehicle_has_stock_batch.pm_stock_batch_id')
                                                ->join('pm_product_sub_category', 'pm_product_sub_category.id', '=', 'pm_stock_batch.pm_product_sub_category_id')
                                                ->orderByRaw('ISNULL(pm_product_sub_category.sequence_no), pm_product_sub_category.sequence_no ASC') // handles the NULL & NOT-NULL records (NOT-NULL records comes first)
                                                ->get();
        return view('vehicle.arrangeVehicle.ajaxDeliveryVehicle.loadUpdateData', compact('category', 'vehicle', 'deliveryStock', 'vehicleRegNo'));
    }

    public function loadBatchDetails(Request $request)
    {
        $batchCodes = StockBatch::where([['pm_product_sub_category_id', $request->subCategory], ['available_quantity', '>', 0]])->get();

        return compact('batchCodes');
    }

    public function updateStockRackQuantities(Request $request)
    {
        $output_record = $request->all();

        $output_record = $output_record['deliveryData'];
        $deliveryRack = 0;
        // logged user id
        $logged_user = session('logged_user_id');
        DB::beginTransaction();
        try {

            $storeRck = StoreRack::find(1);
            if (floatval($request->deliveryRackTot) > floatval($storeRck->rack_count)) {

                $msg = 'rack';
            } else {

                $basic_details = $output_record['deliveryDetails'];

                for ($index = 0; $index < count($basic_details); $index++) {

                    $rowOfDatas = $basic_details[$index];
                    $vehicle = $rowOfDatas['vehicle'];
                    $stockBatch = $rowOfDatas['batchId'];
                    // $qty = $rowOfDatas['qty'];
                    //  $rackQty = $rowOfDatas['rackQty'];
                    $updateRackQty = $rowOfDatas['updateRack'];
                    $stock = $rowOfDatas['stock'];
                    $deliveryVehicleUpdatedQty = DeliveryVehicleHasStock::where([['dm_delivery_vehicle_id', $vehicle], ['pm_stock_batch_id', $stockBatch]])->first();

                    if ($updateRackQty != 0) {

                        $updatedRack2 = (float) $deliveryVehicleUpdatedQty->racks_count + (float) $updateRackQty;

                        $updateRack = DeliveryVehicleHasStock::where([['dm_delivery_vehicle_id', $vehicle], ['pm_stock_batch_id', $stockBatch]])->update(['racks_count' => $updatedRack2]);
                        $deliveryRack += $updateRackQty;
                    }
                }
                $incrementRackLoading = DB::table('dm_delivery_vehicle')
                    ->where('id', $request->deliveryId)
                    ->increment('loading_rack_count', $deliveryRack);

                $incrementRackUnLoading = DB::table('dm_delivery_vehicle')
                    ->where('id', $request->deliveryId)
                    ->increment('unloading_rack_count', $deliveryRack);

                $totRack = floatval($storeRck->rack_count) - floatval($request->deliveryRackTot);
                $storeRck2 = StoreRack::find(1);
                $storeRck2->rack_count = $totRack;
                $storeRck2->updated_at = Carbon::now();
                $storeRck2->save();

                $msg = 'sucess';
            }

            DB::commit();
            return compact('msg');
        } catch (\Exception $e) {
            $msg = 'error';
            $status = 'false';
            $msgDB = $e->getMessage();
            $InvoiceId = 0;

            DB::rollback();
            return compact('msg', 'msgDB');
        }
    }

    public function updateStockQuantities(Request $request)
    {
        $output_record = $request->all();

        $output_record = $output_record['deliveryData'];

        // logged user id
        $logged_user = session('logged_user_id');
        DB::beginTransaction();
        try {

            $basic_details = $output_record['deliveryDetails'];

            for ($index = 0; $index < count($basic_details); $index++) {

                $rowOfDatas = $basic_details[$index];
                $vehicle = $rowOfDatas['vehicle'];
                $stockBatch = $rowOfDatas['batchId'];
                $qty = $rowOfDatas['qty'];
                //  $rackQty = $rowOfDatas['rackQty'];
                //    $updateRackQty = $rowOfDatas['updateRack'];
                $stock = $rowOfDatas['stock'];
                $deliveryVehicleUpdatedQty = DeliveryVehicleHasStock::where([['dm_delivery_vehicle_id', $vehicle], ['pm_stock_batch_id', $stockBatch]])->first();

                if ($qty != 0) {
                    //     $subQty =
                    // if($qtyFloat >= (float)$deliveryVehicleUpdatedQty->loaded_qty){

                    //     $deliveryAvailable = (float)$qtyFloat - (float)$deliveryVehicleUpdatedQty->loaded_qty;

                    // }else{

                    //     $deliveryAvailable =(float)$deliveryVehicleUpdatedQty->loaded_qty -  (float)$qtyFloat;

                    // }

                    $incrementdeliveryAvailable = DB::table('dm_delivery_vehicle_has_stock_batch')
                        ->where('dm_delivery_vehicle_id', $vehicle)
                        ->where('pm_stock_batch_id', $stockBatch)
                        ->increment('availbale_qty', $qty);

                    $updateDelivery = DB::table('dm_delivery_vehicle_has_stock_batch')
                        ->where('dm_delivery_vehicle_id', $vehicle)
                        ->where('pm_stock_batch_id', $stockBatch)
                        ->increment('loaded_qty', $qty);

                    // $incrementRack = DB::table('dm_delivery_vehicle_has_stock_batch')
                    // ->where('dm_delivery_vehicle_id', $vehicle)
                    // ->where('pm_stock_batch_id', $stockBatch)
                    // ->increment('racks_count', $updateRackQty);

                    $sumStockDelivery = DeliveryVehicleHasStock::where([['dm_delivery_vehicle_id', $vehicle], ['pm_stock_batch_id', $stockBatch]])->sum('loaded_qty');

                    // $stockavailable= StockBatch::where('id',$stockBatch)->first();

                    $stockAvailableDecre = (float) $sumStockDelivery;

                    $updatestockavailable = DB::table('pm_stock_batch')
                        ->where('id', $stockBatch)
                        ->decrement('available_quantity', $qty);

                    // $updateStockQuantity = StockBatch::find($stockBatch);

                    // $updateStockQuantity->available_quantity = $stockAvailableDecre;
                    // $updateStockQuantity->save();

                }
            }

            $msg = 'sucess';

            DB::commit();
            return compact('msg');
        } catch (\Exception $e) {
            $msg = 'error';
            $status = 'false';
            $msgDB = $e->getMessage();
            $InvoiceId = 0;

            DB::rollback();
            return compact('msg', 'msgDB');
        }
    }

    ////////// save delivery details //////////////

    public function saveDeliveryData(Request $request)
    {

        $output_record = $request->all();

        $output_record = $output_record['deliveryData'];

        // logged user id
        $logged_user = session('logged_user_id');
        DB::beginTransaction();
        try {
            $storeRck = StoreRack::find(1);
            if (floatval($request->deliveryRackTot) > floatval($storeRck->rack_count)) {

                $msg = 'rack';
            } else {

                $basic_details = $output_record['deliveryDetails'];
                $deliveryRack = 0;
                $changeDeliveryStatus = DeliveryVehicle::find($request->deliveryId);
                $changeDeliveryStatus->status = STATIC_DATA_MODEL::$deliveryLoaded;
                $changeDeliveryStatus->save();

                $pastDeliveryStock = DeliveryVehicleHasStock::where('dm_delivery_vehicle_id', $request->deliveryId)->get();

                for ($index = 0; $index < count($basic_details); $index++) {

                    $rowOfDatas = $basic_details[$index];
                    $vehicle = $rowOfDatas['vehicle'];
                    $stockBatch = $rowOfDatas['batchId'];
                    $qty = $rowOfDatas['qty'];
                    $rackQty = $rowOfDatas['rackQty'];
                    //    $updateRackQty = $rowOfDatas['updateRack'];
                    $stock = $rowOfDatas['stock'];

                    $deliveryRack += $rackQty;

                    //  $getPreviousRecord = DeliveryVehicleHasStock::where([['dm_delivery_vehicle_id', $vehicle], ['pm_stock_batch_id', $stockBatch]])->first();

                    $qtyFloat = (float) $stock;
                    $qtyUpdated = (float) $qty;

                    $stockBatchavailable = StockBatch::where('id', $stockBatch)->first();
                    // if (is_null($getPreviousRecord)) {

                    $deliveryVehicleStock = new DeliveryVehicleHasStock();
                    $deliveryVehicleStock->dm_delivery_vehicle_id = $vehicle;
                    $deliveryVehicleStock->pm_stock_batch_id = $stockBatch;
                    $deliveryVehicleStock->loaded_qty = $qtyFloat;
                    $deliveryVehicleStock->availbale_qty = $qtyFloat;
                    $deliveryVehicleStock->racks_count = $rackQty;
                    $deliveryVehicleStock->created_at = Carbon::now();
                    $deliveryVehicleStock->created_by = $logged_user;
                    $deliveryVehicleStock->updated_at = Carbon::now();

                    $deliverySave = $deliveryVehicleStock->save();

                    $updateStockQuantity = StockBatch::find($stockBatch);
                    $updateStockQuantity->decrement('available_quantity', $qtyFloat);

                    //Save user activity
                    $userActivity = new UserActivityManagementController();
                    $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "New Delivery Stock - , vehicle Id - " . $vehicle . ", Stock batch Id  -" . $stockBatch . "Saved");

                    //     } else {

                    //                 $deliveryVehicleUpdatedQty = DeliveryVehicleHasStock::where([['dm_delivery_vehicle_id', $vehicle], ['pm_stock_batch_id', $stockBatch]])->first();

                    //                 if ($qtyUpdated != 0) {
                    // //     $subQty =
                    //                     // if($qtyFloat >= (float)$deliveryVehicleUpdatedQty->loaded_qty){

                    // //     $deliveryAvailable = (float)$qtyFloat - (float)$deliveryVehicleUpdatedQty->loaded_qty;

                    // // }else{

                    // //     $deliveryAvailable =(float)$deliveryVehicleUpdatedQty->loaded_qty -  (float)$qtyFloat;

                    // // }

                    //                     $incrementdeliveryAvailable = DB::table('dm_delivery_vehicle_has_stock_batch')
                    //                         ->where('dm_delivery_vehicle_id', $vehicle)
                    //                         ->where('pm_stock_batch_id', $stockBatch)
                    //                         ->increment('availbale_qty', $qtyUpdated);

                    //                     $updateDelivery = DB::table('dm_delivery_vehicle_has_stock_batch')
                    //                         ->where('dm_delivery_vehicle_id', $vehicle)
                    //                         ->where('pm_stock_batch_id', $stockBatch)
                    //                         ->increment('loaded_qty', $qtyUpdated);

                    //                         $updateRack = DB::table('dm_delivery_vehicle_has_stock_batch')
                    //                         ->where('dm_delivery_vehicle_id', $vehicle)
                    //                         ->where('pm_stock_batch_id', $stockBatch)
                    //                         ->increment('racks_count', $updateRackQty);
                    // // $incrementRack = DB::table('dm_delivery_vehicle_has_stock_batch')
                    //                     // ->where('dm_delivery_vehicle_id', $vehicle)
                    //                     // ->where('pm_stock_batch_id', $stockBatch)
                    //                     // ->increment('racks_count', $updateRackQty);

                    //                //     $updateRack = DeliveryVehicleHasStock::where([['dm_delivery_vehicle_id', $vehicle], ['pm_stock_batch_id', $stockBatch]])->update(['racks_count' => $updatedRack2]);

                    //                     $sumStockDelivery = DeliveryVehicleHasStock::where([['dm_delivery_vehicle_id', $vehicle], ['pm_stock_batch_id', $stockBatch]])->sum('loaded_qty');

                    //                     // $stockavailable= StockBatch::where('id',$stockBatch)->first();

                    //                     $stockAvailableDecre = (float) $sumStockDelivery;

                    //                     $updatestockavailable = DB::table('pm_stock_batch')
                    //                         ->where('id', $stockBatch)
                    //                         ->decrement('available_quantity', $qtyUpdated);

                    //                     // $updateStockQuantity = StockBatch::find($stockBatch);

                    //                     // $updateStockQuantity->available_quantity = $stockAvailableDecre;
                    //                     // $updateStockQuantity->save();

                    //                 }

                    //                 if($updateRackQty != 0){

                    //                     $updatedRack2 = (float) $deliveryVehicleUpdatedQty->racks_count + (float) $updateRackQty;

                    //               $updateRack = DeliveryVehicleHasStock::where([['dm_delivery_vehicle_id', $vehicle], ['pm_stock_batch_id', $stockBatch]])->update(['racks_count' => $updatedRack2]);
                    //               $deliveryRack +=  $updateRackQty;
                    //                 }

                    //
                    // $deletePreviousRecord = DeliveryVehicleHasStock::where([['dm_delivery_vehicle_id', $vehicle],['pm_stock_batch_id',$stockBatch]])->delete();
                    //  }
                }

                $incrementRackLoading = DB::table('dm_delivery_vehicle')
                    ->where('id', $request->deliveryId)
                    ->increment('loading_rack_count', $deliveryRack);

                $incrementRackUnLoading = DB::table('dm_delivery_vehicle')
                    ->where('id', $request->deliveryId)
                    ->increment('unloading_rack_count', $deliveryRack);

                $totRack = floatval($storeRck->rack_count) - floatval($request->deliveryRackTot);
                $storeRck2 = StoreRack::find(1);
                $storeRck2->rack_count = $totRack;
                $storeRck2->updated_at = Carbon::now();
                $storeRck2->save();

                $msg = 'sucess';
            }

            DB::commit();
            return compact('msg');
        } catch (\Exception $e) {
            $msg = 'error';
            $status = 'false';
            $msgDB = $e->getMessage();
            $InvoiceId = 0;

            DB::rollback();
            return compact('msg', 'msgDB');
        }
    }

    public function completeDelivery(Request $request)
    {
        $deliverystock = DeliveryVehicleHasStock::where('dm_delivery_vehicle_id', $request->deliveryId)->get();

        foreach ($deliverystock as $delivery) {
            $qty = (float) $delivery->availbale_qty;
            $stock = StockBatch::find($delivery->pm_stock_batch_id);

            $stockalculation = $stock->increment('available_quantity', $qty);
        }

        $deliveryVehicle = DeliveryVehicle::find($request->deliveryId);
        $incrementRackUnLoading = DB::table('pm_store_rack_count')
            ->where('id', 1)
            ->increment('rack_count', $deliveryVehicle->unloading_rack_count);

        $changeDeliveryStatus = DeliveryVehicle::find($request->deliveryId);
        $changeDeliveryStatus->status = STATIC_DATA_MODEL::$deliveryCompleted;
        $changeDeliveryStatus->end_date = Carbon::now();
        $changeDeliveryStatus->end_milage = $request->endMilage;
        $changeStatus2 = $changeDeliveryStatus->save();

        //Save user activity
        $userActivity = new UserActivityManagementController();
        $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "Complte Delivery  - , delivery Id - " . $request->deliveryId . "Completed");

        if ($changeStatus2) {
            $msg = 'sucess';
            return compact('msg');
        } else {
            $msg = 'error';
            return compact('msg');
        }
    }


    // LOAD - Update Modal of Unloadings & Returns
    public function loadUpdateModalOfUnloadingsAndReturns(Request $request)
    {
        $vehicle = $request->deliveryId;
        $delivery = DeliveryVehicle::find($request->deliveryId);
        $vehicleRegNo = Vehicles::find($delivery->vm_vehicles_id);
        // $deliveryStock = DeliveryVehicleHasStock::where('dm_delivery_vehicle_id', $request->deliveryId)->get(); // old method
        $deliveryStock = DeliveryVehicleHasStock::where('dm_delivery_vehicle_id', $request->deliveryId) // new method
                                                ->join('pm_stock_batch', 'pm_stock_batch.id', '=', 'dm_delivery_vehicle_has_stock_batch.pm_stock_batch_id')
                                                ->join('pm_product_sub_category', 'pm_product_sub_category.id', '=', 'pm_stock_batch.pm_product_sub_category_id')
                                                ->orderByRaw('ISNULL(pm_product_sub_category.sequence_no), pm_product_sub_category.sequence_no ASC') // handles the NULL & NOT-NULL records (NOT-NULL records comes first)
                                                ->get();
        $route = Routes::find($delivery->cm_routes_id);

        // old
        // $deliveryInvoice = DB::select("SELECT `dm_customer_invoice_has_stock_batch`.`dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id`
        //                                     , `dm_customer_invoice_has_stock_batch`.`pm_product_sub_category_id`
        //                                     , `dm_customer_invoice_has_stock_batch`.`dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id` AS stockBatchID
        //                                     , SUM(`dm_customer_invoice_has_stock_batch`.`return_qty`) AS sumQty
        //                                     , `dm_delivery_vehicle_has_stock_batch`.`system_return_qty`
        //                                     , `dm_delivery_vehicle_has_stock_batch`.`physical_return_qty`
        //                                 FROM
        //                                     `dm_delivery_vehicle_has_stock_batch`
        //                                     INNER JOIN `dm_delivery_vehicle`
        //                                         ON (`dm_delivery_vehicle_has_stock_batch`.`dm_delivery_vehicle_id` = `dm_delivery_vehicle`.`id`)
        //                                     INNER JOIN `dm_customer_invoice_has_stock_batch`
        //                                         ON (`dm_customer_invoice_has_stock_batch`.`dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id` = `dm_delivery_vehicle_has_stock_batch`.`dm_delivery_vehicle_id`)
        //                                         AND (`dm_customer_invoice_has_stock_batch`.`dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id` = `dm_delivery_vehicle_has_stock_batch`.`pm_stock_batch_id`)
        //                                     INNER JOIN `pm_stock_batch`
        //                                         ON (`dm_delivery_vehicle_has_stock_batch`.`pm_stock_batch_id` = `pm_stock_batch`.`id`)
        //                                     INNER JOIN `dm_customer_invoice`
        //                                         ON (`dm_customer_invoice_has_stock_batch`.`dm_customer_invoice_id` = `dm_customer_invoice`.`id`)
        //                                 WHERE (`dm_customer_invoice_has_stock_batch`.`dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id` = ? )
        //                                 GROUP BY `dm_customer_invoice_has_stock_batch`.`pm_product_sub_category_id`", [$request->deliveryId]);

        // new
        $deliveryInvoice = DB::select("SELECT `dm_customer_invoice_has_stock_batch`.`dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id`
                                            , `dm_customer_invoice_has_stock_batch`.`pm_product_sub_category_id`
                                            , `dm_customer_invoice_has_stock_batch`.`dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id` AS stockBatchID
                                            , SUM(`dm_customer_invoice_has_stock_batch`.`return_qty`) AS sumQty
                                            , `dm_delivery_vehicle_has_stock_batch`.`system_return_qty`
                                            , `dm_delivery_vehicle_has_stock_batch`.`physical_return_qty`
                                        FROM
                                            `dm_delivery_vehicle_has_stock_batch`
                                            INNER JOIN `dm_delivery_vehicle`
                                                ON (`dm_delivery_vehicle_has_stock_batch`.`dm_delivery_vehicle_id` = `dm_delivery_vehicle`.`id`)
                                            INNER JOIN `dm_customer_invoice_has_stock_batch`
                                                ON (`dm_customer_invoice_has_stock_batch`.`dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id` = `dm_delivery_vehicle_has_stock_batch`.`dm_delivery_vehicle_id`)
                                                AND (`dm_customer_invoice_has_stock_batch`.`dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id` = `dm_delivery_vehicle_has_stock_batch`.`pm_stock_batch_id`)
                                            INNER JOIN `pm_stock_batch`
                                                ON (`dm_delivery_vehicle_has_stock_batch`.`pm_stock_batch_id` = `pm_stock_batch`.`id`)
                                            INNER JOIN `dm_customer_invoice`
                                                ON (`dm_customer_invoice_has_stock_batch`.`dm_customer_invoice_id` = `dm_customer_invoice`.`id`)
                                            INNER JOIN `pm_product_sub_category`
                                                ON (`pm_stock_batch`.`pm_product_sub_category_id` = `pm_product_sub_category`.`id`)
                                        WHERE (`dm_customer_invoice_has_stock_batch`.`dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id` = ?)
                                        GROUP BY `dm_customer_invoice_has_stock_batch`.`pm_product_sub_category_id`
                                        ORDER BY ISNULL(`pm_product_sub_category`.`sequence_no`), `pm_product_sub_category`.`sequence_no` ASC", [$request->deliveryId]);

        return view('vehicle.arrangeVehicle.ajaxDeliveryVehicle.loadUpdateModalOfUnloadingsAndReturns', compact('vehicle', 'deliveryStock', 'delivery', 'vehicleRegNo', 'route', 'deliveryInvoice'));
    }

    // UPDATE Return Quantities
    public function updateReturns(Request $request)
    {
        $logged_user = session('logged_user_id');
        $deliveryId = $request->deliveryID;
        $tableData = $request->dataString;
        $tableData_arr = json_decode($tableData);
        $isUpdated = (bool) true;
        //get array size
        $tableData_arr_SIZE = count($tableData_arr);
        // get Objects
        $logged_userOBJ = User::find($logged_user);
        $deliVehicleOBJ = DeliveryVehicle::find($deliveryId);
        $vehicleOBJ = Vehicles::find($deliVehicleOBJ->vm_vehicles_id);

        // looping the table data for saving
        for ($i = 0; $i <= $tableData_arr_SIZE - 1; $i++) {
            // declare "Return Physical Qty" input value
            $physicalReturnQty_input = null;
            // if input val is NOT empty
            if (is_numeric($tableData_arr[$i]->physicalReturnQty)) {
                $physicalReturnQty_input = $tableData_arr[$i]->physicalReturnQty;
            }

            $deliveryVehicleHasStock = DeliveryVehicleHasStock::where(['dm_delivery_vehicle_id' => $deliveryId, 'pm_stock_batch_id' => $tableData_arr[$i]->stockBatchID])
                ->update(array(
                    'system_return_qty' => $tableData_arr[$i]->systemReturnQty,
                    'physical_return_qty' => $physicalReturnQty_input,
                    'updated_at' => Carbon::now()
                ));

            if ($deliveryVehicleHasStock != null) {
                $isUpdated = True;
            } else {
                $isUpdated = false;
            }
        }

        //if all saved successfully
        if ($isUpdated) {
            // Session activity
            $userActivity = new UserActivityManagementController();
            $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, 'Return Quantities of the Delivery vehicle: "' . $vehicleOBJ->reg_number . '" on "' . date('Y-m-d',strtotime($deliVehicleOBJ->created_at)) . '" has been updated by ' . $logged_userOBJ->first_name . ' ' . $logged_userOBJ->last_name . ' on ' . Carbon::now() . '.');

            $msg = 'sucess';

            return compact('msg');
        } else {
            $msg = 'error';

            return compact('msg');
        }
    }


    // UPDATE Unloading Quantities
    public function updateUnloadings(Request $request)
    {
        $logged_user = session('logged_user_id');
        $deliveryId = $request->deliveryID;
        $tableData = $request->dataString;
        $tableData_arr = json_decode($tableData);
        $isUpdated = (bool) true;
        //get array size
        $tableData_arr_SIZE = count($tableData_arr);
        // get Objects
        $logged_userOBJ = User::find($logged_user);
        $deliVehicleOBJ = DeliveryVehicle::find($deliveryId);
        $vehicleOBJ = Vehicles::find($deliVehicleOBJ->vm_vehicles_id);

        // loop table data for saving
        for ($i = 0; $i <= $tableData_arr_SIZE - 1; $i++) {
            // declare "Physical Unloading Qty" input value
            $physicalUnloadingQty_input = null;
            // if input val is NOT empty
            if (is_numeric($tableData_arr[$i]->physicalUnloadingQty)) {
                $physicalUnloadingQty_input = $tableData_arr[$i]->physicalUnloadingQty;
            }

            $deliveryVehicleHasStock = DeliveryVehicleHasStock::where(['dm_delivery_vehicle_id' => $deliveryId, 'pm_stock_batch_id' => $tableData_arr[$i]->stockBatchID])
                ->update(array(
                    'physical_unloading_qty' => $physicalUnloadingQty_input,
                    'updated_at' => Carbon::now()
                ));

            if ($deliveryVehicleHasStock != null) {
                $isUpdated = True;
            } else {
                $isUpdated = false;
            }
        }

        //if all saved successfully
        if ($isUpdated) {
            // Session activity
            $userActivity = new UserActivityManagementController();
            $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, 'Unloading Quantities of the Delivery vehicle: "' . $vehicleOBJ->reg_number . '" on "' . date('Y-m-d',strtotime($deliVehicleOBJ->created_at)) . '" has been updated by ' . $logged_userOBJ->first_name . ' ' . $logged_userOBJ->last_name . ' on ' . Carbon::now() . '.');
            $msg = 'sucess';
            return compact('msg');
        } else {
            $msg = 'error';
            return compact('msg');
        }
    }


    public function viewDeliveryData(Request $request)
    {
        $vehicle = $request->deliveryId;
        $delivery = DeliveryVehicle::find($request->deliveryId);
        $vehicleRegNo = Vehicles::find($delivery->vm_vehicles_id);
        // $deliveryStock = DeliveryVehicleHasStock::where('dm_delivery_vehicle_id', $request->deliveryId)->get(); // old method
        $deliveryStock = DeliveryVehicleHasStock::where('dm_delivery_vehicle_id', $request->deliveryId) // new method
                                                ->join('pm_stock_batch', 'pm_stock_batch.id', '=', 'dm_delivery_vehicle_has_stock_batch.pm_stock_batch_id')
                                                ->join('pm_product_sub_category', 'pm_product_sub_category.id', '=', 'pm_stock_batch.pm_product_sub_category_id')
                                                ->orderByRaw('ISNULL(pm_product_sub_category.sequence_no), pm_product_sub_category.sequence_no ASC') // handles the NULL & NOT-NULL records (NOT-NULL records comes first)
                                                ->get();
        // $invoiceData = customerInvoiceHasStock::where('dm_customer_invoice_id', $request->invoiceId)->get();

        // old
        // $deliveryInvoice = DB::select("select dm_customer_invoice_has_stock_batch.dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id, dm_customer_invoice_has_stock_batch.pm_product_sub_category_id, SUM(dm_customer_invoice_has_stock_batch.return_qty) as sumQty FROM dm_customer_invoice_has_stock_batch INNER JOIN dm_customer_invoice ON (dm_customer_invoice_has_stock_batch.dm_customer_invoice_id = dm_customer_invoice.id) WHERE (dm_customer_invoice_has_stock_batch.dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id='" . $request->deliveryId . "' ) group by dm_customer_invoice_has_stock_batch.pm_product_sub_category_id");

        // new
        $deliveryInvoice = DB::select("SELECT `dm_customer_invoice_has_stock_batch`.`dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id`
                                            , `dm_customer_invoice_has_stock_batch`.`pm_product_sub_category_id`
                                            , `dm_customer_invoice_has_stock_batch`.`dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id` AS stockBatchID
                                            , SUM(`dm_customer_invoice_has_stock_batch`.`return_qty`) AS sumQty
                                            , `dm_delivery_vehicle_has_stock_batch`.`system_return_qty`
                                            , `dm_delivery_vehicle_has_stock_batch`.`physical_return_qty`
                                        FROM
                                            `dm_delivery_vehicle_has_stock_batch`
                                            INNER JOIN `dm_delivery_vehicle`
                                                ON (`dm_delivery_vehicle_has_stock_batch`.`dm_delivery_vehicle_id` = `dm_delivery_vehicle`.`id`)
                                            INNER JOIN `dm_customer_invoice_has_stock_batch`
                                                ON (`dm_customer_invoice_has_stock_batch`.`dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id` = `dm_delivery_vehicle_has_stock_batch`.`dm_delivery_vehicle_id`)
                                                AND (`dm_customer_invoice_has_stock_batch`.`dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id` = `dm_delivery_vehicle_has_stock_batch`.`pm_stock_batch_id`)
                                            INNER JOIN `pm_stock_batch`
                                                ON (`dm_delivery_vehicle_has_stock_batch`.`pm_stock_batch_id` = `pm_stock_batch`.`id`)
                                            INNER JOIN `dm_customer_invoice`
                                                ON (`dm_customer_invoice_has_stock_batch`.`dm_customer_invoice_id` = `dm_customer_invoice`.`id`)
                                            INNER JOIN `pm_product_sub_category`
                                                ON (`pm_stock_batch`.`pm_product_sub_category_id` = `pm_product_sub_category`.`id`)
                                        WHERE (`dm_customer_invoice_has_stock_batch`.`dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id` = ?)
                                        GROUP BY `dm_customer_invoice_has_stock_batch`.`pm_product_sub_category_id`
                                        ORDER BY ISNULL(`pm_product_sub_category`.`sequence_no`), `pm_product_sub_category`.`sequence_no` ASC", [$request->deliveryId]);

        $route = Routes::find($delivery->cm_routes_id);

        $deliveryInvoices = DB::select("select c.customer_name,c.id As cusID FROM cm_customers c INNER JOIN dm_customer_invoice cusInvo ON (c.id = cusInvo.cm_customers_id) INNER JOIN dm_customer_invoice_has_stock_batch cusSto ON (cusInvo.id = cusSto.dm_customer_invoice_id) WHERE c.cm_routes_id= '" . $delivery->cm_routes_id . "' && cusSto.dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id='" . $request->deliveryId . "' group by c.customer_name");
        $deliveryCustomers = DB::select("select cm.customer_name from `cm_customers` cm where cm.id NOT IN (select c.id FROM cm_customers c INNER JOIN dm_customer_invoice cusInvo ON (c.id = cusInvo.cm_customers_id) INNER JOIN dm_customer_invoice_has_stock_batch cusSto ON (cusInvo.id = cusSto.dm_customer_invoice_id) WHERE c.cm_routes_id= '" . $delivery->cm_routes_id . "' && cusSto.dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id='" . $request->deliveryId . "' group by c.customer_name) AND cm.cm_routes_id='" . $delivery->cm_routes_id . "' && cm.is_active = 1");

        return view('vehicle.arrangeVehicle.ajaxDeliveryVehicle.loadVehicleDeliveryDetails', compact('vehicle', 'deliveryStock', 'delivery', 'vehicleRegNo', 'deliveryInvoice', 'route', 'deliveryInvoices', 'deliveryCustomers'));
    }

    public function viewCompleteModal(Request $request)
    {
        $deliveryId = $request->deliveryId;
        return view($request->url, compact('deliveryId'));
    }

    public function removeDeliveryProducts(Request $request)
    {
        $available = '';
        $msg = "";
        $deliverVehicleDetails = DeliveryVehicleHasStock::where([['dm_delivery_vehicle_id', $request->VehicleID], ['pm_stock_batch_id', $request->batchID]])->first();
        $available = $deliverVehicleDetails->availbale_qty;

        $deleteDeliveryRecord = DeliveryVehicleHasStock::where([['dm_delivery_vehicle_id', $request->VehicleID], ['pm_stock_batch_id', $request->batchID]])->delete();
        $stock = StockBatch::find($request->batchID);
        $stockalculation = $stock->increment('available_quantity', $available);
        if ($deleteDeliveryRecord && $stockalculation) {
            $msg = "sucess";
        } else {
            $msg = "error";
        }
        return compact("msg");
    }

    public function deleteDelivery(Request $request)
    {
        $logged_user = session('logged_user_id');

        DB::beginTransaction();
        try {
            $deliverystock = DeliveryVehicleHasStock::where('dm_delivery_vehicle_id', $request->deliveryId)->get();

            foreach ($deliverystock as $delivery) {
                $qty = (float) $delivery->availbale_qty;
                $stock = StockBatch::find($delivery->pm_stock_batch_id);

                $stockalculation = $stock->increment('available_quantity', $qty);
            }

            // DELETE - Delivery Vehicle Has Stock  records
            $deleteVehicleStock = DeliveryVehicleHasStock::where('dm_delivery_vehicle_id', $request->deliveryId)->delete();

            // DELETE - Delivery Vehicle (change status to 3)
            // $deleteVehicle = DeliveryVehicle::where('id', $request->deliveryId)->delete();
            $deleteVehicle = DeliveryVehicle::where('id', $request->deliveryId)->update([
                'status' => STATIC_DATA_MODEL::$deliveryDeleted,
                'updated_at' => Carbon::now(),
                'updated_by' => $logged_user
            ]);

            //Save user activity
            $userActivity = new UserActivityManagementController();
            $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "delete Delivery  - , delivery Id - " . $request->deliveryId . "Completed");

            DB::commit();
            $msg = 'sucess';

            return compact('msg');
        } catch (\Exception $e) {

            $msg = $e->getMessage();

            DB::rollback();
            return compact('msg');
        }
    }
}
