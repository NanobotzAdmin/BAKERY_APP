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
        $productItems = \App\ProductItem::with(['mainCategory', 'subCategory', 'variation', 'variationValue'])->get();
        return view('Products.category.manageproduct', compact('MainCategory', 'mainActiveCategory', 'subCategory', 'productItems'));
    }

    public function adminCategoryVariationManagementIndex()
    {
        $MainCategory = MainCategory::all();
        $mainActiveCategory = MainCategory::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        $subCategory = SubCategory::all();
        $variations = \App\Variation::with('variationValues')->get();
        
        // Get variation value types from STATIC_DATA_MODEL
        $variationValueTypes = [
            \App\STATIC_DATA_MODEL::$variationValueTypeL => 'L',
            \App\STATIC_DATA_MODEL::$variationValueTypeML => 'ML',
            \App\STATIC_DATA_MODEL::$variationValueTypeG => 'G',
            \App\STATIC_DATA_MODEL::$variationValueTypeKG => 'KG'
        ];
        
        return view('Products.category.categoryVariationManagement', compact('MainCategory', 'mainActiveCategory', 'subCategory', 'variations', 'variationValueTypes'));
    }

    public function adminProductRegistrationIndex()
    {
        $mainCategories = MainCategory::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        $productItemTypes = [
            \App\STATIC_DATA_MODEL::$packed => 'Packed',
            \App\STATIC_DATA_MODEL::$unpacked => 'Unpacked'
        ];
        $variations = \App\Variation::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        return view('Products.category.productRegistration', compact('mainCategories', 'productItemTypes', 'variations'));
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
        $MainCategory = MainCategory::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        return view('Products.category.ajaxCategory.loadCategorydataTomodal', compact('CategoryData', 'categoryType', 'MainCategory'));
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

    /////////// SAVE VARIATION //////////////////
    public function saveVariation(Request $request)
    {
        $this->validate($request, [
            'variation_name' => 'required',
        ]);

        if (\App\Variation::where('variation_name', request('variation_name'))->exists()) {
            return response()->json(['status' => 'error', 'message' => 'Variation already exists']);
        } else {
            $logged_user = session('logged_user_id');

            $variation = new \App\Variation();
            $variation->variation_name = $request->variation_name;
            $variation->is_active = STATIC_DATA_MODEL::$Active;
            $variation->created_by = $logged_user;
            $variation->updated_by = $logged_user;
            $variationsaved = $variation->save();

            if (!$variationsaved) {
                return response()->json(['status' => 'error', 'message' => 'Failed to save variation']);
            } else {
                return response()->json(['status' => 'success', 'message' => 'Variation saved successfully', 'data' => $variation]);
            }
        }
    }

    /////////// SAVE VARIATION VALUE //////////////////
    public function saveVariationValue(Request $request)
    {
        $this->validate($request, [
            'pm_variation_id' => 'required',
            'pm_variation_value_type_id' => 'required',
            'variation_value' => 'required',
        ]);

        $logged_user = session('logged_user_id');

        $variationValue = new \App\VariationValue();
        $variationValue->pm_variation_id = $request->pm_variation_id;
        $variationValue->pm_variation_value_type_id = $request->pm_variation_value_type_id;
        $variationValue->variation_value_name = $request->variation_value_name;
        $variationValue->variation_value = $request->variation_value;
        $variationValue->is_active = STATIC_DATA_MODEL::$Active;
        $variationValue->created_by = $logged_user;
        $variationValue->updated_by = $logged_user;
        $variationValuesaved = $variationValue->save();

        if (!$variationValuesaved) {
            return response()->json(['status' => 'error', 'message' => 'Failed to save variation value']);
        } else {
            // Load the variation value with its relationship
            $variationValue->load('variation');
            return response()->json(['status' => 'success', 'message' => 'Variation value saved successfully', 'data' => $variationValue]);
        }
    }

    /////////// UPDATE VARIATION //////////////////
    public function updateVariation(Request $request)
    {
        $this->validate($request, [
            'variation_id' => 'required',
            'variation_name' => 'required',
        ]);

        $variation = \App\Variation::find($request->variation_id);
        if (!$variation) {
            return response()->json(['status' => 'error', 'message' => 'Variation not found']);
        }

        // Check if another variation with the same name exists
        if (\App\Variation::where('variation_name', $request->variation_name)->where('id', '!=', $request->variation_id)->exists()) {
            return response()->json(['status' => 'error', 'message' => 'Variation name already exists']);
        }

        $variation->variation_name = $request->variation_name;
        $variation->updated_by = session('logged_user_id');
        $variationsaved = $variation->save();

        if (!$variationsaved) {
            return response()->json(['status' => 'error', 'message' => 'Failed to update variation']);
        } else {
            return response()->json(['status' => 'success', 'message' => 'Variation updated successfully', 'data' => $variation]);
        }
    }

    /////////// UPDATE VARIATION VALUE //////////////////
    public function updateVariationValue(Request $request)
    {
        $this->validate($request, [
            'variation_value_id' => 'required',
            'pm_variation_value_type_id' => 'required',
            'variation_value' => 'required',
        ]);

        $variationValue = \App\VariationValue::find($request->variation_value_id);
        if (!$variationValue) {
            return response()->json(['status' => 'error', 'message' => 'Variation value not found']);
        }

        $variationValue->pm_variation_value_type_id = $request->pm_variation_value_type_id;
        $variationValue->variation_value_name = $request->variation_value_name;
        $variationValue->variation_value = $request->variation_value;
        $variationValue->updated_by = session('logged_user_id');
        $variationValuesaved = $variationValue->save();

        if (!$variationValuesaved) {
            return response()->json(['status' => 'error', 'message' => 'Failed to update variation value']);
        } else {
            // Load the variation value with its relationship
            $variationValue->load('variation');
            return response()->json(['status' => 'success', 'message' => 'Variation value updated successfully', 'data' => $variationValue]);
        }
    }

    /////////// DELETE VARIATION //////////////////
    public function deleteVariation(Request $request)
    {
        $variation = \App\Variation::find($request->variation_id);
        if (!$variation) {
            return response()->json(['status' => 'error', 'message' => 'Variation not found']);
        }

        // Check if variation has any values
        if ($variation->variationValues()->count() > 0) {
            return response()->json(['status' => 'error', 'message' => 'Cannot delete variation with existing values']);
        }

        $variation->delete();
        return response()->json(['status' => 'success', 'message' => 'Variation deleted successfully']);
    }

    /////////// DELETE VARIATION VALUE //////////////////
    public function deleteVariationValue(Request $request)
    {
        $variationValue = \App\VariationValue::find($request->variation_value_id);
        if (!$variationValue) {
            return response()->json(['status' => 'error', 'message' => 'Variation value not found']);
        }

        $variationValue->delete();
        return response()->json(['status' => 'success', 'message' => 'Variation value deleted successfully']);
    }

    /////////// TOGGLE VARIATION STATUS //////////////////
    public function toggleVariationStatus(Request $request)
    {
        $variation = \App\Variation::find($request->variation_id);
        if (!$variation) {
            return response()->json(['status' => 'error', 'message' => 'Variation not found']);
        }

        $variation->is_active = $variation->is_active == STATIC_DATA_MODEL::$Active ? STATIC_DATA_MODEL::$Inactive : STATIC_DATA_MODEL::$Active;
        $variation->updated_by = session('logged_user_id');
        $variation->save();

        $statusText = $variation->is_active == STATIC_DATA_MODEL::$Active ? 'activated' : 'deactivated';
        return response()->json(['status' => 'success', 'message' => "Variation $statusText successfully", 'is_active' => $variation->is_active]);
    }

    /////////// TOGGLE VARIATION VALUE STATUS //////////////////
    public function toggleVariationValueStatus(Request $request)
    {
        $variationValue = \App\VariationValue::find($request->variation_value_id);
        if (!$variationValue) {
            return response()->json(['status' => 'error', 'message' => 'Variation value not found']);
        }

        $variationValue->is_active = $variationValue->is_active == STATIC_DATA_MODEL::$Active ? STATIC_DATA_MODEL::$Inactive : STATIC_DATA_MODEL::$Active;
        $variationValue->updated_by = session('logged_user_id');
        $variationValue->save();

        $statusText = $variationValue->is_active == STATIC_DATA_MODEL::$Active ? 'activated' : 'deactivated';
        return response()->json(['status' => 'success', 'message' => "Variation value $statusText successfully", 'is_active' => $variationValue->is_active]);
    }

    /////////// LOAD SUB CATEGORIES BY MAIN CATEGORY //////////////////
    public function loadSubCategoriesByMainCategory(Request $request)
    {
        $subCategories = SubCategory::where('pm_product_main_category_id', $request->main_category_id)
                                    ->where('is_active', STATIC_DATA_MODEL::$Active)
                                    ->get();
        return response()->json(['status' => 'success', 'data' => $subCategories]);
    }

    /////////// LOAD VARIATION VALUES BY VARIATION //////////////////
    public function loadVariationValuesByVariation(Request $request)
    {
        $variationValues = \App\VariationValue::where('pm_variation_id', $request->variation_id)
                                              ->where('is_active', STATIC_DATA_MODEL::$Active)
                                              ->get();
        
        // Get variation value types from STATIC_DATA_MODEL
        $variationValueTypes = [
            \App\STATIC_DATA_MODEL::$variationValueTypeL => 'L',
            \App\STATIC_DATA_MODEL::$variationValueTypeML => 'ML',
            \App\STATIC_DATA_MODEL::$variationValueTypeG => 'G',
            \App\STATIC_DATA_MODEL::$variationValueTypeKG => 'KG'
        ];
        
        return response()->json(['status' => 'success', 'data' => $variationValues, 'types' => $variationValueTypes]);
    }

    /////////// SAVE PRODUCT ITEMS //////////////////
    public function saveProductItems(Request $request)
    {
        $this->validate($request, [
            'products' => 'required|array',
            'products.*.product_name' => 'required',
            'products.*.product_code' => 'required',
            'products.*.pm_product_item_type_id' => 'required',
            'products.*.pm_product_main_category_id' => 'required',
            'products.*.pm_product_sub_category_id' => 'required',
            'products.*.selling_price' => 'required|numeric',
            'products.*.cost_price' => 'required|numeric',
        ]);

        $logged_user = session('logged_user_id');
        $savedProducts = 0;

        foreach ($request->products as $productData) {
            // Check if product code already exists
            if (\App\ProductItem::where('product_code', $productData['product_code'])->exists()) {
                continue; // Skip this product
            }

            $productItem = new \App\ProductItem();
            $productItem->product_name = $productData['product_name'];
            $productItem->product_description = $productData['product_description'] ?? '';
            $productItem->product_code = $productData['product_code'];
            $productItem->pm_product_item_type_id = $productData['pm_product_item_type_id'];
            $productItem->pm_product_main_category_id = $productData['pm_product_main_category_id'];
            $productItem->pm_product_sub_category_id = $productData['pm_product_sub_category_id'];
            $productItem->pm_product_item_variation_id = $productData['pm_product_item_variation_id'] ?? null;
            $productItem->pm_product_item_variation_value_id = $productData['pm_product_item_variation_value_id'] ?? null;
            $productItem->selling_price = $productData['selling_price'];
            $productItem->cost_price = $productData['cost_price'];
            $productItem->status = $productData['status'] ?? \App\STATIC_DATA_MODEL::$Active;
            $productItem->created_by = $logged_user;
            $productItem->updated_by = $logged_user;
            $productItem->save();

            $savedProducts++;
        }

        if ($savedProducts > 0) {
            return response()->json(['status' => 'success', 'message' => "$savedProducts products saved successfully"]);
        } else {
            return response()->json(['status' => 'error', 'message' => 'No products were saved. They may already exist.']);
        }
    }

    /////////// GET VARIATION VALUES //////////////////
    public function getVariationValues(Request $request)
    {
        $variationValues = \App\VariationValue::where('pm_variation_id', $request->variation_id)->get();
        return response()->json(['status' => 'success', 'data' => $variationValues]);
    }

    /////////// GET VARIATION VALUE //////////////////
    public function getVariationValue(Request $request)
    {
        $variationValue = \App\VariationValue::find($request->variation_value_id);
        if (!$variationValue) {
            return response()->json(['status' => 'error', 'message' => 'Variation value not found']);
        }
        return response()->json(['status' => 'success', 'data' => $variationValue]);
    }
}
