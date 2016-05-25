<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class TemplateGroup extends Model {

	//
    protected $fillable = ['name', 'type', 'display_img'];

    public function templates()
    {
        return $this->hasMany('App\Template');
    }
}
