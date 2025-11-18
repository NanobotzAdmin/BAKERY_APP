<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $um_user_login_id
 * @property string $ip_address
 * @property string $time_in
 * @property string $time_out
 * @property boolean $is_active
 * @property string $created_at
 * @property string $updated_at
 * @property UmUserLogin $umUserLogin
 * @property SmSessionActivity[] $smSessionActivities
 */
class SmSession extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sm_session';
    public $timestamps = false;


    /**
     * @var array
     */
    protected $fillable = ['um_user_login_id', 'ip_address', 'time_in', 'time_out', 'is_active', 'created_at', 'updated_at'];

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
    public function smSessionActivities()
    {
        return $this->hasMany('App\SmSessionActivity');
    }
}
