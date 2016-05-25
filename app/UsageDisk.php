<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class UsageDisk extends Model {

	//
    protected $fillable = ['zoneId', 'accountId', 'volumeId', 'size', 'type', 'tags', 'usage', 'vmInstanceId', 'startDate', 'endDate'];
    protected $dates = ['startDate', 'endDate'];
    public $timestamps = false;
}
