<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Session;
use DB;
use Hash;
use Redirect;

class UserController extends Controller
{


    public function create(){

        $roles = Role::all();
        return view('users.create',compact('roles'));
    }

    public function store(Request $request){

        $data['name'] = $request->name;
        $data['username'] = $request->username;
        $data['email'] = $request->email;
        $data['password'] = Hash::make($request->password);
        $data['role_id'] = $request->role_id;
        $data['user_type'] = 'user';

        User::create($data);
        Session::flash('flash_success',__('msg.update_ok'));
        return Redirect::to('users/viewUsers#lms');



    }


    public function viewUsers(){
        $users = User::with('role')->where('user_type','user')->get();
        return view('users.index',compact('users'));
    }

    public function edit(Request $request){

        $roles = Role::all();
        $users =  User::with('role')->where('id',$request->id)->first();

        return view('users.edit',compact('users','roles'));


    }

    public function delete(Request $request){

        User::where('id',$request->id)->delete();
        Session::flash('flash_success',__('msg.del_ok'));
        return Redirect::to('users/viewUsers#lms');


    }

    public function update(Request $request){

        if($request->password != ''){
            $data['password'] = Hash::make($request->password);
        }
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['role_id'] = $request->role_id;

        User::where('id',$request->id)->update($data);
        Session::flash('flash_success',__('msg.update_ok'));
        return Redirect::to('users/viewUsers#lms');

    }


}
