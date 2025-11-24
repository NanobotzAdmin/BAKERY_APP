<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'pm_product';

    protected $fillable = [
        'pm_brands_id',
        'product_name',
        'product_code',
        'product_description',
        'is_active',
        'created_by',
        'updated_by',
        'pm_product_item_type_id',
        'pm_product_main_category_id',
        'pm_product_sub_category_id',
    ];

    public function items()
    {
        return $this->hasMany(ProductItem::class, 'pm_product_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'pm_brands_id');
    }

    public function mainCategory()
    {
        return $this->belongsTo(MainCategory::class, 'pm_product_main_category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'pm_product_sub_category_id');
    }
}


