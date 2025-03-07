<?php

namespace App\Http\Controllers\Auth;
use App\Models\ActivityLogins;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void$field
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    protected function authenticated($request, $user){

        $request->session()->put('ip_address', $request->ip());
        $request->session()->put('latitude', $request->latitude);
        $request->session()->put('longitude', $request->longitude);
        ActivityLogins::create(array('user_id'=>$user->id,'ip_address'=>$request->ip(),'latitude'=>$request->latitude,'longitude'=>$request->longitude,'type'=>'in'));
         return redirect()->route('dashboard');

    }
    /*
     *  Login with Username or Email
     * */
    public function username()
    {
        $identity = request()->identity;
        $field = filter_var($identity, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$field => $identity]);
        return $field;
    }
}
