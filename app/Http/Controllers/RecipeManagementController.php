<?php

namespace App\Http\Controllers;

use App\PmProductRecipe;
use App\ProductItem;
use Illuminate\Http\Request;

class RecipeManagementController extends Controller
{
    public function index()
    {
        $recipe = PmProductRecipe::with(['productItem.mainCategory', 'ingredients.productItem', 'steps'])->get();
        $products = ProductItem::all();
        $variationValueTypes = \App\STATIC_DATA_MODEL::$variationValueType;
        return view('Products.Recipe Management.recipe-management', compact('products', 'recipe', 'variationValueTypes'));
    }
    public function store(Request $request)
    {
        try {
            $request->validate([
                'pm_product_item_id' => 'required|exists:pm_product_item,id',
                'recipe_name' => 'required|string|max:255',
                'yield' => 'required|numeric',
                'pm_variation_value_type_id' => 'required|integer',
                'ingredients' => 'required|array',
                'ingredients.*' => 'exists:pm_product_item,id',
                'quantities' => 'required|array',
                'units' => 'required|array',
                'steps' => 'required|array',
            ]);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/recipes'), $imageName);
                $imagePath = 'uploads/recipes/' . $imageName;
            }

            $recipe = PmProductRecipe::create([
                'pm_product_item_id' => $request->pm_product_item_id,
                'recipe_name' => $request->recipe_name,
                'description' => $request->description,
                'image' => $imagePath,
                'yield' => $request->yield,
                'pm_variation_value_type_id' => $request->pm_variation_value_type_id,
                'status' => 1, // Active by default
            ]);

            // Save Ingredients
            foreach ($request->ingredients as $key => $ingredientId) {
                $recipe->ingredients()->create([
                    'pm_product_item_id' => $ingredientId, // This should probably be the raw material ID
                    'metirial_product_id' => $ingredientId,
                    'quantity' => $request->quantities[$key],
                    'pm_variation_value_type_id' => 1, // Default unit type, needs adjustment based on 'units' input if applicable
                ]);
            }

            // Save Steps
            foreach ($request->steps as $index => $instruction) {
                $recipe->steps()->create([
                    'step_number' => $index + 1,
                    'instruction' => $instruction,
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Recipe created successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
