<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property float $min_sales_amount
 * @property float $max_sales_amount
 * @property float $commission_rate
 * @property boolean $is_active
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by
 * @property UmUser $umUser
 */
class CommissionSettings extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'st_commission_settings';
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = ['min_sales_amount', 'max_sales_amount', 'commission_rate', 'is_active', 'created_at', 'updated_at', 'created_by'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function umUserCreatedBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
