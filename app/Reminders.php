<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
class Reminders extends Model
{
    public function event(){
        return $this->belongsTo('App\Events','event_id','id');
    }
}
