<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class UsageGeneral extends Model
{

    //
    protected $fillable = ['zoneId', 'accountId', 'type', 'usage', 'vmInstanceId', 'templateId', 'startDate', 'endDate'];
    protected $dates = ['startDate', 'endDate'];
    public $timestamps = false;
}
