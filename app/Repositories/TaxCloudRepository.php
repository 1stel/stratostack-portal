<?php namespace App\Repositories;

use Config;
use TaxCloud\Address;
use TaxCloud\Client;
use TaxCloud\Request\VerifyAddress;
use TaxCloud\Request\Lookup;
use TaxCloud\CartItem;

class TaxCloudRepository
{

    protected $client;
    protected $uspsUserId;

    protected $originAddress;
    protected $destAddress;

    public function __construct(Client $client, $uspsUserId)
    {
        // Need a valid client and a usps user id.
        $this->client = $client;
        $this->uspsUserId = $uspsUserId;
    }

    public function setOriginAddress($address1, $address2, $city, $state, $zip)
    {
        $this->originAddress = $this->createAddress($address1, $address2, $city, $state, $zip);
    }

    public function setDestAddress($address1, $address2, $city, $state, $zip)
    {
        $this->destAddress = $this->createAddress($address1, $address2, $city, $state, $zip);
    }

    public function verifyAddress(Address $address)
    {
//        $address = new Address($address1, $address2, $city, $state, $zip);

        $verifyAddress = new VerifyAddress($this->uspsUserId, $address);

        try {
            $response = $this->client->VerifyAddress($verifyAddress);
            return $response;
        } catch (Exception $e) {
            return null;
        }
    }

    public function createAddress($address1, $address2, $city, $state, $zip)
    {
        return new Address($address1, $address2, $city, $state, $zip);
    }

    public function calculateSalesTax($userId, $invoiceId, $usage)
    {
        $taxIdCounter = 0;
        $taxItems = [];

        foreach ($usage['instance'] as $instance) {
            foreach ($instance as $so_instance) {
                $taxItemName = "{$so_instance['resources']['cpunumber']}-{$so_instance['resources']['memory']}-{$so_instance['resources']['disk_size']}";
                $taxItems[] = new CartItem($taxIdCounter, $taxItemName, $so_instance['tic'], $so_instance['price'], 1);
                $taxIdCounter++;
            }
        }

        $taxInfo = $this->rateLookup($userId, $invoiceId, $taxItems);

        $salesTax = 0;

        foreach (array_pop($taxInfo) as $taxItem) {
            $salesTax += $taxItem;
        }
        return $salesTax;
    }

    public function rateLookup($custId, $cartId, $taxItems)
    {
        $lookup = new Lookup(
            Config::get('taxcloud.apiLoginID'),
            Config::get('taxcloud.apiKey'),
            $custId,
            $cartId,
            $taxItems,
            $this->originAddress,
            $this->destAddress
        );

        return $this->client->Lookup($lookup);
    }

/*
$verifyAddress = new \TaxCloud\Request\VerifyAddress($uspsUserID, $address);

try {
$address = $client->VerifyAddress($verifyAddress);
}
catch (\TaxCloud\Exceptions\USPSIDException $e) {
    echo 'Caught exception: ', $e->getMessage(), "\n";
    return;
}
catch (Exception $e) {
    echo 'Caught exception: ', $e->getMessage(), "\n";
}
*/
}
