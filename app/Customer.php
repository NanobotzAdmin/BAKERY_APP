<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $um_user_id
 * @property string $customer_name
 * @property string $address
 * @property string $contact_person
 * @property string $contact_number
 * @property string $email_address
 * @property boolean $is_active
 * @property string $created_at
 * @property string $updated_at
 * @property UmUser $umUser
 * @property DmCustomerInvoice[] $dmCustomerInvoices
 */
class Customer extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cm_customers';
    public $timestamps = false;


    /**
     * @var array
     */
    protected $fillable = [
        'um_user_id',
        'customer_name',
        'address',
        'contact_person',
        'contact_number',
        'email_address',
        'is_active',
        'created_at',
        'updated_at',
        'max_credit_bills',
        'max_credit_bill_availability',
        'max_credit_amount',
        'max_discount',
        'cm_routes_id',
        'route_order',
        'latitude',
        'longitude',
        'location_link'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function umUser()
    {
        return $this->belongsTo('App\UmUser');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dmCustomerInvoices()
    {
        return $this->hasMany('App\DmCustomerInvoice', 'cm_customers_id');
    }
}
