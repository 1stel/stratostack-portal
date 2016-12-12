<?php namespace App\DNS;

use Illuminate\Support\Facades\Facade;

class DNS extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'dns';
    }
}
