<?php namespace App\Repositories;

use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Eloquent\CreditCard;
use AuthorizeNet\Common\Type\Customer;
use AuthorizeNet\Common\Type\PaymentProfile;
use AuthorizeNet\Common\Type\Transaction;
use App\User;

class AuthorizeNetRepository implements PaymentRepositoryInterface
{

    // !!REVISE!! This class needs input checking and Exception handling.
    // We also lack validation of credit cards at the moment.  Not sure how that is supposed to be handled.

    protected $request;

    public function __construct()
    {
        $this->request = app('AuthorizeNetRequest');
    }

    public function newCard(array $cardInfo, $userId)
    {
        // We need some error checking on $cardInfo here.

        $user = User::find($userId);

        if (!$user->authnet_cid) {
        // We don't have a customer profile created.

            $customer = new Customer;
            $customer->description = $user->name;
            $customer->merchantCustomerId = ''; // !!REVISE!! What is merchantCustomerId??

            $custResponse = $this->request->createCustomerProfile($customer);

            // Get the Authorize.Net customer profile ID and save it in our DB.
            $user->authnet_cid = $custResponse->getCustomerProfileId();
            $user->save();
        }

        $paymentProfile = new PaymentProfile;

        // Card Information
        $paymentProfile->customerType = "individual"; // !!REVISE!!  Is this proper?
        $paymentProfile->payment->creditCard->cardNumber = $cardInfo['cardNumber'];
        $paymentProfile->payment->creditCard->expirationDate = $cardInfo['cardExp'];
        $paymentProfile->payment->creditCard->cardCode = $cardInfo['CVV'];

        // Billing Address Information
        $paymentProfile->billTo->firstName = $cardInfo['billTo']['firstName'];
        $paymentProfile->billTo->lastName = $cardInfo['billTo']['lastName'];
        $paymentProfile->billTo->address = $cardInfo['billTo']['address'];
        $paymentProfile->billTo->city = $cardInfo['billTo']['city'];
        $paymentProfile->billTo->state = $cardInfo['billTo']['state'];
        $paymentProfile->billTo->zip = $cardInfo['billTo']['zipcode'];
        $paymentProfile->billTo->country = $cardInfo['billTo']['country'];

        // Save the Card information with Authorize.Net and record the last 4 digits, expiration date
        // and paymentProfileID in our local database for easier display.
        $response = $this->request->createCustomerPaymentProfile($user->authnet_cid, $paymentProfile);
        $paymentProfileId = $response->xml->customerPaymentProfileId->__toString();

        switch (substr($cardInfo['cardNumber'], 0, 1)) {
            case 3:
                $cardType = 'American Express';
                break;

            case 4:
                $cardType = 'VISA';
                break;

            case 5:
                $cardType = 'Mastercard';
                break;

            case 6:
                $cardType = 'Discover';
                break;
        }

        CreditCard::create(['user_id'            => $user->id,
                            'number'             => substr($cardInfo['cardNumber'], - 4),
                            'exp'                => $cardInfo['cardExp'],
                            'type'               => $cardType,
                            'payment_profile_id' => $paymentProfileId]);

        return $response;
    }

    public function updateCard($id, array $cardInfo)
    {
    }

    public function deleteCard($id, $userId)
    {
        $user = User::find($userId);

        $card = CreditCard::where('user_id', '=', $user->id)->where('id', '=', $id)->firstOrFail();

        $response = $this->request->deleteCustomerPaymentProfile($user->authnet_cid, $card->payment_profile_id);

        $card->delete();

        return $response;
    }

    public function get($id, $userId)
    {
        $user = User::find($userId);

        $card = CreditCard::where('user_id', '=', $user->id)->where('id', '=', $id)->first();

        $custProfile = $this->request->getCustomerPaymentProfile($user->authnet_cid, $card->payment_profile_id);

        list ($expYear, $expMonth) = explode('-', $card->exp);
        $name = $custProfile->xpath('paymentProfile/billTo/firstName')[0] . ' ' . $custProfile->xpath('paymentProfile/billTo/lastName')[0];

        $cardInfo = ['name'     => $name,
                     'number'   => $card->number,
                     'expMonth' => $expMonth,
                     'expYear'  => $expYear,
                     'address'  => (string)$custProfile->xpath('paymentProfile/billTo/address')[0],
                     'city'     => (string)$custProfile->xpath('paymentProfile/billTo/city')[0],
                     'state'    => (string)$custProfile->xpath('paymentProfile/billTo/state')[0],
                     'zipcode'  => (string)$custProfile->xpath('paymentProfile/billTo/zip')[0]];

        return $cardInfo;
    }

    public function all($userId)
    {
        $user = User::find($userId);

        return CreditCard::where('user_id', '=', $user->id)->get();
    }

    public function charge($id, $amount)
    {
        $transaction = new Transaction;
        $transaction->amount = $amount;
        $transaction->customerPaymentProfileId = $id;

        $response = $this->request->createCustomerProfileTransaction('AuthCapture', $transaction);

        return $response->getTransactionResponse();
    }

    public function voidTransaction($id)
    {
        $transaction = new Transaction;
        $transaction->transId = $id;

        return $this->request->createCustomerProfileTransaction('Void', $transaction);
    }

    public function refund()
    {
    }
}
