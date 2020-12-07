<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reminders extends Model
{
    public function event(){
        return $this->belongsTo('App\Events','id','event_id');
    }
}
