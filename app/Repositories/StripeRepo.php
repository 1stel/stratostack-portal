<?php

namespace App\Repositories;


use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Eloquent\CreditCard;
use App\User;
use Cartalyst\Stripe\Stripe;

class StripeRepo implements PaymentRepositoryInterface
{
    private $gw;

    public function __construct(Stripe $stripe)
    {
        $this->gw = $stripe;
    }

    public function newCard(array $cardInfo, $userId) {
        // Does user have a customer profile?  We steal authnet_cid for now...
        $user = User::find($userId);

        if (!$user->authnet_cid) {
            // Create customer profile.
            $customer = $this->createCustomerToken($user->name, $user->email);

            $user->authnet_cid = $customer['id'];
            $user->save();
        }

        // Create the card with Stripe
        $address = [
            'street' => $cardInfo['billTo']['address'],
            'city' => $cardInfo['billTo']['city'],
            'state' => $cardInfo['billTo']['state'],
            'zip' => $cardInfo['billTo']['zipcode'],
        ];

        list($expYear, $expMonth) = explode('-', $cardInfo['cardExp']);
        $cardData = [
            'number' => $cardInfo['cardNumber'],
            'expiration' => "{$expMonth}/{$expYear}",
            'cvv' => $cardInfo['CVV']
        ];

        $card = $this->createPaymentToken($user->authnet_cid, $user->name, $address, $cardData);

        // Add an entry to the local DB
        $cardType = $this->determineCardType(substr($cardInfo['cardNumber'], 0, 1));

        CreditCard::create(['user_id'            => $user->id,
            'number'             => substr($cardInfo['cardNumber'], - 4),
            'exp'                => $cardInfo['cardExp'],
            'type'               => $cardType,
            'payment_profile_id' => $card['id']
        ]);
    }

    public function updateCard($id, array $cardInfo) {
        // Currently unused
    }

    public function deleteCard($id, $userId) {
        $user = User::find($userId);
        $card = CreditCard::where('user_id', '=', $user->id)->where('id', '=', $id)->firstOrFail();

        return $this->gw->cards()->delete($user->authnet_cid, $card->payment_profile_id);
    }

    public function get($id, $userId) {
        $user = User::find($userId);
        $card = CreditCard::where('user_id', '=', $user->id)->where('id', '=', $id)->firstOrFail();

        $stripeCardInfo = $this->gw->cards()->find($user->authnet_cid, $card->payment_profile_id);

        return [
            'name'     => $stripeCardInfo['name'],
            'number'   => $stripeCardInfo['last4'],
            'expMonth' => $stripeCardInfo['exp_month'],
            'expYear'  => $stripeCardInfo['exp_year'],
            'address'  => $stripeCardInfo['address_line1'],
            'city'     => $stripeCardInfo['address_city'],
            'state'    => $stripeCardInfo['address_state'],
            'zipcode'  => $stripeCardInfo['address_zip']
        ];
    }

    public function all($userId) {
        $user = User::find($userId);
        return CreditCard::where('user_id', '=', $user->id)->get();
    }

    public function charge($id, $amount) {
        // ID is a credit card token
        return $this->gw->charges()->create([
            'source' => $id,
            'currency' => 'USD',
            'amount' => $amount,
            'capture' => true
        ]);
    }

    public function voidTransaction($id) {
        // Currently unused
    }

    public function refund() {
        // Currently unused
    }

    private function createCustomerToken($name, $email)
    {
        return $this->gw->customers()->create([
            'email' => $email,
            'description' => $name
        ]);
    }

    private function createPaymentToken($stripeCustomerId, $name, $address, $ccInfo)
    {
        list($expMonth, $expYear) = explode('/', $ccInfo['expiration']);

        $token = $this->gw->tokens()->create([
            'card' => [
                'number' => $ccInfo['number'],
                'exp_month' => $expMonth,
                'cvc' => $ccInfo['cvv'],
                'exp_year' => $expYear,
                'address_line1' => $address['street'],
                'address_city' => $address['city'],
                'address_state' => $address['state'],
                'address_zip' => $address['zip'],
                'name' => $name,
            ]
        ]);

        $card = $this->gw->cards()->create($stripeCustomerId, $token['id']);
        return $card;
    }

    private function determineCardType($firstNum)
    {
        $cardType = 'Unknown';

        switch ($firstNum) {
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
        return $cardType;
    }
}