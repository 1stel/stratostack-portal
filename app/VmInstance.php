<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class VmInstance extends Model
{

    //
    protected $fillable = ['agent_id', 'vm_instance_id', 'cpu_number', 'scpu_speed', 'memory', 'disk_size', 'disk_type', 'rate'];
    public $timestamps = false;

    public function user()
    {
        $this->belongsTo('App/User');
    }
}
