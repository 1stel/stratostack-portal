<?php namespace App\Http\Controllers\Settings;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Invoice;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use Auth;
use Illuminate\Http\Request;

class BillingController extends Controller
{

    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function billing(PaymentRepositoryInterface $payments)
    {
        // Show current balance, monthly usage, payment methods, voucher redemption, and transactions
        $creditCards = $payments->all(Auth::User()->id);

        $curYear = date('Y');
        $validYears = range($curYear, $curYear + 10);

        $transactions = Auth::User()->transactions;

        return view('settings.billing')->with(compact('creditCards', 'validYears', 'transactions'));
    }

    public function getInvoice($id)
    {
        // Get invoice if authorized
        $invoice = Invoice::where('invoice_number', '=', $id)->first();

        $invoiceData = unserialize($invoice->invoice_data);

        return view('settings.invoice', ['invoiceData' => $invoiceData, 'date' => $invoice->created_at]);
    }
}
