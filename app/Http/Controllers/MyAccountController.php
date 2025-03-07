<?php

namespace App\Http\Controllers;

use App\Models\ActivityLogins;
use App\Models\ActivityLogs;
use App\Models\SubMenu;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\Qs;
use App\Http\Requests\UserChangePass;
use App\Http\Requests\UserUpdate;
use App\Repositories\UserRepo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use DB;
use App\Models\MainMenu;
use App\Models\Role;
use Session;


class MyAccountController extends Controller
{
    protected $user;

    public function __construct(UserRepo $user)
    {
        $this->user = $user;
    }

    public function edit_profile()
    {
        $d['my'] = Auth::user();
        return view('pages.support_team.my_account', $d);
    }

    public function update_profile(UserUpdate $req)
    {
        $user = Auth::user();

        $d = $user->username ? $req->only(['email', 'phone', 'address']) : $req->only(['email', 'phone', 'address', 'username']);

        if(!$user->username && !$req->username && !$req->email){
            return back()->with('pop_error', __('msg.user_invalid'));
        }

        $user_type = $user->user_type;
        $code = $user->code;

        if($req->hasFile('photo')) {
            $photo = $req->file('photo');
            $f = Qs::getFileMetaData($photo);
            $f['name'] = 'photo.' . $f['ext'];
            $f['path'] = $photo->storeAs(Qs::getUploadPath($user_type).$code, $f['name']);
            $d['photo'] = asset('storage/' . $f['path']);
        }

        $this->user->update($user->id, $d);
        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function change_pass(UserChangePass $req)
    {
        $user_id = Auth::user()->id;
        $my_pass = Auth::user()->password;
        $old_pass = $req->current_password;
        $new_pass = $req->password;

        if(password_verify($old_pass, $my_pass)){
            $data['password'] = Hash::make($new_pass);
            $this->user->update($user_id, $data);
            return back()->with('flash_success', __('msg.p_reset'));
        }

        return back()->with('flash_danger', __('msg.p_reset_fail'));
    }


    public function addMainMenu(){


        $main_menu = MainMenu::get()->toArray();
        return view('main-menu.create',compact('main_menu'));

    }

    public function storeMainMenu(Request $request){
        $main_menu_id =  $request->main_menu_name;
        $title = $request->title_name;
        $title_id = preg_replace('/\s+/', '', $title);

        $data1['main_menu_id'] =	$main_menu_id;
        $data1['title']     = $title;
        $data1['title_id']  = $title_id;
        $data1['menu_type'] = $request->menu_type;
        $data1['icon']     = $request->icon;
        $data1['date']      = date("Y-m-d");
        MainMenu::insert($data1);
        return back()->with('flash_success', __('msg.update_ok'));
    }



    public function addSubMenu(){

     ;
        $main_menu = MainMenu::where('status', '=', '1')->get();
        $sub_menu = SubMenu::where('status', '=', '1')->orderBy('m_parent_code')->get();
        return view('sub-menu.create',compact('main_menu','sub_menu'));

    }

    public function storeSubMenu(Request $request){

        $main_navigation_name = $request->main_navigation_name;
        $explodeMainNavigation = explode('_',$main_navigation_name);
        $subNavigationTitleName =  $request->sub_navigation_title_name;
        $subNavigationUrl =  $request->sub_navigation_url;
        $js = $request->js;
        $page_type = $request->page_type;
        $mainNavigationName = $explodeMainNavigation[0];
        $mainNavigationTitleId = $explodeMainNavigation[1];

        $max_id = DB::selectOne('SELECT max(`id`) as id  FROM `sub_menus` WHERE `m_parent_code` = '.$mainNavigationName.'')->id;

        if($max_id == ''){
            $code = $mainNavigationName.'-1';
        }else{
            $max_code2 = DB::selectOne('SELECT `m_code` FROM `sub_menus` WHERE `m_parent_code` = '.$explodeMainNavigation[0].'')->m_code;
            $max_code2;
            $max_code2;
            $max = explode('-',$max_code2);
            $code = $mainNavigationName.'-'.(end($max)+1);
        }
        $data1['m_code'] =	$code;
        $data1['m_parent_code'] = $explodeMainNavigation[0];
        $data1['m_type'] = '';
        $data1['m_main_title']= $explodeMainNavigation[1];
        $data1['name'] = $subNavigationTitleName;
        $data1['js'] = $js;
        $data1['m_controller_name'] = $subNavigationUrl;
        $data1['page_type'] = $page_type;
        $data1['date']     		  = date("Y-m-d");
        SubMenu::insert($data1);
        return back()->with('flash_success', __('msg.update_ok'));

    }

    public function addRoles(){
        return view('roles.create');
    }

    public function storeRoles(Request $request){

        Role::where('role_name', $request->role_name)->delete();

        $hide_confidentiality=$request->hide_confidentiality;
        if($hide_confidentiality != ''){
            $hide_confidentiality='yes';
        }
        else{
            $hide_confidentiality='no';
        }

        $Roles = new Role();
        $Roles->role_name           = $request->role_name;
        $Roles->hide_confidentiality= $hide_confidentiality;
        $Roles->main_menu_ids       = implode(',',$request->main_menu);
        $Roles->sub_menu_ids        = implode(',',$request->sub_menu);
        $Roles->crud_rights         = implode(',',$request->crud_rights);
        $Roles->status              = 1;
        $Roles->username            = Auth::user()->name;
        $Roles->save();
        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function viewRoles(){
        $roles = Role::where('status',1)->get();
        return view('roles.index',compact('roles'));
    }

    public function addUsers(){

        $roles = Role::where('status',1)->get();
        return view('users.create',compact('roles'));
    }

    public function storeUsers(Request $request){

        $data['name'] = $request->name;
        $data['username'] = $request->username;
        $data['email'] = $request->email;
        $data['password'] = Hash::make($request->password);
        $data['role_id'] = $request->role_id;
        $data['user_type'] = 'user';

        User::insert($data);
        return back()->with('flash_success', __('msg.update_ok'));


    }

    public function viewUsers(){
        $users =  DB::table('users')
            ->join('roles', 'roles.id', '=', 'users.role_id')
            ->where('users.user_type','!=','super_admin')
            ->select("users.*",'roles.role_name')
            ->get();
        return view('users.index',compact('users'));
    }


    public function editUsers(Request $request){

        $roles = Role::where('status',1)->get();
        $users =  DB::table('users')
            ->join('roles', 'roles.id', '=', 'users.role_id')
            ->where('users.id',$request->segment(2))
            ->select("users.*",'roles.id as roleId')
            ->get();



        return view('users.edit',compact('users','roles'));


    }
    public function updateUsers(Request $request){

        $data['name'] = $request->name;
        $data['username'] = $request->username;
        $data['email'] = $request->email;
        $data['role_id'] = $request->role_id;


        User::where('id',$request->segment(2))->update($data);
        return back()->with('flash_success', __('msg.update_ok'));

    }

    public function delete(Request $request){
        User::where('id',$request->segment(2))->delete();
        return back()->with('flash_success', __('msg.del_ok'));

    }



    public function storeLogs(Request $request){

        $request->session()->put('latitude',$request->latitude);
        $request->session()->put('longitude', $request->longitude);
        return ActivityLogs::create($request->all());

    }


    public function PageActivity(){

         $users = User::all();
         return view('audit-logs.PageActivity',compact('users'));
    }

    public function userPageActivity(Request $request){



     $activity =  ActivityLogs::with(['user' => function ($query) {
          $query->select('id', 'name');
      }])->where('user_id',$request->user_id)->orderBy('id','desc');
        if($request->show_all == 'yes'):
            $activity_logs = $activity->get();
        else:
            $activity_logs = $activity->whereDate('timestamp', $request->date)->get();
        endif;

       return view('audit-logs.userPageActivity',compact('activity_logs'));

    }

    public function loginActivity(){

        $users = User::all();
        return view('audit-logs.loginActivity',compact('users'));

    }

    public function loginPageActivity(Request $request){


        $activity =  ActivityLogins::with(['user' => function ($query) {
            $query->select('id', 'name');
        }])->where('user_id',$request->user_id)->orderBy('id','desc');
        if($request->show_all == 'yes'):
            $activity_logs = $activity->get();
        else:
            $activity_logs = $activity->whereDate('timestamp', $request->date)->get();
        endif;


        return view('audit-logs.userLoginActivity',compact('activity_logs'));


    }

}
