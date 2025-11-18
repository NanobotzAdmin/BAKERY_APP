<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $pm_interface_components_id
 * @property int $pm_user_role_id
 * @property int $um_user_id
 * @property int $created_by
 * @property string $created_at
 * @property string $interface_name
 * @property string $component_name
 * @property boolean $is_added
 * @property boolean $is_removed
 * @property string $updated_at
 * @property PmInterfaceComponent $pmInterfaceComponent
 * @property PmUserRole $pmUserRole
 * @property UmUser $umUser
 * @property UmUser $umUser
 */
class ComponentHistory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pm_interface_component_history';
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = ['pm_interface_components_id', 'pm_user_role_id', 'um_user_id', 'created_by', 'created_at', 'interface_name', 'component_name', 'is_added', 'is_removed', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pmInterfaceComponent()
    {
        return $this->belongsTo('App\PmInterfaceComponent', 'pm_interface_components_id');
    }

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
    public function umUser()
    {
        return $this->belongsTo('App\UmUser');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function umUser1()
    {
        return $this->belongsTo('App\UmUser', 'created_by');
    }
}
