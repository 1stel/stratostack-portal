<?php

namespace App\Repositories\Eloquent;

use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Model;

class DomainRecord extends Model
{

    use RecordsActivity;
    //
    protected $fillable = ['domain_id', 'name', 'type', 'target', 'weight', 'port', 'priority', 'user_id'];

    public function domain()
    {
        return $this->belongsTo('App\Repositories\Eloquent\Domain');
    }
}
