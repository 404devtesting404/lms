<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
class Role extends Model{
	protected $table = 'roles';
	protected $fillable = ['role_name','main_menu_ids','sub_menu_ids','crud_rights','hide_confidentiality','created_by','status','created_at','updated_at'];
	protected $primaryKey = 'id';
	public $timestamps = true;

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('status', function (Builder $builder) {
            $builder->where('status', 1);
        });
    }

    public function user(){

        return $this->hasOne(User::class, 'id','created_by');

    }
}
