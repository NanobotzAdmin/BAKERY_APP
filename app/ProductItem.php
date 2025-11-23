<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductItem extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pm_product_item';

    /**
     * @var array
     */
    protected $fillable = [
        'product_name',
        'product_description',
        'product_code',
        'pm_product_item_type_id',
        'pm_product_main_category_id',
        'pm_product_sub_category_id',
        'pm_product_item_variation_id',
        'pm_product_item_variation_value_id',
        'selling_price',
        'cost_price',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mainCategory()
    {
        return $this->belongsTo(MainCategory::class, 'pm_product_main_category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'pm_product_sub_category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function variation()
    {
        return $this->belongsTo(Variation::class, 'pm_product_item_variation_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function variationValue()
    {
        return $this->belongsTo(VariationValue::class, 'pm_product_item_variation_value_id');
    }
}

