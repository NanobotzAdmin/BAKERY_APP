<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\StockBatch;
use App\Product;
use App\ProductItem;
use App\SubCategory;
use App\MainCategory;
use App\ProductHasIngredients;
use App\STATIC_DATA_MODEL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

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
        $subCategory = SubCategory::with('pmProductMainCategory')->get();
        $variations = \App\Variation::with('variationValues')->get();
        
        // Get variation value types from STATIC_DATA_MODEL
        $variationValueTypes = [];
        foreach (\App\STATIC_DATA_MODEL::$variationValueType as $type) {
            $variationValueTypes[$type['id']] = $type['name'];
        }
        
        return view('Products.category.categoryVariationManagement', compact('MainCategory', 'mainActiveCategory', 'subCategory', 'variations', 'variationValueTypes'));
    }

    public function adminProductRegistrationIndex()
    {
        $mainCategories = MainCategory::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        $productItemTypes = [];
        foreach (\App\STATIC_DATA_MODEL::$productItemTypes as $type) {
            $productItemTypes[$type['id']] = $type['name'];
        }
        $variations = \App\Variation::where('is_active', STATIC_DATA_MODEL::$Active)->get();

        $variationValueTypes = [];
        foreach (\App\STATIC_DATA_MODEL::$variationValueType as $type) {
            $variationValueTypes[$type['id']] = $type['name'];
        }

        $brands = collect();
        if (Schema::hasTable('pm_brands')) {
            $brandColumns = Schema::getColumnListing('pm_brands');
            $labelColumn = in_array('brand_name', $brandColumns)
                ? 'brand_name'
                : (in_array('name', $brandColumns) ? 'name' : null);

            $brandQuery = DB::table('pm_brands')->select('id');
            if ($labelColumn) {
                $brandQuery->addSelect($labelColumn . ' as label');
            } else {
                $brandQuery->addSelect(DB::raw("CONCAT('Brand ', id) as label"));
            }

            if (in_array('is_active', $brandColumns)) {
                $brandQuery->where('is_active', STATIC_DATA_MODEL::$Active);
            }

            $brands = $brandQuery->orderBy('label')->get();
        }

        return view('Products.category.productRegistration', compact('mainCategories', 'productItemTypes', 'variations', 'brands', 'variationValueTypes'));
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
        ]);

        if (SubCategory::where('sub_category_name', $request->subCategoryName)->exists()) {
            session()->flash('message', '<i class="fa fa-exclamation-circle" style="color: red;"></i> <b>Sub Category Saving Stopped !</b> <br> &nbsp;&nbsp;&nbsp; <i>Sub Category Name already exists.</i>');
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
            $subCategory->created_by = $logged_user;
            $subCategorysaved = $subCategory->save();

            //Get last record user login
            $lastsubCategoryId = DB::table('pm_product_sub_category')->latest()->first();

            if (!$subCategorysaved) {
                session()->flash('message', '<i class="fa fa-exclamation-circle" style="color: red;"></i> <b>Sub Category saving stopped !</b> <br> &nbsp;&nbsp;&nbsp; <i>Something went wrong! New Sub Category failed to save..</i>');
                session()->flash('flash_message_type', 'alert-danger');
                return redirect()->back();
            } else {
                //Save user activity
                $userActivity = new UserActivityManagementController();
                $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "New Sub Category " . $lastsubCategoryId->id . " Saved.");

                session()->flash('message', '<i class="fa fa-check-circle"></i> <b>Sub Category save successful</b> <br> &nbsp;&nbsp;&nbsp; <i>New Sub Category: "'.$request->subCategoryName.'" has been successfully saved.</i>');
                session()->flash('flash_message_type', 'alert-success');
                return redirect()->back();
            }
        }
    }


    public function updateSubCategory(Request $request)
    {
        $this->validate($request, [
            'MODAL_SUBCATEGORY_NAME' => 'required',
            'MODAL_SUBCATEGORY_MAINCATEGORY_SELECT' => 'required|not_in:0',
        ]);

        $checkSubCategoryName = SubCategory::find($request->MODAL_SUBCATEGORY_UPDATE_ID);
        $categoryStatus = true;

        if (SubCategory::where('sub_category_name', $request->MODAL_SUBCATEGORY_NAME)->exists()) {
            if ($checkSubCategoryName->sub_category_name == $request->MODAL_SUBCATEGORY_NAME) {
                $categoryStatus = true;
            } else {
                $categoryStatus = false;
            }
        } else {
            $categoryStatus = true;
        }

        if ($categoryStatus) {
            $subCategoryUpdate = SubCategory::find($request->MODAL_SUBCATEGORY_UPDATE_ID);
            $subCategoryUpdate->sub_category_name = $request->MODAL_SUBCATEGORY_NAME;
            $subCategoryUpdate->pm_product_main_category_id = $request->MODAL_SUBCATEGORY_MAINCATEGORY_SELECT;
            $subCategoryUpdate->updated_at = Carbon::now();
            $subCategoryUpdatesaved = $subCategoryUpdate->save();

            if (!$subCategoryUpdatesaved) {
                session()->flash('message', '<i class="fa fa-exclamation-circle" style="color: red;"></i> <b>Sub Category update failed</b> <br> &nbsp;&nbsp;&nbsp; <i>Something went wrong! Sub Category failed to update..</i>');
                session()->flash('flash_message_type', 'alert-danger');
                return redirect()->back();
            } else {
                //Save user activity
                $userActivity = new UserActivityManagementController();
                $userActivity->saveActivity(STATIC_DATA_MODEL::$update, "Update Sub Category " . $request->MODAL_SUBCATEGORY_UPDATE_ID . " Updated.");

                session()->flash('message', '<i class="fa fa-check-circle" style="color:green;"></i> <b>Sub Category update successful</b> <br> &nbsp;&nbsp;&nbsp; <i>Sub Category: "'.$subCategoryUpdate->sub_category_name.'" details have been successfully updated.</i>');
                session()->flash('flash_message_type', 'alert-success');
                return redirect()->back();
            }
        } else {
            session()->flash('message', '<i class="fa fa-exclamation-circle" style="color:red;"></i> <b>Sub Category updating stopped !</b> <br> &nbsp;&nbsp;&nbsp; <i>Sub Category Name already exists!</i>');
            session()->flash('flash_message_type', 'alert-warning');
            return redirect()->back();
        }
    }


    ////////// load sub Categories ////////////////
    public function loadSubCategories(Request $request)
    {
        $products = SubCategory::where('pm_product_main_category_id', $request->MainCategory)
                                ->where('is_active', STATIC_DATA_MODEL::$Active)
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
        $variationValueTypes = [];
        foreach (\App\STATIC_DATA_MODEL::$variationValueType as $type) {
            $variationValueTypes[$type['id']] = $type['name'];
        }
        
        return response()->json(['status' => 'success', 'data' => $variationValues, 'types' => $variationValueTypes]);
    }

    /////////// SAVE PRODUCT ITEMS //////////////////
    public function saveProductItems(Request $request)
    {
        $validProductItemTypes = [];
        foreach (\App\STATIC_DATA_MODEL::$productItemTypes as $type) {
            $validProductItemTypes[] = $type['id'];
        }

        $brandRule = Schema::hasTable('pm_brands') ? 'nullable|exists:pm_brands,id' : 'nullable';

        $this->validate($request, [
            'product' => 'required|array',
            'product.pm_brands_id' => $brandRule,
            'product.product_name' => 'required|string|max:150',
            'product.product_code' => 'required|string|max:50|unique:pm_product,product_code',
            'product.product_description' => 'nullable|string',
            'product.pm_product_item_type_id' => 'required|in:' . implode(',', $validProductItemTypes),
            'product.pm_product_main_category_id' => 'required|exists:pm_product_main_category,id',
            'product.pm_product_sub_category_id' => 'required|exists:pm_product_sub_category,id',
            'items' => 'required|array|min:1',
            'items.*.product_item_name' => 'required|string|max:150',
            'items.*.bin_code' => 'required|string|max:50|distinct|unique:pm_product_item,bin_code',
            'items.*.pm_product_item_variation_id' => 'nullable|exists:pm_variation,id',
            'items.*.pm_product_item_variation_value_id' => 'nullable|exists:pm_variation_value,id',
            'items.*.selling_price' => 'nullable|numeric|min:0',
            'items.*.cost_price' => 'nullable|numeric|min:0',
        ]);

        $logged_user = session('logged_user_id');
        $productPayload = $request->input('product');
        $itemsPayload = $request->input('items');
        $savedItems = 0;

        DB::beginTransaction();

        try {
            $product = Product::create([
                'pm_brands_id' => $productPayload['pm_brands_id'] ?? null,
                'product_name' => $productPayload['product_name'],
                'product_code' => $productPayload['product_code'],
                'product_description' => $productPayload['product_description'] ?? null,
                'is_active' => STATIC_DATA_MODEL::$Active,
                'pm_product_item_type_id' => $productPayload['pm_product_item_type_id'],
                'pm_product_main_category_id' => $productPayload['pm_product_main_category_id'],
                'pm_product_sub_category_id' => $productPayload['pm_product_sub_category_id'],
                'created_by' => $logged_user,
                'updated_by' => $logged_user,
            ]);

            foreach ($itemsPayload as $itemData) {
                if (ProductItem::where('bin_code', $itemData['bin_code'])->exists()) {
                    continue;
                }

                ProductItem::create([
                    'pm_product_id' => $product->id,
                    'product_item_name' => $itemData['product_item_name'],
                    'bin_code' => $itemData['bin_code'],
                    'pm_product_item_type_id' => $product->pm_product_item_type_id,
                    'pm_product_main_category_id' => $product->pm_product_main_category_id,
                    'pm_product_sub_category_id' => $product->pm_product_sub_category_id,
                    'pm_product_item_variation_id' => $itemData['pm_product_item_variation_id'] ?? null,
                    'pm_product_item_variation_value_id' => $itemData['pm_product_item_variation_value_id'] ?? null,
                    'selling_price' => $itemData['selling_price'] ?? 0,
                    'cost_price' => $itemData['cost_price'] ?? 0,
                    'status' => $itemData['status'] ?? STATIC_DATA_MODEL::$Active,
                    'created_by' => $logged_user,
                    'updated_by' => $logged_user,
                ]);

                $savedItems++;
            }

            if ($savedItems === 0) {
                DB::rollBack();
                return response()->json(['status' => 'error', 'message' => 'No product items were saved. They may already exist.']);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Failed to save product with items', [
                'error' => $th->getMessage(),
            ]);
            return response()->json(['status' => 'error', 'message' => 'Failed to save product. Please try again.'], 500);
        }

        return response()->json(['status' => 'success', 'message' => "$savedItems product items saved successfully"]);
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

    /////////// ADMIN PRODUCT INGREDIENTS MANAGEMENT INDEX //////////////////
    public function adminProductIngredientsManagementIndex()
    {
        $sellingProducts = ProductItem::with(['mainCategory', 'subCategory', 'variation', 'variationValue'])
            ->where('pm_product_item_type_id', STATIC_DATA_MODEL::$productItemTypes[0]['id'])
            ->where('status', STATIC_DATA_MODEL::$Active)
            ->get();

        $rawMaterials = Product::with(['items' => function ($query) {
                $query->with(['variation.variationValues', 'variationValue'])
                    ->where('status', STATIC_DATA_MODEL::$Active);
            }])
            ->where('pm_product_item_type_id', STATIC_DATA_MODEL::$productItemTypes[1]['id'])
            ->where('is_active', STATIC_DATA_MODEL::$Active)
            ->get()
            ->map(function ($rawMaterial) {
                return $this->enrichProductWithVariationMetadata($rawMaterial);
            });

        $variationValueTypesCollection = collect(STATIC_DATA_MODEL::$variationValueType ?? [])
            ->map(function ($type) {
                return [
                    'id' => $type['id'],
                    'name' => $type['name'],
                ];
            });

        $variationValueTypes = $variationValueTypesCollection->values()->all();
        $variationValueTypeMap = $variationValueTypesCollection->keyBy('id')->toArray();

        return view('Products.ingredient.manageIngredients', compact('sellingProducts', 'rawMaterials', 'variationValueTypes', 'variationValueTypeMap'));
    }

    /**
     * Fetch existing ingredient mappings for a specific selling product.
     *
     * @param  int  $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductIngredients($productId)
    {
        $productItem = ProductItem::find($productId);

        if (!$productItem) {
            return response()->json([
                'status' => 'error',
                'message' => 'Selling product not found.',
            ], 404);
        }

        $ingredients = ProductHasIngredients::with(['rawMaterial.items.variation.variationValues', 'rawMaterial.items.variationValue'])
            ->where('pm_product_item_id', $productId)
            ->where('status', STATIC_DATA_MODEL::$Active)
            ->orderBy('id')
            ->get()
            ->map(function ($ingredient) {
                $ingredient->rawMaterial = $this->enrichProductWithVariationMetadata($ingredient->rawMaterial);
                return [
                    'raw_material_id' => $ingredient->pm_raw_material_id,
                    'variation_value_type_id' => $ingredient->pm_variation_value_type_id,
                    'variation_value' => $ingredient->pm_variation_value,
                    'raw_material' => $ingredient->rawMaterial,
                ];
            })
            ->values();

        return response()->json([
            'status' => 'success',
            'product_id' => $productId,
            'ingredients' => $ingredients,
        ]);
    }

    /**
     * Save ingredient mappings for selling products.
     */
    public function saveProductIngredients(Request $request)
    {
        $this->validate($request, [
            'products' => 'required|array|min:1',
            'products.*.product_item_id' => 'required|exists:pm_product_item,id',
            'products.*.ingredients' => 'required|array|min:1',
            'products.*.ingredients.*.raw_material_id' => 'required|exists:pm_product,id',
            'products.*.ingredients.*.variation_value_type_id' => 'required|integer',
            'products.*.ingredients.*.variation_value' => 'required|numeric|min:0.0001',
        ]);

        $loggedUser = session('logged_user_id');

        if (!$loggedUser) {
            return response()->json([
                'status' => 'error',
                'message' => 'Session expired. Please login again.',
            ], 401);
        }

        DB::beginTransaction();

        try {
            $allowedVariationValueTypeIds = collect(STATIC_DATA_MODEL::$variationValueType ?? [])->pluck('id')->map(function ($id) {
                return (int) $id;
            })->all();

            foreach ($request->products as $productData) {
                $productId = $productData['product_item_id'];

                $existingIngredients = ProductHasIngredients::where('pm_product_item_id', $productId)
                    ->orderByDesc('id')
                    ->get();

                $activeIngredients = $existingIngredients
                    ->filter(function ($ingredient) {
                        return (int) $ingredient->status === STATIC_DATA_MODEL::$Active;
                    })
                    ->keyBy('pm_raw_material_id');

                $processedRawMaterialIds = [];

                foreach ($productData['ingredients'] as $ingredientData) {
                    $rawMaterial = Product::with(['items' => function ($query) {
                            $query->with(['variation.variationValues', 'variationValue'])
                                ->where('status', STATIC_DATA_MODEL::$Active);
                        }])->find($ingredientData['raw_material_id']);

                    if (!$rawMaterial) {
                        throw new \Exception('Selected raw material not found.');
                    }

                    $typeId = (int) $ingredientData['variation_value_type_id'];
                    if (!in_array($typeId, $allowedVariationValueTypeIds, true)) {
                        throw new \Exception('Invalid variation value type selected.');
                    }

                    $rawMaterial = $this->enrichProductWithVariationMetadata($rawMaterial);
                    $materialTypeIds = $rawMaterial->available_variation_type_ids ?? [];

                    if (empty($materialTypeIds) || !in_array($typeId, $materialTypeIds, true)) {
                        throw new \Exception('Selected variation value type is not available for the chosen raw material.');
                    }

                    $processedRawMaterialIds[] = (int) $rawMaterial->id;
                    $existingActiveRecord = $activeIngredients->get($rawMaterial->id);
                    $incomingValue = round((float) $ingredientData['variation_value'], 4);
                    $existingValue = $existingActiveRecord ? round((float) $existingActiveRecord->pm_variation_value, 4) : null;

                    if (
                        $existingActiveRecord &&
                        (int) $existingActiveRecord->pm_variation_value_type_id === $typeId &&
                        $existingValue === $incomingValue
                    ) {
                        continue;
                    }

                    if ($existingActiveRecord) {
                        $existingActiveRecord->status = STATIC_DATA_MODEL::$Inactive;
                        $existingActiveRecord->updated_by = $loggedUser;
                        $existingActiveRecord->save();
                    }

                    ProductHasIngredients::create([
                        'pm_product_item_id' => $productId,
                        'pm_raw_material_id' => $rawMaterial->id,
                        'pm_variation_value_type_id' => $typeId,
                        'pm_variation_value' => $incomingValue,
                        'status' => STATIC_DATA_MODEL::$Active,
                        'created_by' => $loggedUser,
                        'updated_by' => $loggedUser,
                    ]);
                }

                foreach ($activeIngredients as $rawMaterialId => $existingActiveRecord) {
                    if (!in_array((int) $rawMaterialId, $processedRawMaterialIds, true)) {
                        $existingActiveRecord->status = STATIC_DATA_MODEL::$Inactive;
                        $existingActiveRecord->updated_by = $loggedUser;
                        $existingActiveRecord->save();
                    }
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Product ingredients saved successfully.',
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save product ingredients. ' . $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Ensure the given product carries variation metadata needed by the UI.
     */
    private function enrichProductWithVariationMetadata(?Product $product)
    {
        if (!$product) {
            return $product;
        }

        $product->loadMissing(['items' => function ($query) {
            $query->with(['variation.variationValues', 'variationValue'])
                ->where('status', STATIC_DATA_MODEL::$Active);
        }]);

        $product->setAttribute('available_variation_type_ids', $this->collectVariationTypeIdsFromProduct($product));

        return $product;
    }

    /**
     * Collect all variation value type ids available for the given product.
     */
    private function collectVariationTypeIdsFromProduct(Product $product)
    {
        if (!$product->relationLoaded('items')) {
            return [];
        }

        return $product->items->flatMap(function ($item) {
            $typeIds = collect();

            if ($item->relationLoaded('variation') && $item->variation) {
                $item->variation->loadMissing('variationValues');
                $typeIds = $typeIds->merge($item->variation->variationValues->pluck('pm_variation_value_type_id'));
            }

            if ($item->relationLoaded('variationValue') && $item->variationValue) {
                $typeIds->push($item->variationValue->pm_variation_value_type_id);
            }

            return $typeIds;
        })
        ->filter()
        ->unique()
        ->values()
        ->map(function ($id) {
            return (int) $id;
        })
        ->all();
    }
}
