<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\MainMenu;
use Session;
use DB;
use Hash;
use Redirect;
use Input;

class RolesController extends Controller
{


    public function create(){

        $menu = \App\Models\MainMenu::with('submenu')->get();
        return view('roles.create',compact('menu'));
    }

    public function store(Request $request){

        $data['role_name'] = $request->role_name;
        $data['main_menu_ids'] = implode(",",$request->main_modules);
        $data['sub_menu_ids'] = implode(",",$request->submenu_id);
        $data['crud_rights'] = implode(",",$request->crud);
        $data['created_by'] = \Auth::user()->id;
        Role::create($data);
        Session::flash('flash_success',__('msg.update_ok'));
        return Redirect::to('roles/viewRoles#lms');

    }

    public function viewRoles(){
        $roles = Role::with('user')->get();
        return view('roles.index',compact('roles'));
    }

    public function viewRolesDetail(Request $request){
        $menu = \App\Models\MainMenu::with('submenu')->get();
        $role = Role::where('id',$request->id)->first();
        $main_modules = explode(",",$role->main_menu_ids);
        $submenu_id = explode(",",$role->sub_menu_ids);
        $crud = explode(",",$role->crud_rights);
        return view("roles.ajax.viewRolesDetail",compact('role','menu','main_modules','submenu_id','crud'));

    }




    public function delete(Request $request)
    {
        Role::where('id',Input::get('id'))->delete();

    }
}
