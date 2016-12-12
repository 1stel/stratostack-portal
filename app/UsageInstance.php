<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class UsageInstance extends Model
{

    //
    protected $fillable = ['zoneId', 'accountId', 'vm_name', 'usage', 'vmInstanceId', 'serviceOfferingId', 'templateId', 'cpuNumber', 'cpuSpeed', 'memory', 'startDate', 'endDate'];
    protected $dates = ['startDate', 'endDate'];
    public $timestamps = false;
}
