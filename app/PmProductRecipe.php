<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PmProductRecipe extends Model
{
    protected $table = 'pm_product_recipe';

    protected $fillable = [
        'recipe_name',
        'category',
        'product_type',
        'description',
        'image',
        'pm_product_item_id',
        'yield',
        'pm_variation_value_type_id',
        'status',
        'created_by',
        'updated_by'
    ];

    public function steps()
    {
        return $this->hasMany(PmRecipeHasSteps::class, 'pm_product_recipe_id');
    }

    public function ingredients()
    {
        return $this->hasMany(PmProductRecipeHasIngredients::class, 'pm_product_recipe_id');
    }

    public function productItem()
    {
        return $this->belongsTo(ProductItem::class, 'pm_product_item_id');
    }
}
