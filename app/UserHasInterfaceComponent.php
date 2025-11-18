<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $um_user_id
 * @property int $pm_interface_components_id
 * @property int $created_by
 * @property string $created_at
 * @property string $updated_at
 * @property UmUser $umUser
 * @property PmInterfaceComponent $pmInterfaceComponent
 * @property UmUser $umUser
 */
class UserHasInterfaceComponent extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'um_user_has_interface_components';
    public $timestamps = false;

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
    public function umUser1()
    {
        return $this->belongsTo('App\UmUser');
    }
}
