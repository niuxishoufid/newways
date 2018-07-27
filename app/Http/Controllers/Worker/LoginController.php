<?php
 
namespace App\Http\Controllers\Worker;
 
use App\Worker;
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
    protected $redirectTo = '/worker/home';
 
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:worker')->except('logout');
    }
    
    public function showLoginForm()
    {
        return view('worker.login');
    }
 
    protected function guard()
    {
        return Auth::guard('worker');
    }
    
    public function logout(Request $request)
    {
        Auth::guard('worker')->logout();
        $request->session()->flush();
        $request->session()->regenerate();
 
        return redirect('/worker/login');
    }
}