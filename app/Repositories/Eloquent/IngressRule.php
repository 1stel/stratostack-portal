<?php

namespace App\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Model;

class IngressRule extends Model
{
    //
    protected $fillable = ['cidr', 'protocol', 'icmp_type', 'icmp_code', 'start_port', 'end_port'];

    public function securitygroup()
    {
        return $this->belongsTo('App\Repositories\Eloquent\SecurityGroup');
    }
}
