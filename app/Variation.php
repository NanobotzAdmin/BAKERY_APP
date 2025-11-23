<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Variation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pm_variation';

    /**
     * @var array
     */
    protected $fillable = ['variation_name', 'is_active', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function variationValues()
    {
        return $this->hasMany(VariationValue::class, 'pm_variation_id');
    }
}

