<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $vm_vehicles_id
 * @property int $vm_drivers_id
 * @property int $vm_sales_reps_id
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property boolean $status
 * @property string $updated_at
 * @property string $end_date
 * @property float $start_milage
 * @property float $end_milage
 * @property string $delivery_route
 * @property VmDriver $vmDriver
 * @property VmSalesRep $vmSalesRep
 * @property VmVehicle $vmVehicle
 * @property UmUser $umUser
 * @property UmUser $umUser
 * @property DmDeliveryVehicleHasStockBatch[] $dmDeliveryVehicleHasStockBatches
 */
class DeliveryVehicle extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dm_delivery_vehicle';
    public $timestamps = false;


    /**
     * @var array
     */
    protected $fillable = ['vm_vehicles_id', 'vm_drivers_id', 'vm_sales_reps_id', 'created_by', 'updated_by', 'created_at', 'status', 'updated_at', 'end_date', 'start_milage', 'end_milage', 'delivery_route'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vmDriver()
    {
        return $this->belongsTo('App\VmDriver', 'vm_drivers_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vmSalesRep()
    {
        return $this->belongsTo('App\VmSalesRep', 'vm_sales_reps_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vmVehicle()
    {
        return $this->belongsTo('App\VmVehicle', 'vm_vehicles_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function umUser1()
    {
        return $this->belongsTo('App\UmUser', 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function umUser()
    {
        return $this->belongsTo('App\UmUser', 'updated_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dmDeliveryVehicleHasStockBatches()
    {
        return $this->hasMany('App\DmDeliveryVehicleHasStockBatch');
    }
}
