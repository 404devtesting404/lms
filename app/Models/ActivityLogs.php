<?php

namespace App\Models;

use Eloquent;

class ActivityLogs extends Eloquent
{
    public $timestamps = false;
    public $table = 'activity_logs';
    protected $fillable = ['user_id', 'url','ip_address','type','latitude','longitude'];


    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }


}
