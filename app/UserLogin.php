<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $user_name
 * @property string $password
 * @property string $created_at
 * @property string $updated_at
 * @property SmSession[] $smSessions
 * @property UmUser[] $umUsers
 */
class UserLogin extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'um_user_login';
    public $timestamps = false;


    /**
     * @var array
     */
    protected $fillable = ['user_name', 'password', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function smSessions()
    {
        return $this->hasMany('App\SmSession');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function umUsers()
    {
        return $this->hasMany('App\UmUser');
    }
}
