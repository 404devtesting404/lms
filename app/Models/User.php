<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class User extends Model
{

    protected $table = 'users';
    protected $fillable = ['name','username','email','password','role_id','user_type'];
    protected $primaryKey = 'id';
    public $timestamps = true;


        public function role(){

        return $this->hasOne(Role::class, 'id','role_id');

    }

}
