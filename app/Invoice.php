<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{

    //
    protected $fillable = ['invoice_data'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
