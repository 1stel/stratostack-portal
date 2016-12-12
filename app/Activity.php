<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{

    protected $fillable = ['subject_type', 'subject_id', 'event', 'user_id', 'ip'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
