<?php namespace App\Repositories\Eloquent;

use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Model;

// This model is not designed to be used outside of the Payment Repository system.

class CreditCard extends Model
{

    use RecordsActivity;

    //
    protected $fillable = ['user_id', 'number', 'exp', 'type', 'primary', 'payment_profile_id'];
}
