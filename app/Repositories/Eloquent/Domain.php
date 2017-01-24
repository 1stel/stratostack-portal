<?php namespace App\Repositories\Eloquent;

use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{

    use RecordsActivity;
    //
    protected $fillable = ['name', 'user_id'];

    public function records()
    {
        return $this->hasMany('App\Repositories\Eloquent\DomainRecord');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
