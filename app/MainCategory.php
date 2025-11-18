<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $created_by
 * @property string $main_category_name
 * @property boolean $is_active
 * @property string $created_at
 * @property string $updated_at
 * @property UmUser $umUser
 * @property PmProductSubCategory[] $pmProductSubCategories
 */
class MainCategory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pm_product_main_category';
    public $timestamps = false;


    /**
     * @var array
     */
    protected $fillable = ['created_by', 'main_category_name', 'is_active', 'created_at', 'updated_at'];

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
    public function pmProductSubCategories()
    {
        return $this->hasMany('App\PmProductSubCategory');
    }

    public function subCategories()
{
    return $this->hasMany(SubCategory::class, 'pm_product_main_category_id');
}
}
