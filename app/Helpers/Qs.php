<?php

namespace App\Helpers;
use App\Models\ActivityLogs;
use Illuminate\Http\Request;
use App\Models\MainMenu;
use App\Models\Setting;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Models\SubMenu;
use Hashids\Hashids;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Models\LoanHistory;

class Qs
{
    public static function displayError($errors)
    {
        foreach ($errors as $err) {
            $data[] = $err;
        }
        return '
                <div class="alert alert-danger alert-styled-left alert-dismissible">
									<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
									<span class="font-weight-semibold">Oops!</span> '.
        implode(' ', $data).'
							    </div>
                ';
    }

    public static function getAppCode()
    {
        return self::getSetting('system_title') ?: 'CJ';
    }

    public static function getDefaultUserImage()
    {
        return asset('public/global_assets/images/user.png');
    }

    public static function getPanelOptions()
    {
        return '    <div class="header-elements">
                    <div class="list-icons">
                        <a class="list-icons-item" data-action="collapse"></a>
                        <a class="list-icons-item" data-action="remove"></a>
                    </div>
                </div>';
    }

    public static function displaySuccess($msg)
    {
        return '
 <div class="alert alert-success alert-bordered">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button> '.
        $msg.'  </div>
                ';
    }

    public static function getTeamSA()
    {
        return ['admin', 'super_admin'];
    }

    public static function getTeamAccount()
    {
        return ['admin', 'super_admin', 'accountant'];
    }

    public static function getTeamSAT()
    {
        return ['admin', 'super_admin', 'teacher'];
    }

    public static function getTeamAcademic()
    {
        return ['admin', 'super_admin', 'teacher', 'student'];
    }

    public static function getTeamAdministrative()
    {
        return ['admin', 'super_admin', 'accountant'];
    }

    public static function hash($id)
    {
        $date = date('dMY').'CJ';
        $hash = new Hashids($date, 14);
        return $hash->encode($id);
    }

    public static function getUserRecord($remove = [])
    {
        $data = ['name', 'email', 'phone', 'phone2', 'dob', 'gender', 'address', 'bg_id', 'nal_id', 'state_id', 'lga_id'];

        return $remove ? array_values(array_diff($data, $remove)) : $data;
    }

    public static function getStaffRecord($remove = [])
    {
        $data = ['emp_date',];

        return $remove ? array_values(array_diff($data, $remove)) : $data;
    }

    public static function getStudentData($remove = [])
    {
        $data = ['my_class_id', 'section_id', 'my_parent_id', 'dorm_id', 'dorm_room_no', 'year_admitted', 'house', 'age'];

        return $remove ? array_values(array_diff($data, $remove)) : $data;

    }

    public static function decodeHash($str, $toString = true)
    {
        $date = date('dMY').'CJ';
        $hash = new Hashids($date, 14);
        $decoded = $hash->decode($str);
        return $toString ? implode(',', $decoded) : $decoded;
    }

    public static function userIsTeamAccount()
    {
        return in_array(Auth::user()->user_type, self::getTeamAccount());
    }

    public static function userIsTeamSA()
    {
        return in_array(Auth::user()->user_type, self::getTeamSA());
    }

    public static function userIsTeamSAT()
    {
        return in_array(Auth::user()->user_type, self::getTeamSAT());
    }

    public static function userIsAcademic()
    {
        return in_array(Auth::user()->user_type, self::getTeamAcademic());
    }

    public static function userIsAdministrative()
    {
        return in_array(Auth::user()->user_type, self::getTeamAdministrative());
    }

    public static function userIsAdmin()
    {
        return Auth::user()->user_type == 'admin';
    }

    public static function getUserType()
    {
        return Auth::user()->user_type;
    }

    public static function userIsSuperAdmin()
    {
        return Auth::user()->user_type == 'super_admin';
    }

    public static function userIsStudent()
    {
        return Auth::user()->user_type == 'student';
    }

    public static function userIsTeacher()
    {
        return Auth::user()->user_type == 'teacher';
    }

    public static function userIsParent()
    {
        return Auth::user()->user_type == 'parent';
    }

    public static function userIsStaff()
    {
        return in_array(Auth::user()->user_type, self::getStaff());
    }

    public static function getStaff($remove=[])
    {
        $data =  ['super_admin', 'admin', 'teacher', 'accountant', 'librarian'];
        return $remove ? array_values(array_diff($data, $remove)) : $data;
    }

    public static function getAllUserTypes($remove=[])
    {
        $data =  ['super_admin', 'admin', 'teacher', 'accountant', 'librarian', 'student', 'parent'];
        return $remove ? array_values(array_diff($data, $remove)) : $data;
    }

    // Check if User is Head of Super Admins (Untouchable)
    public static function headSA(int $user_id)
    {
        return $user_id === 1;
    }

    public static function userIsPTA()
    {
        return in_array(Auth::user()->user_type, self::getPTA());
    }

    public static function userIsMyChild($student_id, $parent_id)
    {
        $data = ['user_id' => $student_id, 'my_parent_id' =>$parent_id];
        return StudentRecord::where($data)->exists();
    }

    public static function getSRByUserID($user_id)
    {
        return StudentRecord::where('user_id', $user_id)->first();
    }

    public static function getPTA()
    {
        return ['super_admin', 'admin', 'teacher', 'parent'];
    }

    /*public static function filesToUpload($programme)
    {
        return ['birth_cert', 'passport',  'neco_cert', 'waec_cert', 'ref1', 'ref2'];
    }*/

    public static function getPublicUploadPath()
    {
        return 'uploads/';
    }

    public static function getUserUploadPath()
    {
        return 'uploads/'.date('Y').'/'.date('m').'/'.date('d').'/';
    }

    public static function getUploadPath($user_type)
    {
        return 'uploads/'.$user_type.'/';
    }

    public static function getFileMetaData($file)
    {
        //$dataFile['name'] = $file->getClientOriginalName();
        $dataFile['ext'] = $file->getClientOriginalExtension();
        $dataFile['type'] = $file->getClientMimeType();
        $dataFile['size'] = self::formatBytes($file->getSize());
        return $dataFile;
    }

    public static function generateUserCode()
    {
        return substr(uniqid(mt_rand()), -7, 7);
    }

    public static function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');

        return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
    }

    public static function getSetting($type)
    {
        return Setting::where('type', $type)->first()->description;
    }

    public static function getCurrentSession()
    {
        return self::getSetting('current_session');
    }

    public static function getNextSession()
    {
        $oy = self::getCurrentSession();
        $old_yr = explode('-', $oy);
        return ++$old_yr[0].'-'.++$old_yr[1];
    }

    public static function getSystemName()
    {
        return self::getSetting('system_name');
    }

    public static function findMyChildren($parent_id)
    {
        return StudentRecord::where('my_parent_id', $parent_id)->with(['user', 'my_class'])->get();
    }

    public static function findTeacherSubjects($teacher_id)
    {
        return Subject::where('teacher_id', $teacher_id)->with('my_class')->get();
    }

    public static function findStudentRecord($user_id)
    {
        return StudentRecord::where('user_id', $user_id)->first();
    }

    public static function getMarkType($class_type)
    {
       switch($class_type){
           case 'J' : return 'junior';
           case 'S' : return 'senior';
           case 'N' : return 'nursery';
           case 'P' : return 'primary';
           case 'PN' : return 'pre_nursery';
           case 'C' : return 'creche';
       }
        return $class_type;
    }

    public static function json($msg, $ok = TRUE, $arr = [])
    {
        return $arr ? response()->json($arr) : response()->json(['ok' => $ok, 'msg' => $msg]);
    }

    public static function jsonStoreOk()
    {
        return self::json(__('msg.store_ok'));
    }

    public static function jsonUpdateOk()
    {
        return self::json(__('msg.update_ok'));
    }

    public static function storeOk($routeName)
    {
        return self::goWithSuccess($routeName, __('msg.store_ok'));
    }

    public static function deleteOk($routeName)
    {
        return self::goWithSuccess($routeName, __('msg.del_ok'));
    }

    public static function updateOk($routeName)
    {
        return self::goWithSuccess($routeName, __('msg.update_ok'));
    }

    public static function goToRoute($goto, $status = 302, $headers = [], $secure = null)
    {
        $data = [];
        $to = (is_array($goto) ? $goto[0] : $goto) ?: 'dashboard';
        if(is_array($goto)){
            array_shift($goto);
            $data = $goto;
        }
        return app('redirect')->to(route($to, $data), $status, $headers, $secure);
    }

    public static function goWithDanger($to = 'dashboard', $msg = NULL)
    {
        $msg = $msg ? $msg : __('msg.rnf');
        return self::goToRoute($to)->with('flash_danger', $msg);
    }

    public static function goWithSuccess($to, $msg)
    {
        return self::goToRoute($to)->with('flash_success', $msg);
    }

    public static function getDaysOfTheWeek()
    {
        return ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    }


    public static function getUserRole(){
        
         $roles = \App\Models\Role::where('id', Auth::user()->role_id)->get();
         return $roles[0];
    }

    public static function menuAccess(){
        if(Auth::user()->user_type != 'super_admin'){
            $menu_ids = explode(",",Qs::getUserRole()->main_menu_ids);
            return $main_menu_names = MainMenu::select('id','title','icon')->where('status',1)->whereIn('id',$menu_ids)->get();
        }
        else{
            return $main_menu_names = MainMenu::select('id','title','icon')->where('status',1)->get();
        }

    }

    public static function subMenuAccess(){

        if(Auth::user()->user_type != 'super_admin'){

             $menu_ids = explode(",",Qs::getUserRole()->sub_menu_ids);
             return $sub_menu_names = SubMenu::select('id','name','m_controller_name','m_parent_code')->where('status',1)->where('page_type',1)->whereIn('id',$menu_ids)->get();
        }
        else{
            return $sub_menu_names = SubMenu::select('id','name','m_controller_name','m_parent_code')->where('status',1)->where('page_type',1)->get();
        }

    }
    public static function crudRights(){
        $req_uri = substr($_SERVER['REQUEST_URI'],1);
         $page_id = \App\Models\SubMenu::where('m_controller_name',$req_uri)->value('id');
        $crud_rights = explode(',',Qs::getUserRole()->crud_rights);
       // print_r($crud_rights) ;die;

    }


    public static function dateFormat($date){

        return $newDate = date("d-M-Y", strtotime($date));
    }

    public static function dateFormat2($date){

        return date('h:i:s a', strtotime($date));
    }


    public static function curl($arr,$page){


        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,env('DOC_URL').$page);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
        http_build_query($arr));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close ($ch);

        return $server_output;
    }




    public static function finAuthorityToUser(){

        return ['cfo','super_admin'];
    }

    public static function storelogs($request){

        $data = array(
            'user_id'=> Auth::user()->id,
            'url'=> $request->url(),
            'ip_address'=>$request->session()->get('ip_address'),
            'latitude'=>$request->session()->get('latitude'),
            'longitude'=>$request->session()->get('longitude'),
            'timestamp'=>date('d-m-y h:i:s'),
            'type'=>'in');
        ActivityLogs::create($data);

    }

    public static function CheckUserLoanPresent($cnic)
    {
         $loan = \App\Models\LoanBorrower::select('*')
            ->join('loan_history', 'loan_history.borrower_id' , '=', 'loan_borrowers.id')
        ->where('cnic', $cnic)
        ->first();
        //        if($loan && $loan->loan_status_id !== 4 ){
        if($loan){
            return true;
        }
        return false;
    }

    public static function getStatus($param) {
        $LoanStatus = "";
        $title = \App\Models\LoanStatus::find($param)->title;

        $class = [1 => "bg-primary", 2 => "#80CC72", 3 => "bg-danger", 4 => "bg-danger", 5 => "notice", 6 => "bg-warning", 7 => "bg-primary", 8 => "bg-primary",
            9 => "bg-primary", 10 => "#216968"];
        if ($param == 2 || $param == 10) {
            $LoanStatus = '<span class="badge rounded-pill bg-success" >' . $title . '</span>';
        } else {
            $LoanStatus = '<span class="badge ' . ($class[$param]) . '">' . $title . '</span>';
        }


        return $LoanStatus;
    }



    public static function pageCheck(){


        $req_uri1 = rtrim(preg_replace('/[0-9]+/', '', substr(strtok($_SERVER['REQUEST_URI'],'?'),1)),"/");
        $req_uri2 = rtrim(preg_replace('/ops/i', '', $req_uri1),"/");
        $page_id = \App\Models\SubMenu::where('m_controller_name','like','%'.substr($req_uri2,1).'%')->value('id');
        if(Auth::user()->user_type != 'super_admin'){
            $sub_menu_ids = explode(',',static::getUserRole()->sub_menu_ids);
        }

        return array('page_id'=>$page_id,'sub_menu_ids'=>$sub_menu_ids);
    }
    
    public static function getUserInfo($account_no){
        $loan_histories = LoanHistory::with(['loan_borrower','loan_status'])->where('account_no', $account_no)->first();
        return $loan_histories->loan_borrower->fname.' '.$loan_histories->loan_borrower->mname.' '.$loan_histories->loan_borrower->lname;
    }

}


