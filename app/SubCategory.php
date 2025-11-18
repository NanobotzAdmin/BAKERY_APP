<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $created_by
 * @property int $pm_product_main_category_id
 * @property string $sub_category_name
 * @property boolean $is_active
 * @property string $created_at
 * @property string $updated_at
 * @property int $expire_in_days
 * @property string $product_code
 * @property float $selling_price
 * @property float $actual_cost
 * @property float $retail_price
 * @property float $discountable_qty
 * @property float $discounted_price
 * @property PmProductMainCategory $pmProductMainCategory
 * @property UmUser $umUser
 * @property PmRawMaterial[] $pmRawMaterials
 * @property PmStockBatch[] $pmStockBatches
 */
class SubCategory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pm_product_sub_category';
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = ['created_by', 'pm_product_main_category_id', 'sub_category_name', 'is_active', 'created_at', 'updated_at', 'expire_in_days', 'product_code', 'selling_price', 'actual_cost', 'retail_price', 'discountable_qty', 'discounted_price'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pmProductMainCategory()
    {
        return $this->belongsTo('App\PmProductMainCategory');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function umUser()
    {
        return $this->belongsTo('App\UmUser', 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pmRawMaterials()
    {
        return $this->hasMany('App\PmRawMaterial');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pmStockBatches()
    {
        return $this->hasMany('App\PmStockBatch');
    }
}
