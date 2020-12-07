<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    public function reminders(){
        return $this->hasMany('App\Reminders','event_id','id');
    }
}
