<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $um_user_login_id
 * @property int $pm_user_role_id
 * @property string $first_name
 * @property string $last_name
 * @property boolean $is_active
 * @property string $created_at
 * @property string $updated_at
 * @property PmUserRole $pmUserRole
 * @property UmUserLogin $umUserLogin
 * @property CmCustomer[] $cmCustomers
 * @property DmDeliveryVehicle[] $dmDeliveryVehicles
 * @property PmInterfaceComponentHistory[] $pmInterfaceComponentHistories
 * @property PmInterfaceComponentHistory[] $pmInterfaceComponentHistories
 * @property PmInterfaceComponent[] $pmInterfaceComponents
 * @property PmInterfaceTopic[] $pmInterfaceTopics
 * @property PmInterface[] $pmInterfaces
 * @property PmProductItemState[] $pmProductItemStates
 * @property PmProductItem[] $pmProductItems
 * @property PmProductMainCategory[] $pmProductMainCategories
 * @property PmProductSubCategory[] $pmProductSubCategories
 * @property PmRawMaterial[] $pmRawMaterials
 * @property PmUserRole[] $pmUserRoles
 * @property PmUserRoleHasInterfaceComponent[] $pmUserRoleHasInterfaceComponents
 * @property UmForgotPassword[] $umForgotPasswords
 * @property UmUserHasInterfaceComponent[] $umUserHasInterfaceComponents
 * @property UmUserHasInterfaceComponent[] $umUserHasInterfaceComponents
 * @property VmDriver[] $vmDrivers
 * @property VmSalesRep[] $vmSalesReps
 * @property VmVehicle[] $vmVehicles
 */
class User extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'um_user';
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = ['um_user_login_id', 'pm_user_role_id', 'first_name', 'last_name', 'is_active', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pmUserRole()
    {
        return $this->belongsTo('App\PmUserRole');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function umUserLogin()
    {
        return $this->belongsTo('App\UmUserLogin');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cmCustomers()
    {
        return $this->hasMany('App\CmCustomer');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dmDeliveryVehicles()
    {
        return $this->hasMany('App\DmDeliveryVehicle', 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pmInterfaceComponentHistories()
    {
        return $this->hasMany('App\PmInterfaceComponentHistory');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pmInterfaceComponentHistories1()
    {
        return $this->hasMany('App\PmInterfaceComponentHistory', 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pmInterfaceComponents()
    {
        return $this->hasMany('App\PmInterfaceComponent', 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pmInterfaceTopics()
    {
        return $this->hasMany('App\PmInterfaceTopic', 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pmInterfaces()
    {
        return $this->hasMany('App\PmInterface', 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pmProductItemStates()
    {
        return $this->hasMany('App\PmProductItemState', 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pmProductItems()
    {
        return $this->hasMany('App\PmProductItem', 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pmProductMainCategories()
    {
        return $this->hasMany('App\PmProductMainCategory', 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pmProductSubCategories()
    {
        return $this->hasMany('App\PmProductSubCategory', 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pmRawMaterials()
    {
        return $this->hasMany('App\PmRawMaterial', 'added_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pmUserRoles()
    {
        return $this->hasMany('App\PmUserRole', 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pmUserRoleHasInterfaceComponents()
    {
        return $this->hasMany('App\PmUserRoleHasInterfaceComponent', 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function umForgotPasswords()
    {
        return $this->hasMany('App\UmForgotPassword');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function umUserHasInterfaceComponents()
    {
        return $this->hasMany('App\UmUserHasInterfaceComponent', 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function umUserHasInterfaceComponents1()
    {
        return $this->hasMany('App\UmUserHasInterfaceComponent');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vmDrivers()
    {
        return $this->hasMany('App\VmDriver', 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vmSalesReps()
    {
        return $this->hasMany('App\VmSalesRep', 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vmVehicles()
    {
        return $this->hasMany('App\VmVehicle', 'created_by');
    }


 /////////// query scope begin


 public function scopeGetLastRecord($query)
{
    return $query->latest()->first();
}

public function scopeGetUserData($query,  $column, $value)
{
    return $query->where($column, $value)->first();
}


}
