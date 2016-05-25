<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model {

    use SoftDeletes;

	//
    protected $fillable = ['number', 'type', 'amount'];
    protected $dates = ['deleted_at', 'redeemed_at'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
