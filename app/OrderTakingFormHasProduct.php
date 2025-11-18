<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTakingFormHasProduct extends Model
{
    use HasFactory;

    // Specify the table associated with the model
    protected $table = 'dm_order_taking_form_has_products';

    // Specify the primary key
    protected $primaryKey = 'id';

    // Specify if the primary key is auto-incrementing
    public $incrementing = true;

    // Specify the data type of the primary key
    protected $keyType = 'int';

    // Set the timestamp fields for created_at and updated_at
    public $timestamps = true;

    // Define the fillable fields (mass assignable)
    protected $fillable = [
        'dm_order_taking_form_id',
        'pm_product_sub_category_id',
        'order_qty',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    // Define the relationship with the dm_order_taking_form model
    public function dmOrderTakingForm()
    {
        return $this->belongsTo(OrderTakingForm::class, 'dm_order_taking_form_id');
    }

    // Define the relationship with the pm_product_sub_category model
    public function pmProductSubCategory()
    {
        return $this->belongsTo(SubCategory::class, 'pm_product_sub_category_id');
    }
}
