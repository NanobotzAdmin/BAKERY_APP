<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $pm_product_item_state_id
 * @property int $pm_product_sub_category_id
 * @property int $created_by
 * @property string $batch_code
 * @property float $stock_in_quantity
 * @property float $available_quantity
 * @property float $stock_returned_quantity
 * @property string $returned_date
 * @property float $retail_price
 * @property float $selling_price
 * @property float $actual_cost
 * @property float $discountable_qty
 * @property float $discounted_price
 * @property string $stock_date
 * @property string $expire_date
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $is_active
 * @property boolean $is_visible
 * @property PmProductItemState $pmProductItemState
 * @property PmProductSubCategory $pmProductSubCategory
 * @property UmUser $umUser
 * @property DmDeliveryVehicleHasStockBatch[] $dmDeliveryVehicleHasStockBatches
 * @property DmReturn[] $dmReturns
 */
class StockBatch extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pm_stock_batch';
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = ['pm_product_item_state_id', 'pm_product_sub_category_id', 'created_by', 'batch_code', 'stock_in_quantity', 'available_quantity', 'stock_returned_quantity', 'returned_date', 'retail_price', 'selling_price', 'actual_cost', 'discountable_qty', 'discounted_price', 'stock_date', 'expire_date', 'created_at', 'updated_at', 'is_active', 'is_visible'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    // public function pmProductItemState()
    // {
    //     return $this->belongsTo('App\PmProductItemState');
    // }

    public function pmProductItemState()
    {
        return $this->belongsTo(ProductItemState::class, 'pm_product_item_state_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pmProductSubCategory()
    {
        return $this->belongsTo('App\PmProductSubCategory');
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
    public function dmDeliveryVehicleHasStockBatches()
    {
        return $this->hasMany('App\DmDeliveryVehicleHasStockBatch');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dmReturns()
    {
        return $this->hasMany('App\DmReturn');
    }
}
