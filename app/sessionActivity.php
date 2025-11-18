<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $sm_session_id
 * @property string $activity_type
 * @property string $created_at
 * @property string $description
 * @property SmSession $smSession
 */
class sessionActivity extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sm_session_activity';
    public $timestamps = false;


    /**
     * @var array
     */
    protected $fillable = ['sm_session_id', 'activity_type', 'created_at', 'description'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function smSession()
    {
        return $this->belongsTo('App\SmSession');
    }
}
