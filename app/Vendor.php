<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $table = "vm_vendor";

    protected $fillable = [
        'vendor_name',
        'vendor_email',
        'vendor_phone',
        'vendor_address_line_1',
        'vendor_address_line_2',
        'vendor_city',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

}
