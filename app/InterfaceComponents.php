<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $pm_interfaces_id
 * @property int $created_by
 * @property string $components_name
 * @property string $component_id
 * @property boolean $is_active
 * @property string $created_at
 * @property string $updated_at
 * @property PmInterface $pmInterface
 * @property UmUser $umUser
 * @property PmInterfaceComponentHistory[] $pmInterfaceComponentHistories
 * @property PmUserRoleHasInterfaceComponent[] $pmUserRoleHasInterfaceComponents
 * @property UmUserHasInterfaceComponent[] $umUserHasInterfaceComponents
 */
class InterfaceComponents extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pm_interface_components';
    public $timestamps = false;


    /**
     * @var array
     */
    protected $fillable = ['pm_interfaces_id', 'created_by', 'components_name', 'component_id', 'is_active', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pmInterface()
    {
        return $this->belongsTo(Interfaces::class, 'pm_interfaces_id');
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
    public function pmInterfaceComponentHistories()
    {
        return $this->hasMany('App\PmInterfaceComponentHistory', 'pm_interface_components_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pmUserRoleHasInterfaceComponents()
    {
        return $this->hasMany('App\PmUserRoleHasInterfaceComponent', 'pm_interface_components_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function umUserHasInterfaceComponents()
    {
        return $this->hasMany('App\UmUserHasInterfaceComponent', 'pm_interface_components_id');
    }
}
