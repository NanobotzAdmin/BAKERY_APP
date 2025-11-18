<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\StockBatch;
use App\SubCategory;
use App\MainCategory;
use App\STATIC_DATA_MODEL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductManagementController extends Controller
{
    public function adminProductManagementIndex()
    {
        $MainCategory = MainCategory::all();
        $mainActiveCategory = MainCategory::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        $subCategory = SubCategory::all();
        return view('Products.category.manageproduct', compact('MainCategory', 'mainActiveCategory', 'subCategory'));
    }

    /////////// SAVE MAIN CATEGORY //////////////////
    public function saveMainCategory(Request $request)
    {
        $this->validate($request, [
            'category' => 'required',
        ]);

        if (MainCategory::where('main_category_name', request('category'))->exists()) {
            session()->flash('message', '<i class="fa fa-exclamation-circle" style="color:red;"></i> <b>Saving Main Category Stopped</b> <br> &nbsp;&nbsp;&nbsp; <i>Main Category: "'.$request->category.'" already exists..</i>');
            session()->flash('flash_message_type', 'alert-danger');
            return redirect()->back();
        } else {
            $logged_user = session('logged_user_id');

            $mainCategory = new MainCategory();
            $mainCategory->main_category_name = $request->category;
            $mainCategory->is_active = STATIC_DATA_MODEL::$Active;
            $mainCategory->created_at = Carbon::now();
            $mainCategory->updated_at = Carbon::now();
            $mainCategory->created_by = $logged_user;
            $mainCategorysaved = $mainCategory->save();

            //Get last record user login
            $lastMainCategoryId = DB::table('pm_product_main_category')->latest()->first();

            if (!$mainCategorysaved) {
                session()->flash('message', '<i class="fa fa-exclamation-circle" style="color:red;"></i> <b>Saving Main Category Stopped</b> <br> &nbsp;&nbsp;&nbsp; <i>Something went wrong! Main Category failed to save..</i>');
                session()->flash('flash_message_type', 'alert-danger');
                return redirect()->back();
            } else {
                //Save user activity
                $userActivity = new UserActivityManagementController();
                $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "New Main Category " . $lastMainCategoryId->id . " Saved.");

                session()->flash('message', '<i class="fa fa-check-circle"  style="color:green;"></i> <b>Main Category Saved</b> <br> &nbsp;&nbsp;&nbsp; <i>New Main Category: "'.$mainCategory->main_category_name.'" has been successfully saved.</i>');
                session()->flash('flash_message_type', 'alert-success');
                return redirect()->back();
            }
        }
    }


    ///////////////// LOAD CATEGORIES //////////
    public function loadCategoryDataToModal(Request $request)
    {
        $categoryType = $request->categoryType;
        if ($categoryType == "mainCategory") {
            $CategoryData = MainCategory::find($request->CategoryId);
        } else {
            $CategoryData = SubCategory::find($request->CategoryId);
        }
        return view('Products.category.ajaxCategory.loadCategorydataTomodal', compact('CategoryData', 'categoryType'));
    }


    ////////// UPDATE MAIN CATEGORY /////////
    public function updateMainCategory(Request $request)
    {
        $this->validate($request, [
            'MODAL_MAIN_CATEGORY_NAME' => 'required',
        ]);

        $checkCategoryName = MainCategory::find($request->MODAL_MAIN_CATEGORY_ID);
        $categoryStatus = true;

        if (MainCategory::where('main_category_name', request('MODAL_MAIN_CATEGORY_NAME'))->exists()) {

            if ($checkCategoryName->main_category_name == $request->MODAL_MAIN_CATEGORY_NAME) {
                $categoryStatus = true;
            } else {
                $categoryStatus = false;
                session()->flash('message', '<i class="fa fa-exclamation-circle" style="color:red;"></i> <b>Updating Main Category Stopped !</b> <br> &nbsp;&nbsp;&nbsp; <i>Main Category Name is already exists!</i>');
                session()->flash('flash_message_type', 'alert-warning');
                return redirect()->back();
            }
        }

        if ($categoryStatus) {
            $mainCategoryUpdate = MainCategory::find($request->MODAL_MAIN_CATEGORY_ID);
            $mainCategoryUpdate->main_category_name = $request->MODAL_MAIN_CATEGORY_NAME;
            $mainCategoryUpdate->updated_at = Carbon::now();
            $mainCategoryUpdatesaved = $mainCategoryUpdate->save();

            if (!$mainCategoryUpdatesaved) {
                session()->flash('message', '<i class="fa fa-exclamation-circle" style="color: red;"></i> <b>Updating Main Category Stopped !</b> <br> &nbsp;&nbsp;&nbsp; <i>Something went wrong! Main Category failed to update..</i>');
                session()->flash('flash_message_type', 'alert-danger');
                return redirect()->back();
            } else {
                //Save user activity
                $userActivity = new UserActivityManagementController();
                $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "Update Main Category " . $request->MODAL_MAIN_CATEGORY_ID . " Updated.");

                session()->flash('message', '<i class="fa fa-check-circle"></i> <b>Main Category Updated</b> <br> &nbsp;&nbsp;&nbsp; <i>Main Category: "'.$mainCategoryUpdate->main_category_name.'" has been successfully updated.</i>');
                session()->flash('flash_message_type', 'alert-success');
                return redirect()->back();
            }
        }
    }



    /////////////// SUB CATEGORY FUNCTIONS ///////////////////////////////////////
    public function saveSubCategory(Request $request)
    {
        $this->validate($request, [
            'subCategoryName' => 'required',
            'mainCategorySelect' => 'required|not_in:0',
            'productCode' => 'required',
            'actualCost' => 'required|numeric',
            'retailPrice' => 'required|numeric',
            'retailPrice' => 'required|numeric',
        ]);

        if ($request->duration != '') {
            $this->validate($request, [
                'duration' => 'numeric',
            ]);
        }

        if ((float)$request->sellingPrice < (float)$request->actualCost) {
            session()->flash('message', '<i class="fa fa-exclamation-circle" style="color: red;"></i> <b>Product Saving Stopped !</b> <br> &nbsp;&nbsp;&nbsp; <i>Selling Price cannot be less than Actual Price.</i>');
            session()->flash('flash_message_type', 'alert-danger');
            return redirect()->back();
        } else if ((float)$request->actualCost > (float)$request->retailPrice) {
            session()->flash('message', '<i class="fa fa-exclamation-circle" style="color: red;"></i> <b>Product Saving Stopped !</b> <br> &nbsp;&nbsp;&nbsp; <i>Actual Price cannot be greater than Retail Price.</i>');
            session()->flash('flash_message_type', 'alert-danger');
            return redirect()->back();
        } else if ((float)$request->sellingPrice > (float)$request->retailPrice) {
            session()->flash('message', '<i class="fa fa-exclamation-circle" style="color: red;"></i> <b>Product Saving Stopped !</b> <br> &nbsp;&nbsp;&nbsp; <i>Selling Price cannot be greater than Retail Price.</i>');
            session()->flash('flash_message_type', 'alert-danger');
            return redirect()->back();
        } else {
            if (SubCategory::where(['sub_category_name' => $request->subCategoryName, 'product_code' => $request->productCode])->exists()) {
                session()->flash('message', '<i class="fa fa-exclamation-circle" style="color: red;"></i> <b>Product Saving Stopped !</b> <br> &nbsp;&nbsp;&nbsp; <i>Product Name & Product Code already exists.</i>');
                session()->flash('flash_message_type', 'alert-danger');
                return redirect()->back();
            } else {
                $logged_user = session('logged_user_id');
                $subCategory = new SubCategory();
                $subCategory->sub_category_name = $request->subCategoryName;
                $subCategory->pm_product_main_category_id = $request->mainCategorySelect;
                $subCategory->is_active = STATIC_DATA_MODEL::$Active;
                $subCategory->created_at = Carbon::now();
                $subCategory->updated_at = Carbon::now();
                if ($request->duration != '') {
                    $subCategory->expire_in_days = $request->duration;
                } else {
                    $subCategory->expire_in_days = 0;
                }
                $subCategory->product_code = $request->productCode;
                $subCategory->created_by = $logged_user;
                $subCategory->selling_price = $request->sellingPrice;
                $subCategory->actual_cost = $request->actualCost;
                $subCategory->retail_price = $request->retailPrice;
                $subCategory->discountable_qty = $request->discountedQty;
                $subCategory->discounted_price = $request->discountedPrice;
                $subCategory->sequence_no = $request->sequenceNo;
                $subCategorysaved = $subCategory->save();

                //Get last record user login
                $lastsubCategoryId = DB::table('pm_product_sub_category')->latest()->first();

                if (!$subCategorysaved) {
                    session()->flash('message', '<i class="fa fa-exclamation-circle" style="color: red;"></i> <b>Product saving stopped !</b> <br> &nbsp;&nbsp;&nbsp; <i>Something went wrong! New Product failed to save..</i>');
                    session()->flash('flash_message_type', 'alert-danger');
                    return redirect()->back();
                } else {
                    //Save user activity
                    $userActivity = new UserActivityManagementController();
                    $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "New Sub Category " . $lastsubCategoryId->id . " Saved.");

                    session()->flash('message', '<i class="fa fa-check-circle"></i> <b>Product save successful</b> <br> &nbsp;&nbsp;&nbsp; <i>New Product: "'.$request->subCategoryName.'" has been successfully saved.</i>');
                    session()->flash('flash_message_type', 'alert-success');
                    return redirect()->back();
                }
            }
        }
    }


    public function updateSubCategory(Request $request)
    {
        $this->validate($request, [
            'MODAL_SUBCATEGORY_NAME' => 'required',
            'MODAL_SUBCATEGORY_MAINCATEGORY_SELECT' => 'required|not_in:0',
            'MODAL_PRODUCT_CODE' => 'required',
            'MODAL_SELLING_PRICE' => 'required|numeric',
            'MODAL_ACTUAL_COST' => 'required|numeric',
            'MODAL_RETAIL_PRICE' => 'required|numeric',
        ]);

        if ($request->duration != '') {
            $this->validate($request, [
                'MODAL_SUBCATEGORY_DURATION' => 'numeric',
            ]);
        }

        $checkSubCategoryName = SubCategory::find($request->MODAL_SUBCATEGORY_UPDATE_ID);
        $categoryStatus = true;

        if (SubCategory::where(['sub_category_name' => $request->MODAL_SUBCATEGORY_NAME, 'product_code' => $request->MODAL_PRODUCT_CODE])->exists()) {
            if ($checkSubCategoryName->sub_category_name == $request->MODAL_SUBCATEGORY_NAME && $checkSubCategoryName->product_code == $request->MODAL_PRODUCT_CODE) {
                $categoryStatus = true;
            } else {
                $categoryStatus = false;
            }
        } else {
            $categoryStatus = true;
        }

        if ((float)$request->MODAL_SELLING_PRICE < (float)$request->MODAL_ACTUAL_COST) {
            session()->flash('message', '<i class="fa fa-exclamation-circle" style="color: red;"></i> <b>Product saving stopped !</b> <br> &nbsp;&nbsp;&nbsp; <i>Selling Price cannot be less than Actual Price.</i>');
            session()->flash('flash_message_type', 'alert-danger');
            return redirect()->back();
        } else if ((float)$request->MODAL_ACTUAL_COST > (float)$request->MODAL_RETAIL_PRICE) {
            session()->flash('message', '<i class="fa fa-exclamation-circle" style="color: red;"></i> <b>Product saving stopped !</b> <br> &nbsp;&nbsp;&nbsp; <i>Actual Price cannot be greater than Retail Price.</i>');
            session()->flash('flash_message_type', 'alert-danger');
            return redirect()->back();
        } else if ((float)$request->MODAL_SELLING_PRICE > (float)$request->MODAL_RETAIL_PRICE) {
            session()->flash('message', '<i class="fa fa-exclamation-circle" style="color: red;"></i> <b>Product saving stopped !</b> <br> &nbsp;&nbsp;&nbsp; <i>Selling Price cannot be greater than Retail Price.</i>');
            session()->flash('flash_message_type', 'alert-danger');
            return redirect()->back();
        } else {
            if ($categoryStatus) {
                $subCategoryUpdate = SubCategory::find($request->MODAL_SUBCATEGORY_UPDATE_ID);
                $subCategoryUpdate->sub_category_name = $request->MODAL_SUBCATEGORY_NAME;
                $subCategoryUpdate->pm_product_main_category_id = $request->MODAL_SUBCATEGORY_MAINCATEGORY_SELECT;
                $subCategoryUpdate->expire_in_days = $request->MODAL_SUBCATEGORY_DURATION;
                $subCategoryUpdate->product_code = $request->MODAL_PRODUCT_CODE;
                $subCategoryUpdate->selling_price = $request->MODAL_SELLING_PRICE;
                $subCategoryUpdate->actual_cost = $request->MODAL_ACTUAL_COST;
                $subCategoryUpdate->retail_price = $request->MODAL_RETAIL_PRICE;
                $subCategoryUpdate->discountable_qty = $request->MODAL_DISCOUNTED_QTY;
                $subCategoryUpdate->discounted_price = $request->MODAL_DISCOUNT_PRICE;
                $subCategoryUpdate->sequence_no = $request->MODAL_SEQUENCE_NO;
                $subCategoryUpdate->updated_at = Carbon::now();
                $subCategoryUpdatesaved = $subCategoryUpdate->save();

                if (!$subCategoryUpdatesaved) {
                    session()->flash('message', '<i class="fa fa-exclamation-circle" style="color: red;"></i> <b>Product update failed</b> <br> &nbsp;&nbsp;&nbsp; <i>Something went wrong! Product failed to update..</i>');
                    session()->flash('flash_message_type', 'alert-danger');
                    return redirect()->back();
                } else {
                    //Save user activity
                    $userActivity = new UserActivityManagementController();
                    $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "Update Sub Category " . $request->MODAL_SUBCATEGORY_UPDATE_ID . " Updated.");

                    session()->flash('message', '<i class="fa fa-check-circle" style="color:green;"></i> <b>Product update successful</b> <br> &nbsp;&nbsp;&nbsp; <i>Product: "'.$subCategoryUpdate->sub_category_name.'" details have been successfully updated.</i>');
                    session()->flash('flash_message_type', 'alert-success');
                    return redirect()->back();
                }
            } else {
                session()->flash('message', '<i class="fa fa-exclamation-circle" style="color:red;"></i> <b>Product updating stopped !</b> <br> &nbsp;&nbsp;&nbsp; <i>Sub Category Name or Product Code is already exists!</i>');
                session()->flash('flash_message_type', 'alert-warning');
                return redirect()->back();
            }
        }
    }


    ////////// load sub Categories ////////////////
    public function loadSubCategories(Request $request)
    {
        $products = SubCategory::where('pm_product_main_category_id', $request->MainCategory)
                                ->where('is_active', STATIC_DATA_MODEL::$Active)
                                ->orderBy('sequence_no')
                                ->get();
        return compact('products');
    }


    //////////// load Product Details //////////// ---- Batch code & product code
    public function loadProductDetails(Request $request)
    {
        $proDetails = SubCategory::find($request->subCategory);
        $productSub = StockBatch::where('pm_product_sub_category_id', $request->subCategory)->get();
        $proCount = $productSub->count() + 1;
        return view($request->url, compact('proDetails', 'proCount'));
    }


    // Active & Deactive sub-Category (Product)
    public function subCatogoryStatusChange(Request $request)
    {
        // Get SubCategory Object
        $subCategoryObj = SubCategory::find($request->subCatID);

        // Toggle the status
        if ($subCategoryObj->is_active == STATIC_DATA_MODEL::$Active) {
            $subCategoryObj->is_active = STATIC_DATA_MODEL::$Inactive;
            $subCategoryObj->updated_at = Carbon::now();
            $msg = 'Sub-Category Deactivated';
        } else {
            $subCategoryObj->is_active = STATIC_DATA_MODEL::$Active;
            $subCategoryObj->updated_at = Carbon::now();
            $msg = 'Sub-Category Activated';
        }

        // save changes
        $subCategoryObj->save();
        // save user activity
        $userActivity = new UserActivityManagementController();
        $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "$msg: " . $subCategoryObj->id);
        return compact('msg');
    }
}
