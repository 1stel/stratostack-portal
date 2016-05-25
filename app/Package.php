<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model {

	//
    use SoftDeletes;

    protected $fillable = ['cpu_number', 'ram', 'disk_size', 'disk_type', 'price', 'tic', 'paymentTypeOverride'];
    protected $dates = ['deleted_at'];

    public function diskType()
    {
        return $this->hasOne('App\DiskType', 'id', 'disk_type');
    }
}
