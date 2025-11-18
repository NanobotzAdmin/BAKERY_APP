<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $updated_by
 * @property int $rack_count
 * @property string $created_at
 * @property string $updated_at
 * @property UmUser $umUser
 * @property PmCustomersHasRackCount[] $pmCustomersHasRackCounts
 */
class StoreRack extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pm_store_rack_count';
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = ['updated_by', 'rack_count', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function umUser()
    {
        return $this->belongsTo('App\UmUser', 'updated_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pmCustomersHasRackCounts()
    {
        return $this->hasMany('App\PmCustomersHasRackCount', 'store_rack_count_id');
    }
}
