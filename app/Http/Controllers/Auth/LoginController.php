<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use DB;
use Auth;
use Session;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use App\Rules\MatchOldPassword;

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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
    * Create a new controller instance.
    *
    * @return void
    */
    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }
    /** index page login */
    public function login()
    {
        return view('admin.auth.login');
    }

    /** login with databases */
    public function authenticate(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'email'    => 'required|string',
                'password' => 'required|string',
            ]);

            $email     = $request->email;
            $password  = $request->password;

            if (Auth::guard('admin')->attempt(['email'=>$email,'password'=>$password])) {
                /** get session */
                $user = Auth::guard('admin')->user();
                Session::put('name', $user->name);
                Session::put('email', $user->email);
                Session::put('user_id', $user->id);
                Session::put('join_date', $user->join_date);
                Session::put('role_name', $user->role_name);
                Session::put('avatar', $user->avtar);
                Toastr::success('Login successfully :)','Success');
                return redirect()->intended('admin');
            } else {
                Toastr::error('fail, WRONG USERNAME OR PASSWORD :)','Error');
                return redirect('admin/login');
            }

        } catch(\Exception $e) {
            DB::rollback();
            Toastr::error('fail, LOGIN :)','Error');
            return redirect('admin/login')->withErrors(['login' => 'An error occurred during login.']);
        }
    }

    /** logout */
    public function logout( Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Toastr::success('Logout successfully :)','Success');
        return redirect('admin/login');
    }

}
