<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $dm_customer_invoice_id
 * @property int $dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id
 * @property int $dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id
 * @property int $pm_product_sub_category_id
 * @property float $quantity
 * @property float $unit_price
 * @property float $total_price
 * @property float $return_qty
 * @property float $return_price
 * @property string $updated_at
 * @property string $created_at
 * @property DmCustomerInvoice $dmCustomerInvoice
 * @property PmProductSubCategory $pmProductSubCategory
 */
class customerInvoiceHasDeletedStock extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dm_customer_invoice_has_stock_batch_deleted';
    public $timestamps = false;
    /**
     * @var array
     */
    protected $fillable = ['dm_customer_invoice_id', 'dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id', 'dm_delivery_vehicle_has_stock_batch_pm_stock_batch_id', 'pm_product_sub_category_id', 'quantity', 'unit_price', 'total_price', 'return_qty', 'return_price', 'updated_at', 'created_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dmCustomerInvoice()
    {
        return $this->belongsTo('App\DmCustomerInvoice');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pmProductSubCategory()
    {
        return $this->belongsTo('App\PmProductSubCategory');
    }
}
