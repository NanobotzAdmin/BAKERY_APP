<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $um_user_id
 * @property string $route_name
 * @property string $route_description
 * @property boolean $is_active
 * @property string $created_at
 * @property string $updated_at
 * @property UmUser $umUser
 * @property CmCustomer[] $cmCustomers
 * @property DmDeliveryVehicle[] $dmDeliveryVehicles
 */
class Routes extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cm_routes';
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = ['um_user_id', 'route_name', 'route_description', 'is_active', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function umUser()
    {
        return $this->belongsTo('App\UmUser');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cmCustomers()
    {
        return $this->hasMany('App\CmCustomer', 'cm_routes_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dmDeliveryVehicles()
    {
        return $this->hasMany('App\DmDeliveryVehicle', 'cm_routes_id');
    }
}
