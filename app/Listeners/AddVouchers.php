<?php

namespace App\Listeners;

use App\Events\UserWasCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Voucher;
use App\SiteConfig;

class AddVouchers
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserWasCreated  $event
     * @return void
     */
    public function handle(UserWasCreated $event)
    {
        // When we create a user, also create some vouchers for the user if we need to
        // User data is in $event->user

        $numVouchers = SiteConfig::whereParameter('vouchers')->first();

        if ($numVouchers->data > 1) {
            $voucherAmount = SiteConfig::whereParameter('voucherAmount')->first();

            static $randList = 'ABCDEF123456789';
            $randLen = strlen($randList) - 1;

            for ($i = 0; $i < $numVouchers->data; $i++) {
                $voucherNum = '';

                for ($j = 0; $j < 10; $j++) {
                    $num = rand(0, $randLen);
                    $voucherNum .= $randList[$num];
                }

                $vouchers[] = new Voucher(['number' => $voucherNum, 'type' => 'Unknown', 'amount' => $voucherAmount->data]);
            }

            $event->user->vouchers()->saveMany($vouchers);
        }
    }
}
