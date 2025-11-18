<?php

namespace App\Http\Controllers;

use App\Customer;
use Carbon\Carbon;
use App\STATIC_DATA_MODEL;
use App\invoicePayments;
use App\customerInvoices;
use Illuminate\Http\Request;
use App\customerInvoiceHasStock;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    // Index
    public function adminPaymentIndex(Request $request)
    {
        $customerList = Customer::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        return view('distribution.payments.adminPayment', compact('customerList'));
    }


    // Create Payment
    public function saveInvoicePayment(Request $request)
    {
        $recNo = invoicePayments::get();
        $nextRecNo = "REC000" . $recNo->count();
        $lastRecept = DB::table('dm_payments')->latest('id')->first();

        $last = substr($lastRecept->receipt_no, 6);
        $invo = intval($last) + 1;
        $receptNumber = "REC000" . $invo;

        $customerInvoice = customerInvoices::find($request->invoice);
        $customerHasStock = customerInvoiceHasStock::where('dm_customer_invoice_id', $request->invoice)->first();

        $updateNetTotal = $customerInvoice->total_amout_paid + $request->amount;
        $net = floatval($customerInvoice->net_price) - (floatval($customerInvoice->discount) + floatval($customerInvoice->display_discount));

        if (invoicePayments::where('receipt_no', $receptNumber)->exists()) {
            $msg = 'already';
            return compact('msg');
        } elseif (floatval($updateNetTotal) > floatval($net)) {
            $msg = 'totalExceed';
            return compact('msg');
        } else {
            $logged_user = session('logged_user_id');
            $chequeDate = '';
            $chequeNo = "";

            if ($request->paymentType == "cash") {
                $chequeNo = null;
                $chequeDate = null;
            } else {
                $chequeNo = $request->chequeNo;
                $chequeDate = $request->paymentDate;
            }

            $payment = new invoicePayments();
            $payment->receipt_no = $receptNumber;
            $payment->payment_date = Carbon::now();
            $payment->type = $request->paymentType;
            $payment->amount = $request->amount;
            $payment->dm_delivery_vehicle_id = $customerHasStock->dm_delivery_vehicle_has_stock_batch_dm_delivery_vehicle_id;
            $payment->cheque_number = $chequeNo;
            $payment->cheque_date = $chequeDate;
            $payment->is_returned = 0;
            $payment->dm_customer_invoice_id = $request->invoice;
            $payment->is_active = STATIC_DATA_MODEL::$Active;
            $payment->created_at = Carbon::now();
            $payment->updated_at = Carbon::now();
            $payment->created_by = $logged_user;
            $payment->updated_by = $logged_user;
            $paymentsaved = $payment->save();

            //Get last record user login
            $laspaymentId = DB::table('dm_payments')->latest()->first();
            // update invoice's "Total Amout Paid"
            $customerInvoice->update(['total_amout_paid' => $updateNetTotal], ['updated_at', Carbon::now()]);

            $cusInvo = customerInvoices::find($request->invoice);
            $paidAmount = $cusInvo->total_amout_paid;
            if ((float)$paidAmount >= (float)$net) {
                $cusInvo->invoice_status = STATIC_DATA_MODEL::$invoiceCompleted;
                $cusInvo->save();
            }

            if (!$paymentsaved) {
                $msg = 'error';
                return compact('msg');
            } else {

                //Save user activity
                $userActivity = new UserActivityManagementController();
                $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "Invoice payment saved, invoice No: " . $request->invoice . "PaymentId-" . $laspaymentId->id);

                $msg = 'success';
                return compact('msg');
            }
        }
    }


    public function removeInvoicePayment(Request $request)
    {
        $paymentUpdate = invoicePayments::find($request->PaymentId);
        $paymentUpdate->is_active = STATIC_DATA_MODEL::$Inactive;
        $updatePayemnt = $paymentUpdate->save();
        $updateCustomerInvoiceTotal = customerInvoices::find($paymentUpdate->dm_customer_invoice_id);
        $updateNetTotal = $updateCustomerInvoiceTotal->total_amout_paid - $paymentUpdate->amount;
        $updateInvoice = $updateCustomerInvoiceTotal->update(['total_amout_paid' => $updateNetTotal]);

        if ($updatePayemnt && $updateInvoice) {
            $msg = "sucess";
        } else {
            $msg = "error";
        }
        return compact('msg');
    }


    // CREDIT Payment
    public function addCreditPayment(Request $request)
    {
        $showingBalance = $request->showingBalance;
        $receptNumber = '';
        $receptNumber2 = invoicePayments::get();

        if ($receptNumber2->count() == 0) {
            $receptNumber = "REC0001";
        } else {
            $lastRecept = DB::table('dm_payments')->latest('id')->first();
            $last = substr($lastRecept->receipt_no, 6);
            $invo = intval($last) + 1;
            $receptNumber = "REC000" . $invo;
        }

        $recNo = invoicePayments::get();
        $nextRecNo = "REC000" . $recNo->count();
        // get Invoice Object
        $customerInvoice = customerInvoices::find($request->invoice);
        // calculations
        $updateNetTotal =  round($customerInvoice->total_amout_paid, 2) + round($request->subTotal, 2);
        $net = round($customerInvoice->net_price, 2) - (round($customerInvoice->discount, 2) + round($customerInvoice->display_discount, 2) + round($customerInvoice->special_discount, 2));

        if (invoicePayments::where('receipt_no', $receptNumber)->exists()) {
            $msg = 'already';
            return compact('msg');
        } else if (round($updateNetTotal, 2) > round($net, 2)) {
            $msg = 'totalExceed';
            return compact('msg');
        } else {
            $logged_user = session('logged_user_id');
            $chequeNo = null;
            $chequeDate = null;

            $payment = new invoicePayments();
            $payment->receipt_no = $receptNumber;
            $payment->payment_date = Carbon::now();
            $payment->type = "cash";
            $payment->amount = $request->subTotal;
            $payment->cheque_number = null;
            $payment->cheque_date = null;
            $payment->is_returned = 0;
            $payment->dm_customer_invoice_id = $request->invoice;
            $payment->is_active = STATIC_DATA_MODEL::$Active;
            $payment->created_at = Carbon::now();
            $payment->updated_at = Carbon::now();
            $payment->created_by = $logged_user;
            $payment->updated_by = $logged_user;
            $paymentsaved = $payment->save();

            // get last record user login
            $laspaymentId = DB::table('dm_payments')->latest()->first();
            // update invoice's "Total Amount Pain"
            $customerInvoice->update(['total_amout_paid' => $updateNetTotal], ['updated_at', Carbon::now()]);
            $cusInvo = customerInvoices::find($request->invoice);
            $paidAmount = $cusInvo->total_amout_paid;

            // update Invoice status to "completed" if FULLY paid
            if (round($paidAmount, 2) == round($net, 2)) {
                $cusInvo->invoice_status = STATIC_DATA_MODEL::$invoiceCompleted;
                $cusInvo->save();
            }
        }

        if ($paymentsaved) {
            $msg = 'success';
            return compact('msg');
            //Save user activity
            $userActivity = new UserActivityManagementController();
            $userActivity->saveActivity(STATIC_DATA_MODEL::$insert, "Invoice payment saved, invoice No: " . $request->invoice . "PaymentId-" . $laspaymentId->id);
        } else {
            $msg = 'error';
            return compact('msg');
        }
    }


    public function adminPaymentReverseIndex()
    {
        $customerList = Customer::where('is_active', STATIC_DATA_MODEL::$Active)->get();
        return view('distribution.payments.paymentReverse', compact('customerList'));
    }


    public function searchInvoices(Request $request)
    {
        $dateFrom = $request->dateFrom;
        $dateTo = $request->dateTo;
        $dateFromFormat = date("Y-m-d", strtotime($dateFrom));
        $dateToFormat = date("Y-m-d", strtotime($dateTo));

        $query = DB::table('dm_customer_invoice')
            ->select('dm_customer_invoice.*')
            ->distinct('dm_customer_invoice.id')
            ->join('dm_customer_invoice_has_stock_batch', 'dm_customer_invoice_has_stock_batch.dm_customer_invoice_id', '=', 'dm_customer_invoice.id')
            ->where('dm_customer_invoice.invoice_status', '!=', 3); // drop removed invoices

        if ($request->customer !== '0') {
            $query->where('dm_customer_invoice.cm_customers_id', $request->customer);
        }
        if ($request->dateFrom !== null && $request->dateTo == null) {
            $query->whereDate('dm_customer_invoice.created_at', $dateFromFormat);
        }
        if ($request->dateTo !== null && $request->dateFrom == null) {
            $query->whereDate('dm_customer_invoice.created_at', $dateToFormat);
        }
        if ($request->dateTo !== null && $request->dateFrom != null) {
            $query->whereDate('dm_customer_invoice.created_at', '>=', $dateFromFormat);
            $query->whereDate('dm_customer_invoice.created_at', '<=', $dateToFormat);
        }
        $data = $query->where('dm_customer_invoice.invoice_type', 1)->get();
        return view('distribution.payments.ajaxPayments.LoadpaymentReverseArea', compact('data'));
    }


    public function reversePayment(Request $request)
    {
        $msg = '';
        $date = Carbon::now();
        $logged_user = session('logged_user_id');

        $customerInvoice = customerInvoices::find($request->InvoiceId);
        $payment = invoicePayments::find($request->paymentId);
        $payamount = $payment->amount;
        $payDate = $payment->created_at;

        $customertotamount = floatval($customerInvoice->total_amout_paid) - floatval($payment->amount);
        $invoPaymentsDelete = invoicePayments::where('id', $request->paymentId)->delete();

        if ($invoPaymentsDelete) {
            $customerInvoiceUpdate = customerInvoices::find($request->InvoiceId);
            $customerInvoiceUpdate->total_amout_paid = $customertotamount;
            $customerUpdate = $customerInvoiceUpdate->save();

            $cusInvoTot = customerInvoices::find($request->InvoiceId);

            if (floatval($cusInvoTot->net_price) == floatval($cusInvoTot->total_amout_paid)) {
                $cusInvoTotUpdate = customerInvoices::find($request->InvoiceId);
                $cusInvoTotUpdate->invoice_status = STATIC_DATA_MODEL::$invoiceCompleted;
                $customerSave =  $cusInvoTotUpdate->save();
            } else {
                $cusInvoTotUpdate = customerInvoices::find($request->InvoiceId);
                $cusInvoTotUpdate->invoice_status = STATIC_DATA_MODEL::$invoicePending;
                $customerSave =  $cusInvoTotUpdate->save();
            }

            if ($customerUpdate && $customerSave) {
                $userActivity = new UserActivityManagementController();
                $userActivity->saveActivity(STATIC_DATA_MODEL::$delete, "The payment of " . $payamount . "made on-" . $payDate . "for Invoice " . $request->InvoiceId . "has been reversed by " . $logged_user . " on " .  $date);

                $msg = 'success';
            } else {
                $msg = 'error';
            }
        } else {
            $msg = 'error';
        }

        return compact('msg');
    }

}
