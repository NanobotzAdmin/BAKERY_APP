<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $dm_delivery_vehicle_id
 * @property int $pm_stock_batch_id
 * @property int $created_by
 * @property int $loaded_qty
 * @property int $availbale_qty
 * @property int $racks_count
 * @property string $created_at
 * @property string $updated_at
 * @property DmDeliveryVehicle $dmDeliveryVehicle
 * @property PmStockBatch $pmStockBatch
 * @property UmUser $umUser
 */
class DeliveryVehicleHasStock extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dm_delivery_vehicle_has_stock_batch';
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = ['created_by', 'loaded_qty', 'availbale_qty', 'racks_count', 'created_at', 'updated_at', 'system_return_qty', 'physical_return_qty', 'sequence_no'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dmDeliveryVehicle()
    {
        return $this->belongsTo('App\DmDeliveryVehicle');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pmStockBatch()
    {
        return $this->belongsTo('App\PmStockBatch');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function umUser()
    {
        return $this->belongsTo('App\UmUser', 'created_by');
    }
}
