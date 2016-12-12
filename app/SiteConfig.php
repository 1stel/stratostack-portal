<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class SiteConfig extends Model
{

    //
    protected $table = 'site_config';
    protected $fillable = ['parameter', 'data'];

    public $timestamps = false;

    public function newCollection(array $models = [])
    {
        return new SiteConfigCollection($models);
    }
}

class SiteConfigCollection extends Collection
{

    /*
    * Make a key-value pair array from the site config table
    * Fetches all of the config parameters and makes an
    * associative array with the column 'parameter'
    * as the key and 'data' as the value.
    *
    * @return Array
    */
    public function makeKVArray()
    {
        $result = array();

        $this->each(function ($config) use (&$result) {
//            echo "Have $config->parameter and $config->data\n";
            $result[$config->parameter] = $config->data;
        });

        return $result;
    }
}
