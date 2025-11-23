<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VariationValue extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pm_variation_value';

    /**
     * @var array
     */
    protected $fillable = ['pm_variation_id', 'pm_variation_value_type_id', 'variation_value_name', 'variation_value', 'is_active', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function variation()
    {
        return $this->belongsTo(Variation::class, 'pm_variation_id');
    }
}

