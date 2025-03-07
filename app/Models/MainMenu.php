<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainMenu extends Model
{
    protected $fillable = ['main_menu_id', 'title', 'title_id', 'menu_type', 'status','date'];

    public function submenu()
    {
        return $this->HasMany(SubMenu::class, 'm_main_title','title_id');

    }
}
