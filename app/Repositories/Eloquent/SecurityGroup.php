<?php

namespace App\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Model;

class SecurityGroup extends Model
{
    //
    protected $fillable = ['name', 'description'];

    public function ingressRules()
    {
        return $this->hasMany('App\Repositories\Eloquent\IngressRule');
    }
}
