<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{

    //
    use SoftDeletes;

    protected $fillable = ['name', 'template_group_id', 'template_id', 'type', 'size', 'price'];
    protected $dates = ['deleted_at'];

    public function group()
    {
        return $this->belongsTo('App\TemplateGroup');
    }
}
