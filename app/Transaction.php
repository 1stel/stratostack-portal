<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

    //
    protected $fillable = ['amount', 'note', 'invoice_number'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
