<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $table = 'pm_brands';

    protected $fillable = [
        'brand_name',
        'is_active',
        'created_by',
        'updated_by',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'pm_brands_id');
    }
}


