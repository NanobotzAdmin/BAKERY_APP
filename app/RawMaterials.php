<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $pm_product_sub_category_id
 * @property int $added_by
 * @property string $material_name
 * @property string $added_date
 * @property int $available_count
 * @property int $reorder_count
 * @property boolean $is_active
 * @property string $created_at
 * @property string $updated_at
 * @property PmProductSubCategory $pmProductSubCategory
 * @property UmUser $umUser
 */
class RawMaterials extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pm_raw_materials';
    public $timestamps = false;


    /**
     * @var array
     */
    protected $fillable = ['pm_product_sub_category_id', 'added_by', 'material_name', 'added_date', 'available_count', 'reorder_count', 'is_active', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pmProductSubCategory()
    {
        return $this->belongsTo('App\PmProductSubCategory');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function umUser()
    {
        return $this->belongsTo('App\UmUser', 'added_by');
    }
}
