<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    public function reminders(){
        return $this->hasMany('App\Reminders','event_id','id');
    }
    public function user(){
        return $this->belongsTo('App\User','user_id','id');
    }
}
