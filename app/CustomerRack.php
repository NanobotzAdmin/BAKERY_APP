<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $cm_customers_id
 * @property int $store_rack_count_id
 * @property int $created_by
 * @property int $rack_count
 * @property string $created_at
 * @property string $updated_at
 * @property CmCustomer $cmCustomer
 * @property PmStoreRackCount $pmStoreRackCount
 * @property UmUser $umUser
 */
class CustomerRack extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pm_customers_has_rack_count';
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = ['created_by', 'rack_count', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cmCustomer()
    {
        return $this->belongsTo('App\CmCustomer', 'cm_customers_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pmStoreRackCount()
    {
        return $this->belongsTo('App\PmStoreRackCount', 'store_rack_count_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function umUser()
    {
        return $this->belongsTo('App\UmUser', 'created_by');
    }
}
