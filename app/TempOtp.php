<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TempOtp extends Model
{
    protected $table="temp_email_otp";
    protected $primaryKey='id';
}
