<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $created_by
 * @property string $sales_rep_name
 * @property string $nic_no
 * @property string $contact_no
 * @property boolean $is_active
 * @property string $created_at
 * @property string $updated_at
 * @property UmUser $umUser
 * @property DmDeliveryVehicle[] $dmDeliveryVehicles
 */
class SaleRep extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vm_sales_reps';
    public $timestamps = false;


    /**
     * @var array
     */
    protected $fillable = ['sales_rep_name', 'nic_no', 'contact_no', 'is_active', 'created_at', 'updated_at', 'created_by', 'um_user_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function umCreatedUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function umUser()
    {
        return $this->belongsTo(User::class, 'um_user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dmDeliveryVehicles()
    {
        return $this->hasMany('App\DmDeliveryVehicle', 'vm_sales_reps_id');
    }
}
