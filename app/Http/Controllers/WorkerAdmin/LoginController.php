<?php

namespace App\Http\Controllers\WorkerAdmin;

use App\WorkerAdmin;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    protected $redirectTo = '/worker_admin/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('guest')->except('logout');
        $this->middleware('guest:worker_admin')->except('logout');
    }
    
    public function showLoginForm()
    {
        //管理者ログインページのテンプレート
        return view('worker_admin.login');
    }
    
    protected function guard()
    {
        //管理者認証のguardを指定
        return Auth::guard('worker_admin');
    }
    
    public function logout(Request $request)
    {
        Auth::guard('worker_admin')->logout();
        $request->session()->flush();
        $request->session()->regenerate();

        return redirect('/worker_admin/login');
    }
}
