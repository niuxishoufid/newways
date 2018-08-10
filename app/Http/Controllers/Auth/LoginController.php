<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Socialite;
use \App\SocialAccountService;

class LoginController extends Controller {
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
     * @return void
     */
    public function __construct() {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return Response
     */
    public function redirectToProvider($provider) {
        //プロバイダーのredirect()メソッドを呼び出して、
        //ユーザーをSNS認証エンドポイントへリダイレクトしています。
        //またredirect()を呼び出す前に、デフォルトのスコープをscopes()で変更できます
        //return \Socialite::driver($provider)->redirect();
        return Socialite::driver($provider)->scopes(['users:email'])->redirect();
    }

    /**
     * Obtain the user information
     *
     * @return Response
     */
    public function handleProviderCallback($provider) {
        try {
            //userオブジェクト（Laravel\Socialite\Contracts\Userのインスタンス）を
            //プロバイダーから受け取ります。
            //userオブジェクトにはユーザー情報を取得するgetterメソッドがあり、
            //ユーザーの名前、メールアドレス、アクセストークンなどを取得できます
            $providerUser = Socialite::with($provider)->user();
        } catch (\Exception $e) {
            return redirect('/login');
        }
        $accountService = new SocialAccountService();
        //ローカルのuserオブジェクト（アプリのusersテーブルに格納されている）を取得する、
        //存在しなければ作成します
        $authUser = $accountService->findOrCreate($providerUser, $provider);
        //ユーザーをログインさせる
        auth()->login($authUser, true);

        return redirect()->to('/home');
    }

}
