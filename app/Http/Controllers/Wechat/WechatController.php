<?php

namespace App\Http\Controllers\Wechat;

use App\User;
use App\Http\Controllers\Controller;
use App\Mail\EmailVerification;
use App\Events\RegisteredEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Auth\Events\Registered;
use Carbon\Carbon;
use class_Weichat;

class WechatController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest');
    }

    public function index() {
        return view('auth.wechat.index');
    }

    public function login(Request $request) {
        $weichat = new class_Weichat();
        $code = $request->input('code');
        if (!isset($code)) {
            $callback = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . 
                    $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            //セッションIDの再生
            $request->session()->regenerate();
            //-------生成唯一随机串防CSRF攻击
            $state = md5(uniqid(rand(), TRUE));
            $request->session()->put('wx_state', $state); //存到SESSION
            $jumpurl = $weichat->qrconnect(urlencode($callback), "snsapi_login", $state);
            //Header("Location: $jumpurl");
            return redirect()->away($jumpurl);
        } else {
            if (($request->input('state')) != $request->session()->get('wx_state')) {
                exit("5001");
            }
            $oauth2_info = $weixin->oauth2_access_token($request->input('code'));
            $providerUser = $weixin->oauth2_get_user_info($oauth2_info['access_token'], $oauth2_info['openid']);
            //得到用户资料
            //var_dump($providerUser);
            
            //ローカルのuserオブジェクト（アプリのusersテーブルに格納されている）を取得する、
            //存在しなければ作成します
            $authUser = $this->findOrCreate($providerUser, 'wechat');
            //ユーザーをログインさせる
            auth()->login($authUser, true);

            return redirect()->to('/home');
        }
    }

    //現在のプロバイダーIDに関連したSNSアカウントが登録されているか確認するクエリを
    //linked_social_accountsテーブルに発行し、登録されていて、
    //SNSアカウントを含むローカルuserオブジェクトを返します
    private function findOrCreate(ProviderUser $providerUser, $provider) {
        $account = LinkedSocialAccount::where('provider_name', $provider)
                ->where('provider_id', $providerUser->getId())
                ->first();

        if ($account) {
            return $account->user;
        } else {
            $user = User::where('email', $providerUser->getEmail())->first();
            if (!$user) {
                $user = User::create([
                            'email' => $providerUser->getEmail(),
                            'name' => $providerUser->getName(),
                ]);
            }
            $user->accounts()->create([
                'provider_id' => $providerUser->getId(),
                'provider_name' => $provider,
            ]);
            return $user;
        }
    }

}
