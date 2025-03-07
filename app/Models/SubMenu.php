<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubMenu extends Model
{
    protected $fillable = ['m_code', 'm_parent_code', 'm_type', 'm_main_title', 'name','js','m_controller_name','page_type','status','date','time'];
}
