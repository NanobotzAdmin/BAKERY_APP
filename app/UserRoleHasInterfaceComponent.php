<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $pm_user_role_id
 * @property int $pm_interface_components_id
 * @property int $created_by
 * @property string $created_at
 * @property string $updated_at
 * @property UmUser $umUser
 * @property PmInterfaceComponent $pmInterfaceComponent
 * @property PmUserRole $pmUserRole
 */
class UserRoleHasInterfaceComponent extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'pm_user_role_has_interface_components';

    /**
     * @var array
     */
    protected $fillable = ['created_by', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function umUser()
    {
        return $this->belongsTo('App\UmUser', 'created_by');
    }

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
}
