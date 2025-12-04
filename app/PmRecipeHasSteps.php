<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PmRecipeHasSteps extends Model
{
    protected $table = 'pm_recipe_has_steps';

    protected $fillable = [
        'pm_product_recipe_id',
        'step_number',
        'instruction'
    ];

    public function recipe()
    {
        return $this->belongsTo(PmProductRecipe::class, 'pm_product_recipe_id');
    }
}
