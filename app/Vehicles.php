<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $created_by
 * @property string $reg_number
 * @property string $engine_number
 * @property string $chassis_number
 * @property boolean $is_active
 * @property string $created_at
 * @property string $updated_at
 * @property UmUser $umUser
 * @property DmDeliveryVehicle[] $dmDeliveryVehicles
 */
class Vehicles extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vm_vehicles';
    public $timestamps = false;
    /**
     * @var array
     */
    protected $fillable = ['created_by', 'reg_number', 'engine_number', 'chassis_number', 'is_active', 'created_at', 'updated_at'];

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
    public function dmDeliveryVehicles()
    {
        return $this->hasMany('App\DmDeliveryVehicle', 'vm_vehicles_id');
    }
}
