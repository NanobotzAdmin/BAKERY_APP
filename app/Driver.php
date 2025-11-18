<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $created_by
 * @property string $driver_name
 * @property string $licence_no
 * @property string $licence_expireration
 * @property string $contact_number
 * @property boolean $is_active
 * @property string $created_at
 * @property string $updated_at
 * @property UmUser $umUser
 * @property DmDeliveryVehicle[] $dmDeliveryVehicles
 */
class Driver extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vm_drivers';
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = ['created_by', 'driver_name', 'licence_no', 'licence_expireration', 'contact_number', 'is_active', 'created_at', 'updated_at'];

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
        return $this->hasMany('App\DmDeliveryVehicle', 'vm_drivers_id');
    }
}
