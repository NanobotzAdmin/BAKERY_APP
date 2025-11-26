<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $created_by
 * @property int $pm_product_main_category_id
 * @property string $sub_category_name
 * @property boolean $is_active
 * @property string $created_at
 * @property string $updated_at
 * @property MainCategory $pmProductMainCategory
 * @property UmUser $umUser
 */
class SubCategory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pm_product_sub_category';
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = ['created_by', 'pm_product_main_category_id', 'sub_category_name', 'is_active', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pmProductMainCategory()
    {
        return $this->belongsTo('App\MainCategory', 'pm_product_main_category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function umUser()
    {
        return $this->belongsTo('App\UmUser', 'created_by');
    }

}
