<?php

namespace App\Models;

use Eloquent;

class ActivityLogins extends Eloquent
{
    public $table = 'activity_logins';
    public $timestamps = false;
    protected $fillable = ['user_id', 'ip_address','type','latitude','longitude'];


    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

}
