<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PmProductRecipeHasIngredients extends Model
{
    protected $table = 'pm_product_recipe_has_ingredients';

    protected $fillable = [
        'pm_product_recipe_id',
        'pm_product_item_id',
        'metirial_product_id',
        'pm_variation_value_type_id',
        'quantity',
        'created_by',
        'updated_by'
    ];

    public function recipe()
    {
        return $this->belongsTo(PmProductRecipe::class, 'pm_product_recipe_id');
    }

    public function productItem()
    {
        return $this->belongsTo(ProductItem::class, 'pm_product_item_id');
    }

    public function materialProduct()
    {
        return $this->belongsTo(ProductItem::class, 'metirial_product_id');
    }
}
