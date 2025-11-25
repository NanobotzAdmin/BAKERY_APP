<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductHasIngredients extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pm_product_has_ingredients';

    /**
     * @var array
     */
    protected $fillable = [
        'pm_product_item_id',
        'pm_raw_material_id',
        'pm_variation_value_type_id',
        'pm_variation_value',
        'status',
        'created_by',
        'updated_by'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(ProductItem::class, 'pm_product_item_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rawMaterial()
    {
        return $this->belongsTo(Product::class, 'pm_raw_material_id');
    }

    // Variation value metadata is stored via static data (pm_variation_value_type_id),
    // therefore no Eloquent relation is required here.
}

