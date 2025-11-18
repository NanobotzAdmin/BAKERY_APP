<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTakingForm extends Model
{
    use HasFactory;

    // Specify the table associated with the model
    protected $table = 'dm_order_taking_form';

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
        'vm_sales_reps_id',
        'vm_vehicles_id',
        'needed_date',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'special_note',
    ];

    // Define the relationship with the vm_sales_reps model
    public function vmSalesRep()
    {
        return $this->belongsTo(SaleRep::class, 'vm_sales_reps_id');
    }

    // Define the relationship with the vm_vehicles model
    public function vmVehicle()
    {
        return $this->belongsTo(Vehicles::class, 'vm_vehicles_id');
    }

    // Define the relationship with OrderTakingFormHasProduct
    public function products()
    {
        return $this->hasMany(OrderTakingFormHasProduct::class, 'dm_order_taking_form_id');
    }
}
