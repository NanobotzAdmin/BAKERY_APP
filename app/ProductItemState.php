<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $created_by
 * @property string $item_name
 * @property boolean $is_active
 * @property string $created_date
 * @property string $updated_date
 * @property UmUser $umUser
 * @property PmStockBatch[] $pmStockBatches
 */
class ProductItemState extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pm_product_item_state';
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = ['created_by', 'item_name', 'is_active', 'created_date', 'updated_date'];

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
    public function pmStockBatches()
    {
        return $this->hasMany('App\PmStockBatch');
    }
}
