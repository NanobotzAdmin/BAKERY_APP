<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $created_by
 * @property string $user_role_name
 * @property boolean $is_active
 * @property string $created_at
 * @property string $updated_at
 * @property UmUser $umUser
 * @property PmInterfaceComponentHistory[] $pmInterfaceComponentHistories
 * @property PmUserRoleHasInterfaceComponent[] $pmUserRoleHasInterfaceComponents
 * @property UmUser[] $umUsers
 */
class UserRole extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pm_user_role';
    public $timestamps = false;


    /**
     * @var array
     */
    protected $fillable = ['created_by', 'user_role_name', 'is_active', 'created_at', 'updated_at'];

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
        return $this->hasMany('App\PmInterfaceComponentHistory');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pmUserRoleHasInterfaceComponents()
    {
        return $this->hasMany('App\PmUserRoleHasInterfaceComponent');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function umUsers()
    {
        return $this->hasMany('App\UmUser');
    }
}
