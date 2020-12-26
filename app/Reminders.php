<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
class Reminders extends Model
{
    public function event(){
        return $this->belongsTo('App\Events','id','event_id');
    }
    public function user(){
        $event=$this->event();
        $user_id=$event->user_id;
        return User::find($user_id);
    }
}
