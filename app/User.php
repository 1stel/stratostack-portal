<?php namespace App;

use App\Traits\RecordsActivity;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{

    use Authenticatable, CanResetPassword, RecordsActivity;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'access', 'paymentTypeOverride', 'agent_id', 'acs_id', 'authnet_cid', 'apiKey', 'verified', 'email_token'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    // Relationships
    public function instances()
    {
        return $this->hasMany('App\VmInstance');
    }

    public function vouchers()
    {
        return $this->hasMany('App\Voucher');
    }

    public function transactions()
    {
        return $this->hasMany('App\Transaction');
    }

    public function invoices()
    {
        return $this->hasMany('App\Invoice');
    }

    public function activity()
    {
        return $this->hasMany('App\Activity');
    }

    // Scopes
    public function scopeBillableToday($query)
    {
        $today = date('Y-m-d');
        return $query->where('bill_date', '=', $today);
    }
}
