<?php

namespace App\Http\Controllers;

use App\StoreRack;
use Carbon\Carbon;
use App\StockBatch;
use App\SubCategory;
use App\MainCategory;
use App\RawMaterials;
use App\STATIC_DATA_MODEL;
use App\ProductItemState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class StockController extends Controller
{
    // private functions ----------------------------------------------
    private function errorResponse($message, $type) {
        session()->flash('message', $message);
        session()->flash('flash_message_type', $type);
        return redirect()->back();
    }

    private function successResponse($message, $type) {
        session()->flash('message', $message);
        session()->flash('flash_message_type', $type);
        return redirect()->back();
    }

    private function saveActivity($action, $message) {
        $userActivity = new UserActivityManagementController();
        $userActivity->saveActivity($action, $message);
    }
    // private functions ends -----------------------------------------


    // index page of StockIn
    public function adminStockInIndex()
    {
        $stockBatchList = StockBatch::orderBy('id', 'DESC')->get();
        $categoryList = MainCategory::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        $Status = ProductItemState::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        $stockBatch = StockBatch::whereDate('created_at', Carbon::today())->orderBy('id', 'DESC')->get(); // load today added Stock Batches
        return view('Stock.stockIn', compact('stockBatchList', 'categoryList', 'Status', 'stockBatch'));
    }


    // save Stock Batch
    public function saveStock(Request $request)
    {
        $this->validate($request, [
            'MainCategory' => 'required|not_in:0',
            'subCategory' => 'required|not_in:0',
            'batchCodeHidden' => 'required',
            'retailPrice' => 'required|numeric',
            'sellingPrice' => 'required|numeric',
            'actualCost' => 'required|numeric',
            'discountedPrice' => 'required|numeric',
            'itemStatus' => 'required|not_in:0',
            'addingQty' => 'required|numeric',
            'expiryDate' => 'required',
        ]);

        // if($request->actualCost !=''){
        //     $this->validate($request, [
        //         'actualCost' => 'numeric',

        //         ]);
        //    }

        // $Nowtime = Carbon::now();
        // $isvisibleCount = StockBatch::where([['pm_product_sub_category_id', $request->subCategory], ['is_visible', 1]])->get();

        if (StockBatch::where(['pm_product_sub_category_id' => $request->subCategory, 'created_at' => Carbon::now()])->exists()) {
            session()->flash('message', 'Stock already added');
            session()->flash('flash_message_type', 'alert-danger');
            return redirect()->back();
        } else {
            // if (count($isvisibleCount) >= 1) {
            //     session()->flash('message', 'Stock batch visible one already exsits');
            //     session()->flash('flash_message_type', 'alert-danger');
            //     return redirect()->back();
            // } else {
            //     $subCategoryItems = SubCategory::find($request->subCategory);
            //     $rawMaterial = RawMaterials::where('pm_product_sub_category_id', $subCategoryItems->id)->first();
            // }

            // $visible = '';
            // if ($request->visibleCheck == 'on') {
            //     $visible = STATIC_DATA_MODEL::$visible;
            // } else {
            //     $visible = STATIC_DATA_MODEL::$nonVisible;
            // }

            // get selected Sub-Category object
            $subCategoryItems = SubCategory::find($request->subCategory);
            // get Raw Materials object for the Sub-Category
            $rawMaterial = RawMaterials::where('pm_product_sub_category_id', $subCategoryItems->id)->first();

            if ($request->itemStatus == STATIC_DATA_MODEL::$packed) { // PACKED

                if (!empty($rawMaterial)) {
                    if ((float) $rawMaterial->available_count < (float) $request->addingQty) {
                        session()->flash('message', 'Adding Qty Cannot be grater than to Material Available Qty');
                        session()->flash('flash_message_type', 'alert-danger');
                        return redirect()->back();
                    } else {
                        $logged_user = session('logged_user_id');

                        $stockBatch = new StockBatch();
                        $stockBatch->batch_code = $request->batchCodeHidden;
                        $stockBatch->pm_product_item_state_id = $request->itemStatus;
                        $stockBatch->pm_product_sub_category_id = $request->subCategory;
                        $stockBatch->stock_in_quantity = $request->addingQty;
                        $stockBatch->available_quantity = $request->addingQty;
                        $stockBatch->stock_returned_quantity = 0;
                        $stockBatch->returned_date = null;

                        // $stockBatch->actual_cost = $subCategoryItems->actual_cost;
                        // $stockBatch->retail_price = $subCategoryItems->retail_price;
                        // $stockBatch->selling_price = $subCategoryItems->selling_price;
                        // $stockBatch->discounted_price = $subCategoryItems->discounted_price;
                        // $stockBatch->discountable_qty = $subCategoryItems->discountable_qty;

                        $stockBatch->actual_cost = $request->actualCost;
                        $stockBatch->retail_price = $request->retailPrice;
                        $stockBatch->selling_price = $request->sellingPrice;
                        $stockBatch->discounted_price = $request->discountedPrice;
                        $stockBatch->discountable_qty = $subCategoryItems->discountable_qty;

                        $stockBatch->stock_date = Carbon::now();
                        $stockBatch->expire_date = $request->expiryDate;
                        $stockBatch->is_active = STATIC_DATA_MODEL::$Active;
                        $stockBatch->created_at = Carbon::now();
                        $stockBatch->updated_at = Carbon::now();
                        $stockBatch->created_by = $logged_user;
                        $stockBatch->is_visible = 0;
                        // $stockBatch->is_visible = $visible;
                        $stockBatchsaved = $stockBatch->save();

                        // get adding Quantity
                        $addingQty = (float) $request->addingQty;

                        $updateRawMaterialQuantity = RawMaterials::where('id', $rawMaterial->id)->decrement('available_count', $addingQty);

                        //Get last record user login
                        $lastStockBatchId = DB::table('pm_stock_batch')->latest()->first();

                        if (!$stockBatchsaved && !$updateRawMaterialQuantity) {
                            session()->flash('message', 'Stock Save Failed');
                            session()->flash('flash_message_type', 'alert-danger');
                            return redirect()->back();
                        } else {
                            //Save user activity
                            $userActivity = new UserActivityManagementController();
                            $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "New Stock Batch " . $lastStockBatchId->id . " Saved.");
                            session()->flash('message', 'New stock successfully saved.');
                            session()->flash('flash_message_type', 'alert-success');
                            return redirect()->back();
                        }
                    }
                } else {
                    session()->flash('message', 'This Product is not assigned to raw material');
                    session()->flash('flash_message_type', 'alert-danger');
                    return redirect()->back();
                }

            } else if ($request->itemStatus == STATIC_DATA_MODEL::$unpacked) { // UNPACKED

                $subCategoryItems = SubCategory::find($request->subCategory);
                $rawMaterial = RawMaterials::where('pm_product_sub_category_id', $subCategoryItems->id)->first();

                $logged_user = session('logged_user_id');

                $stockBatch = new StockBatch();
                $stockBatch->batch_code = $request->batchCodeHidden;
                $stockBatch->pm_product_item_state_id = $request->itemStatus;
                $stockBatch->pm_product_sub_category_id = $request->subCategory;
                $stockBatch->stock_in_quantity = $request->addingQty;
                $stockBatch->available_quantity = $request->addingQty;
                $stockBatch->stock_returned_quantity = 0;
                $stockBatch->returned_date = null;
                $stockBatch->stock_date = Carbon::now();
                $stockBatch->expire_date = $request->expiryDate;

                // $stockBatch->actual_cost = $subCategoryItems->actual_cost;
                // $stockBatch->retail_price = $subCategoryItems->retail_price;
                // $stockBatch->selling_price = $subCategoryItems->selling_price;
                // $stockBatch->discounted_price = $subCategoryItems->discounted_price;
                // $stockBatch->discountable_qty = $subCategoryItems->discountable_qty;

                $stockBatch->actual_cost = $request->actualCost;
                $stockBatch->retail_price = $request->retailPrice;
                $stockBatch->selling_price = $request->sellingPrice;
                $stockBatch->discounted_price = $request->discountedPrice;
                $stockBatch->discountable_qty = $subCategoryItems->discountable_qty;

                $stockBatch->is_active = STATIC_DATA_MODEL::$Active;
                $stockBatch->created_at = Carbon::now();
                $stockBatch->updated_at = Carbon::now();
                $stockBatch->created_by = $logged_user;
                $stockBatch->is_visible = 0;
                // $stockBatch->is_visible = $visible;
                $stockBatchsaved = $stockBatch->save();

                // get adding Quantity
                $addingQty = (float) $request->addingQty;

                $updateRawMaterialQuantity = RawMaterials::where('id', $rawMaterial->id)->decrement('available_count', $addingQty);

                //Get last record user login
                $lastStockBatchId = DB::table('pm_stock_batch')->latest()->first();

                if (!$stockBatchsaved && !$updateRawMaterialQuantity) {
                    session()->flash('message', 'Stock Save Failed');
                    session()->flash('flash_message_type', 'alert-danger');
                    return redirect()->back();
                } else {
                    //Save user activity
                    $userActivity = new UserActivityManagementController();
                    $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "New Stock Batch " . $lastStockBatchId->id . " Saved.");
                    session()->flash('message', 'New stock successfully saved.');
                    session()->flash('flash_message_type', 'alert-success');
                    return redirect()->back();
                }
            }
        }
    }


    // load specific Stock data to update modal
    public function loadstockUpdateData(Request $request)
    {
        $stockDetails = StockBatch::find($request->batchId);
        $mainCategory = MainCategory::all();
        $subCategory = SubCategory::where('pm_product_main_category_id', $request->mainCategoryId)->get();
        $productListstatus = ProductItemState::all();
        $maincategoryId = $request->mainCategoryId;
        return view($request->url, compact('stockDetails', 'mainCategory', 'subCategory', 'productListstatus', 'maincategoryId'));
    }


    // update Stock Batch
    public function updateStockData(Request $request)
    {
        $this->validate($request, [
            'MODAL_MAIN_CATEGORY' => 'required|not_in:0',
            'MODAL_SUB_CATEGORY' => 'required|not_in:0',
            'MODAL_ITEM_STATUS' => 'required|not_in:0',
            'MODAL_STOCK_IN_QTY' => 'required|numeric',
            'expiryDate' => 'required',
            'STOCK_IN_UPDATE_ID' => 'required',
        ]);

        $logged_user = session('logged_user_id');
        $subCategoryItems = SubCategory::find($request->MODAL_SUB_CATEGORY);

        if ($request->MODAL_ITEM_STATUS == STATIC_DATA_MODEL::$packed) {
            $rawMaterial = RawMaterials::where('pm_product_sub_category_id', $subCategoryItems->id)->first();

            if (empty($rawMaterial)) {
                return $this->errorResponse('This Product is not assigned to raw material', 'alert-danger');
            }

            if ((float) $rawMaterial->available_count < (float) $request->MODAL_STOCK_IN_QTY) {
                return $this->errorResponse('Adding Qty Cannot be greater than Material Available Qty', 'alert-danger');
            }
        }

        // search Stock Batch by id
        $stockBatch = StockBatch::find($request->STOCK_IN_UPDATE_ID);

        // $stockBatch->batch_code = $request->batchCodeHidden;
        $stockBatch->pm_product_item_state_id = $request->MODAL_ITEM_STATUS;
        $stockBatch->pm_product_sub_category_id = $request->MODAL_SUB_CATEGORY;
        $stockBatch->stock_in_quantity = $request->MODAL_STOCK_IN_QTY;
        $stockBatch->available_quantity = $request->MODAL_STOCK_IN_QTY;
        $stockBatch->stock_returned_quantity = 0;
        $stockBatch->returned_date = null;
        $stockBatch->selling_price = $subCategoryItems->selling_price;
        $stockBatch->actual_cost = $subCategoryItems->actual_cost;
        $stockBatch->stock_date = Carbon::now();
        $stockBatch->expire_date = $request->expiryDate;
        $stockBatch->retail_price = $subCategoryItems->retail_price;
        $stockBatch->discountable_qty = $subCategoryItems->discountable_qty;
        $stockBatch->discounted_price = $subCategoryItems->discounted_price;
        $stockBatch->is_active = STATIC_DATA_MODEL::$Active;
        $stockBatch->updated_at = Carbon::now();
        $stockBatch->created_by = $logged_user;
        $stockBatchsaved = $stockBatch->save();

        if (!$stockBatchsaved) {
            return $this->errorResponse('Stock Update Failed', 'alert-danger');
        }

        //Save user activity
        $this->saveActivity(STATIC_DATA_MODEL::$update, "Update Stock Batch, ID -  $request->STOCK_IN_UPDATE_ID Updated.");

        return $this->successResponse('Stock Update success', 'alert-success');
    }
    // OLD METHOD--
    // public function updateStockData(Request $request)
    // {
    //     $this->validate($request, [
    //         'MODAL_MAIN_CATEGORY' => 'required|not_in:0',
    //         'MODAL_SUB_CATEGORY' => 'required|not_in:0',
    //         'MODAL_ITEM_STATUS' => 'required|not_in:0',
    //         'MODAL_STOCK_IN_QTY' => 'required|numeric',
    //         'expiryDate' => 'required',
    //         'batchCodeHidden' => 'required',
    //     ]);

    //     // if($request->actualCost !=''){
    //     //     $this->validate($request, [
    //     //         'MODAL_ACTUAL_COST' => 'numeric',

    //     //         ]);
    //     //    }

    //     if ($request->MODAL_ITEM_STATUS == STATIC_DATA_MODEL::$packed) {

    //         $subCategoryItems = SubCategory::find($request->MODAL_SUB_CATEGORY);
    //         $rawMaterial = RawMaterials::where('pm_product_sub_category_id', $subCategoryItems->id)->first();

    //         if (!empty($rawMaterial)) {
    //             if ((float) $rawMaterial->available_count < (float) $request->MODAL_STOCK_IN_QTY) {
    //                 session()->flash('message', 'Adding Qty Cannot be grater than to Material Available Qty');
    //                 session()->flash('flash_message_type', 'alert-danger');
    //                 return redirect()->back();
    //             } else {
    //                 $logged_user = session('logged_user_id');

    //                 $stockBatch = StockBatch::find($request->STOCK_IN_UPDATE_ID);
    //                 $stockBatch->batch_code = $request->batchCodeHidden;
    //                 $stockBatch->pm_product_item_state_id = $request->MODAL_ITEM_STATUS;
    //                 $stockBatch->pm_product_sub_category_id = $request->MODAL_SUB_CATEGORY;
    //                 $stockBatch->stock_in_quantity = $request->MODAL_STOCK_IN_QTY;
    //                 $stockBatch->available_quantity = $request->MODAL_STOCK_IN_QTY;
    //                 $stockBatch->stock_returned_quantity = 0;
    //                 $stockBatch->returned_date = null;
    //                 $stockBatch->selling_price = $subCategoryItems->selling_price;
    //                 $stockBatch->actual_cost = $subCategoryItems->actual_cost;
    //                 $stockBatch->stock_date = Carbon::now();
    //                 $stockBatch->expire_date = $request->expiryDate;
    //                 $stockBatch->retail_price = $subCategoryItems->retail_price;

    //                 $stockBatch->discountable_qty = $subCategoryItems->discountable_qty;
    //                 $stockBatch->discounted_price = $subCategoryItems->discounted_price;

    //                 $stockBatch->is_active = STATIC_DATA_MODEL::$Active;
    //                 $stockBatch->updated_at = Carbon::now();
    //                 $stockBatch->created_by = $logged_user;
    //                 $stockBatchsaved = $stockBatch->save();

    //                 // get Adding Quantity
    //                 $addingQty = (float) $request->MODAL_STOCK_IN_QTY;

    //                 $updateRawMaterialQuantity = RawMaterials::where('id', $rawMaterial->id)->decrement('available_count', $addingQty);

    //                 if (!$stockBatchsaved && !$updateRawMaterialQuantity) {
    //                     session()->flash('message', 'Update Stock Save Failed');
    //                     session()->flash('flash_message_type', 'alert-danger');
    //                     return redirect()->back();
    //                 } else {
    //                     //Save user activity
    //                     $userActivity = new UserActivityManagementController();
    //                     $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "Update Stock Batch, ID -  " . $request->STOCK_IN_UPDATE_ID . " Updated.");
    //                     session()->flash('message', 'Stock Update success');
    //                     session()->flash('flash_message_type', 'alert-success');
    //                     return redirect()->back();
    //                 }
    //             }
    //         } else {
    //             session()->flash('message', 'This Product is not assigned to raw material');
    //             session()->flash('flash_message_type', 'alert-danger');
    //             return redirect()->back();
    //         }
    //     } else {
    //         $subCategoryItems = SubCategory::find($request->MODAL_SUB_CATEGORY);
    //         $rawMaterial = RawMaterials::where('pm_product_sub_category_id', $subCategoryItems->id)->first();

    //         $logged_user = session('logged_user_id');

    //         $stockBatch = StockBatch::find($request->STOCK_IN_UPDATE_ID);
    //         $stockBatch->batch_code = $request->batchCodeHidden;
    //         $stockBatch->pm_product_item_state_id = $request->MODAL_ITEM_STATUS;
    //         $stockBatch->pm_product_sub_category_id = $request->MODAL_SUB_CATEGORY;
    //         $stockBatch->stock_in_quantity = $request->MODAL_STOCK_IN_QTY;
    //         $stockBatch->available_quantity = $request->MODAL_STOCK_IN_QTY;
    //         $stockBatch->stock_returned_quantity = 0;
    //         $stockBatch->returned_date = null;
    //         $stockBatch->selling_price = $subCategoryItems->selling_price;
    //         $stockBatch->actual_cost = $subCategoryItems->actual_cost;
    //         $stockBatch->stock_date = Carbon::now();
    //         $stockBatch->expire_date = $request->expiryDate;
    //         $stockBatch->retail_price = $subCategoryItems->retail_price;

    //         $stockBatch->discountable_qty = $subCategoryItems->discountable_qty;
    //         $stockBatch->discounted_price = $subCategoryItems->discounted_price;

    //         $stockBatch->is_active = STATIC_DATA_MODEL::$Active;
    //         $stockBatch->updated_at = Carbon::now();
    //         $stockBatch->created_by = $logged_user;
    //         $stockBatchsaved = $stockBatch->save();

    //         //Get last record user login
    //         $lastStockBatchId = DB::table('pm_stock_batch')->latest()->first();

    //         if (!$stockBatchsaved && !$updateRawMaterialQuantity) {
    //             session()->flash('message', 'Stock Update Failed');
    //             session()->flash('flash_message_type', 'alert-danger');
    //             return redirect()->back();
    //         } else {
    //             //Save user activity
    //             $userActivity = new UserActivityManagementController();
    //             $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "Update Stock Batch, ID -  " . $request->STOCK_IN_UPDATE_ID . " Updated.");
    //             session()->flash('message', 'Stock Update success');
    //             session()->flash('flash_message_type', 'alert-success');
    //             return redirect()->back();
    //         }
    //     }
    // }


    public function searchByDateStockIn(Request $request)
    {
        $daterange = $request->dateSelect;
        $dateFromFormat = date("Y-m-d", strtotime($daterange));
        $stockBatch = StockBatch::whereDate('created_at', $dateFromFormat)->orderBy('id', 'DESC')->get();

        return view('Stock.ajaxStock.loadStocksToDate', compact('stockBatch'));
    }


    public function adminManageProductsIndex(Request $request)
    {
        $Category = MainCategory::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        return view('Stock.stockManagement.manageStock', compact('Category'));
    }


    public function loadProductsToCategory(Request $request)
    {
        $productList = SubCategory::where('pm_product_main_category_id', $request->categoryId)
                                    ->where('is_active', STATIC_DATA_MODEL::$Active)
                                    ->get();
        return view('Stock.stockManagement.ajaxStockManagement.loadProductsTocategory', compact('productList'));
    }


    public function loadProductBatches(Request $request)
    {
        // OLD
        // $stockBatch_List = StockBatch::where('pm_product_sub_category_id', $request->productSubCategoryId)->get();

        // NEW
        $query = StockBatch::query()
                ->select('pm_stock_batch.id AS SB_id', 'pm_stock_batch.*')
                ->join('pm_product_sub_category', 'pm_product_sub_category.id', '=', 'pm_stock_batch.pm_product_sub_category_id')
                ->join('pm_product_main_category', 'pm_product_main_category.id', '=', 'pm_product_sub_category.pm_product_main_category_id')
                ->where('available_quantity', '>', 0.0);
        if ($request->productMainCategoryId > 0) {
            $query->where('pm_product_main_category.id', $request->productMainCategoryId);
        }
        if ($request->productSubCategoryId > 0) {
            $query->where('pm_product_sub_category_id', $request->productSubCategoryId);
        }
        $stockBatch_List = $query->get();

        return view('Stock.stockManagement.ajaxStockManagement.loadBatchList', compact('stockBatch_List'));
    }


    public function loadQuantityUpdateModal(Request $request)
    {
        $batchId = $request->batchId;
        return view('Stock.stockManagement.ajaxStockManagement.loadUpdateModalContent', compact('batchId'));
    }


    // UPDATE Stock Batch
    public function updateStockBatch(Request $request)
    {
        try {
            // get request data
            $quantity = $request->qty;
            $productItemState = $request->productItemState;
            $retailPrice = $request->retailPrice;
            $sellingPrice = $request->sellingPrice;
            $actualCost = $request->actualCost;
            $discountedPrice = $request->discountedPrice;
            $action = $request->action;

            // get Objects
            $stock = StockBatch::findOrFail($request->batchId);
            $subCategoryItems = SubCategory::findOrFail($stock->pm_product_sub_category_id);
            $rawMaterial = RawMaterials::where('pm_product_sub_category_id', $subCategoryItems->id)->first();
            $addingQty = (float)$quantity - (float)$stock->available_quantity;
// dd($stock->pm_product_item_state_id);
            $stock->pm_product_item_state_id = $productItemState;

            // Update Stock Batch prices
            $stock->retail_price = $retailPrice;
            $stock->selling_price = $sellingPrice;
            $stock->actual_cost = $actualCost;
            $stock->discounted_price = $discountedPrice;
            $stock->updated_at = Carbon::now();

            // Update Stock Batch quantity based on action
            if ($action == '2') { //  2 = Add (+)
                if ($stock->pm_product_item_state_id == STATIC_DATA_MODEL::$packed) {
                    if ((float)$rawMaterial->available_count < (float)$quantity) {
                        return ['msg' => 'rawmaterialError'];
                    } else {
                        $stock->available_quantity = (float)$quantity;
                        $stock->save();
                        RawMaterials::where('id', $rawMaterial->id)->decrement('available_count', $addingQty);
                    }
                } else {
                    $stock->available_quantity = (float)$quantity;
                    $stock->save();
                }
            } else {
                $stock->available_quantity = (float)$quantity;
                $stock->save();
            }

            return ['msg' => 'success'];

        } catch (QueryException $exception) {
            // Log SQL exception
            Log::error($exception->getMessage());
            return ['msg' => $exception->getMessage()];
        } catch (\Exception $exception) {
            // Log other exceptions
            Log::error($exception->getMessage());
            return ['msg' => $exception->getMessage()];
        }
    }


    public function updateStockRackCount(Request $request)
    {
        $msg = '';
        $logged_user = session('logged_user_id');
        $updateRack = StoreRack::find($request->id);
        $updateRack->rack_count = $request->rackCount;
        $updateRack->updated_at =Carbon::now();
        $updateRack->updated_by =$logged_user;
        $saveUpdate = $updateRack->save();
        if ($saveUpdate) {
            $msg = 'success';
                 //Save user activity
                 $userActivity = new UserActivityManagementController();
                 $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "Update Store Rack count -  " . $request->rackCount );
        } else {
            $msg = 'error';
        }
        return compact('msg');
    }


    public function viewStoreRackModel(){
        $rackCount =StoreRack::find(1);
        return view('customer.ajaxCustomerManagement.loadUpdateStoreRackModel', compact('rackCount'));
    }

}
