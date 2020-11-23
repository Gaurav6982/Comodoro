<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    protected $table='user_activity';
    protected $primaryKey='id';
    protected $fillable=['user_id','device_token'];
}
