<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $cm_customers_id
 * @property int $created_by
 * @property int $updated_by
 * @property string $invoice_number
 * @property string $created_at
 * @property float $invoice_price
 * @property float $return_price
 * @property float $net_price
 * @property float $discount
 * @property boolean $invoice_status
 * @property string $invoice_type
 * @property string $updated_at
 * @property float $total_amout_paid
 * @property int $given_rack_count
 * @property int $taken_rack_count
 * @property float $display_discount
 * @property UmUser $umUser
 * @property UmUser $umUser
 * @property CmCustomer $cmCustomer
 * @property DmCustomerInvoiceHasStockBatch[] $dmCustomerInvoiceHasStockBatches
 * @property DmPayment[] $dmPayments
 * @property DmReturn[] $dmReturns
 * @property DmReturn[] $dmReturns
 */
class customerInvoices extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dm_customer_invoice';

    /**
     * @var array
     */
    protected $fillable = ['cm_customers_id', 'created_by', 'updated_by', 'invoice_number', 'created_at', 'invoice_price', 'return_price', 'net_price', 'discount', 'invoice_status', 'invoice_type', 'updated_at', 'total_amout_paid', 'given_rack_count', 'taken_rack_count', 'display_discount'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function umUser()
    {
        return $this->belongsTo('App\UmUser', 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function umUser1()
    {
        return $this->belongsTo('App\UmUser', 'updated_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cmCustomer()
    {
        return $this->belongsTo(Customer::class, 'cm_customers_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dmCustomerInvoiceHasStockBatches()
    {
        return $this->hasMany('App\DmCustomerInvoiceHasStockBatch');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dmPayments()
    {
        return $this->hasMany('App\DmPayment');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dmReturns()
    {
        return $this->hasMany('App\DmReturn');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dmReturns1()
    {
        return $this->hasMany('App\DmReturn', 'returned_from');
    }
}
