<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $dm_customer_invoice_id
 * @property int $created_by
 * @property int $updated_by
 * @property string $receipt_no
 * @property string $payment_date
 * @property string $type
 * @property float $amount
 * @property string $cheque_number
 * @property boolean $is_returned
 * @property boolean $is_active
 * @property string $created_at
 * @property string $updated_at
 * @property string $cheque_date
 * @property DmCustomerInvoice $dmCustomerInvoice
 * @property UmUser $umUser
 * @property UmUser $umUser
 */
class invoicePayments extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dm_payments';
    public $timestamps = false;


    /**
     * @var array
     */
    protected $fillable = ['dm_customer_invoice_id', 'created_by', 'updated_by', 'receipt_no', 'payment_date', 'type', 'amount', 'cheque_number', 'is_returned', 'is_active', 'created_at', 'updated_at', 'cheque_date'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dmCustomerInvoice()
    {
        return $this->belongsTo('App\DmCustomerInvoice');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function umUserCreatedBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function umUserUpdatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
