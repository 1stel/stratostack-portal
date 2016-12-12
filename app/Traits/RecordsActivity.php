<?php namespace App\Traits;

//use Str;
use Auth;
use ReflectionClass;
use App\Activity;
use Request;

trait RecordsActivity
{

    protected static function bootRecordsActivity()
    {
        foreach (['created', 'deleted', 'updated'] as $event) {
            static::$event(function ($model) use ($event) {
                $model->recordActivity($event);
            });
        }
    }

    public function recordActivity($event)
    {
        if (!empty($this->name)) {
            $eventText = "$event: $this->name";
        } else if (!empty($this->number)) {
            $eventText = "$event: $this->number";
        } else {
            $eventText = $event;
        }

        Activity::create([
            'subject_type' => $this->getDisplaySingular(),
            'subject_id' => $this->id,
//            'subject_name' => null,
            'event' => $eventText,
            'user_id' => (Auth::user() ? Auth::user()->id : 0),
            'ip' => Request::server('REMOTE_ADDR')
        ]);
    }

    public function getDisplaySingular()
    {
        if (isset($this->displaySingular)) {
            return $this->displaySingular;
        }

        return (new ReflectionClass($this))->getShortName();
    }
}
