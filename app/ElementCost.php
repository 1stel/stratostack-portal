<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class ElementCost extends Model {

	//
    protected $fillable = ['element', 'quantity', 'quantity_type', 'price', 'active'];
}
