<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\SiteConfig;

class SiteConfigSeeder extends Seeder
{

    public function run()
    {
        DB::table('site_config')->delete();

        SiteConfig::create(['parameter' => 'setupComplete', 'data' => 'false']);
        SiteConfig::create(['parameter' => 'defaultPaymentType', 'data' => 'PostPay']);
        SiteConfig::create(['parameter' => 'hoursInMonth', 'data' => '672']);
        SiteConfig::create(['parameter' => 'domainId', 'data' => '']);
        SiteConfig::create(['parameter' => 'creditLimit', 'data' => '100']);
        SiteConfig::create(['parameter' => 'rootdiskresize', 'data' => 'FALSE']);
        SiteConfig::create(['parameter' => 'recordsUrl', 'data' => '']);
        SiteConfig::create(['parameter' => 'hypervisor', 'data' => '']);
        SiteConfig::create(['parameter' => 'vouchers', 'data' => '10']);
        SiteConfig::create(['parameter' => 'voucherAmount', 'data' => '25']);
        SiteConfig::create(['parameter' => 'lastBillRecordDate', 'data' => '2015-08-01']);
        SiteConfig::create(['parameter' => 'dnsServer', 'data' => '']);
        SiteConfig::create(['parameter' => 'dnsApiKey', 'data' => '']);
        SiteConfig::create(['parameter' => 'grandfatherPricing', 'data' => 'NO']);

//        SiteConfig::create(['parameter' => '', 'data' => '']);
    }
}
