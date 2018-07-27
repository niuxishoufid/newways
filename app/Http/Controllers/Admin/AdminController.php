<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\UserInfo;
use App\Admin;
use App\StylistAdmin;
use App\Worker;
use App\WorkerInfo;
use App\Order;
use App\PointPurchase;
use App\Applicant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManagerStatic as Image;

class AdminController extends Controller {

    public function __construct() {
        $this->middleware('auth:admin');
    }

    public function index() {
        $admin = Auth::guard('admin')->user();
        return $admin->name;
    }

    public function list_admin(Request $request) {
        $admin_list = DB::table("admins")->paginate(10);
        $msg = "";
        $name = "";

        if ($request->input('action') == "検索") {
            $search1 = $request->input('name');
            $name = $search1;
            $query = Admin::query();

            if (!empty($search1)) {
                $query->where('name', 'like', '%' . $search1 . '%');
            }
            $admin_list = $query->paginate(10);
            $num = $query->count();

            if ($num == 0 && $request->input('page') < 2) {
                $msg = "検索結果が0件でした。";
            } else {
                $msg = "";
            }
        }

        return view("admin/list_admin")->with([
                    "admin_list" => $admin_list,
                    "name" => $name,
                    "msg" => $msg,
        ]);
    }

    public function edit_admin(Request $request) {
        if ($request->input("admin_id") == "") {
            return redirect("/admin/list_admin");
        }
        $admin_id = $request->input("admin_id");
        if ($request->input('action') == "更新") {
            $rules = [
                'name' => 'required',
                'email' => 'required | email',
                'password' => 'confirmed',
            ];
            $messages = [
                'name.required' => '名前は必ず入力してください。',
                'email.required' => 'メールアドレスは必ず入力してください。',
                'email.email' => 'メールアドレスを正しく入力してください。',
                'password.confirmed' => 'パスワードが確認用と違います。',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            $errors = $validator->errors();
            $request->flash();
            $now_admin = DB::table("admins")->where("id", $admin_id)->get();
            $admin = $now_admin[0];
            $email = $request->input('email');
            $name = $request->input('name');
            if ($validator->fails()) {
                return redirect()->to("admin/edit_admin?admin_id={$admin_id}")->withErrors($validator)->withInput();
            }
            $password = bcrypt($request->input('password'));
            $date = date("Y/m/d H:i:s");
            DB::table('admins')->where("id", $admin_id)
                    ->update([
                        'name' => $name,
                        'email' => $email,
                        'password' => $password,
                        'updated_at' => $date
            ]);
            return redirect("/admin/admin_editfinish");
        }
        $now_admin = DB::table("admins")->where("id", $admin_id)->get();
        // 該当するユーザーがいなかったらリダイレクト
        if ($now_admin == null) {
            return redirect("/admin/list_admin");
        }
        $admin = $now_admin[0];
        $email = $admin->email;
        return view("admin/edit_admin")->with([
                    "admin" => $admin,
                    "email" => $email,
        ]);
    }

    public function admin_editfinish(Request $request) {
        return view("admin/admin_editfinish");
    }

    public function list_applicant(Request $request) {
        $applicant_list = DB::table("applicants")->paginate(10);
        $name = "";
        $pref = "";
        $address_1 = "";
        $status = "";
        $msg = "";

        if ($request->input('action') == "検索") {
            $search1 = $request->input('name');
            $name = $search1;
            $search2 = $request->input('pref');
            $pref = $search2;
            if ($request->old('pref') != "") {
                $pref = $request->old('pref');
            }
            $search3 = $request->input('address_1');
            $address_1 = $search3;
            $search4 = $request->input('status');
            $status = $search4;
            //$query = Applicant::query();
            $query = DB::table("applicants");

            if (!empty($search1)) {
                $query->where('name', 'like', '%' . $search1 . '%');
            }
            if (!empty($search2)) {
                $query->where('pref', 'like', '%' . $search2 . '%');
            }
            if (!empty($search3)) {
                $query->where('address_1', 'like', '%' . $search3 . '%');
            }
            if (!empty($search4)) {
                $query->where('status', 'like', '%' . $search4 . '%');
            }
            $applicant_list = $query->paginate(10);
            $num = $query->count();

            if ($num == 0 && $request->input('page') < 2) {
                $msg = "検索結果は0件でした。";
            } else {
                $msg = "";
            }
        }

        return view("admin/list_applicant")->with([
                    "applicant_list" => $applicant_list,
                    "name" => $name,
                    "pref" => $pref,
                    "address_1" => $address_1,
                    "status" => $status,
                    "msg" => $msg,
        ]);
    }

    public function create_admin(Request $request) {

        if ($request->input("action") == "登録") {

            $rules = [
                'name' => 'required',
                'email' => 'required | email',
                'password' => 'required | confirmed'
            ];

            $messages = [
                'name.required' => 'お名前は必ず入力してください。',
                'email.required' => 'メールアドレスは必ず入力してください。',
                'email.email' => 'メールアドレスを正しく入力してください。',
                'password.required' => 'パスワード必ず入力してください。',
                'password.confirmed' => 'パスワードが確認用と違います。',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            $errors = $validator->errors();

            if ($validator->fails()) {
                return redirect('admin/create_admin')->withErrors($validator)->withInput();
            }

            $name = $request->input('name');
            $email = $request->input('email');
            $password = bcrypt($request->input('password'));

            Admin::create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ]);

            return redirect("admin/admin_finish");
        }

        return view("admin/create_admin");
    }

    public function create_stylistadmin(Request $request) {

        if ($request->input("action") == "登録") {

            $rules = [
                'name' => 'required',
                'email' => 'required | email',
                'password' => 'required | confirmed'
            ];

            $messages = [
                'name.required' => 'お名前は必ず入力してください。',
                'email.required' => 'メールアドレスは必ず入力してください。',
                'email.email' => 'メールアドレスを正しく入力してください。',
                'password.required' => 'パスワード必ず入力してください。',
                'password.confirmed' => 'パスワードが確認用と違います。',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            $errors = $validator->errors();

            if ($validator->fails()) {
                return redirect('admin/create_stylistadmin')->withErrors($validator)->withInput();
            }

            $name = $request->input('name');
            $email = $request->input('email');
            $password = bcrypt($request->input('password'));

            StylistAdmin::create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ]);
        }

        return view("admin/create_stylistadmin");
    }

    public function create_user(Request $request) {

        $msg = "";

        if ($request->input("action") == "登録") {

            $rules = [
                'name_1' => 'required',
                'name_2' => 'required',
                'name_kana_1' => 'required',
                'name_kana_2' => 'required',
                'email' => 'required | email',
                'tel' => 'required | digits_between:10,14',
                'post' => 'required',
                'pref' => 'required',
                'address_1' => 'required',
                'address_2' => 'required',
                'gender' => 'required',
            ];

            $messages = [
                'name_1.required' => '名前（姓）は必ず入力してください。',
                'name_2.required' => '名前（名）は必ず入力してください。',
                'name_kana_1.required' => 'フリガナ（姓）は必ず入力してください。',
                'name_kana_2.required' => 'フリガナ（名）は必ず入力してください。',
                'email.required' => 'メールアドレスは必ず入力してください。',
                'email.email' => 'メールアドレスを正しく入力してください。',
                'tel.required' => '電話番号は必ず入力してください。',
                'tel.digits_between' => '電話番号を半角数字で正しく入力してください。',
                'post.required' => '郵便番号は必ず入力してください。',
                'pref.required' => '都道府県は必ず入力してください。',
                'address_1.required' => '市区町村は必ず入力してください。',
                'address_2.required' => '番地は必ず入力してください。',
                'gender.required' => '性別は必ず入力してください。',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            $email = $request->input("email");
            $email_exists = false;
            if (!$validator->errors()->has('email')) {
                $email_cnt = DB::table('users')->where('email', $email)->count();
                if ($email_cnt > 0) {
                    $msg = "該当emailは他人より使われています。";
                    $validator->errors()->add('email', $msg);
                    $email_exists = true;
                }
            }

            $errors = $validator->errors();
            if ($validator->fails() || $email_exists) {
                return redirect('admin/create_user')->withErrors($validator)->withInput();
            }

            $name = $request->input('name_1') . "\t" . $request->input('name_2');
            $name_kana = $request->input('name_kana_1') . "\t" . $request->input('name_kana_2');
            $email = $request->input('email');
            $password = substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 7);

            User::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt($password),
            ]);

            $user_id = DB::table('users')->where('email', $email)->get()[0]->id;
            $tel = $request->input('tel');
            $gender = $request->input('gender');
            $birthday_1 = $request->input('birthday_1');
            $birthday_2 = $request->input('birthday_2');
            $birthday_3 = $request->input('birthday_3');
            $birthday = date("Y/m/d", mktime(0, 0, 0, $birthday_2, $birthday_3, $birthday_1));
            $post = $request->input('post');
            $pref = $request->input('pref');
            $points = 0;
            $address_1 = $request->input('address_1');
            $address_2 = $request->input('address_2');
            if ($request->input('address_3') != "") {
                $address_3 = $request->input('address_3');
            } else {
                $address_3 = "";
            }

            $date = date("Y/m/d H:i:s");
            $date_1 = date("Y-m-d");
            $a = (int) date('Ymd', strtotime($birthday));
            $b = (int) date('Ymd', strtotime($date));
            $age = (int) (($b - $a) / 10000);
            $status = "初回";

            DB::table('user_info')->insert([
                'user_id' => $user_id,
                'name' => $name,
                'name_kana' => $name_kana,
                'post' => $post,
                'pref' => $pref,
                'address_1' => $address_1,
                'address_2' => $address_2,
                'address_3' => $address_3,
                'gender' => $gender,
                'birthday' => $birthday,
                'age' => $age,
                'email' => $email,
                'tel' => $tel,
                'points' => $points,
                'created_at' => $date,
                'updated_at' => $date,
                'delete_flag' => '0',
            ]);

// 			DB::table('karte')->insert([
// 				'order_id' => "",
// 				'user_id' => $user_id,
//                 'user_name' => $name,
//                 'worker_id' => "",
//                 'worker_name' => "",
// 				'content_1' => "",
// 				'content_2' => "",
// 				'content_3' => "",
// 				'content_4' => "",
// 				'content_5' => "",
// 				'content_6' => "",
// 				'content_7' => "",
// 				'content_8_path' => "",
// 				'content_9_path' => "",
// 				'content_8_path_2' => "",
// 				'content_9_path_2' => "",
// 				'content_10' => "",
// 				'content_11' => "",
// 				'date' => $date_1,
// 				'status' => $status,
//             ]);
// 		    \Mail::send(new \App\Mail\Contact([
//                 'to' => $email,
//                 'to_name' => $name,
//                 'from' => 'deutschsiegteworldcup@gmail.com',
//                 'from_name' => 'ルーム・サロン',
//                 'subject' => '会員登録いただきありがとうございます',
//                 'name' => $name,
// 				'name_kana' => $name_kana,
//                 'gender' => $gender,
// 				'email' => $email,
// 				'tel' => $tel,
//                 'password' => $password,
//             ], 'signupto'));

            return redirect("/admin/user_finish");
        }

        return view("admin/create_user")->with([
                    "msg" => $msg,
        ]);
    }

    public function admin_finish(Request $request) {
        return view("admin.admin_finish");
    }

    public function stylistadmin_finish(Request $request) {
        return view("admin.stylistadmin_finish");
    }

    public function user_finish(Request $request) {
        return view("admin.user_finish");
    }

    public function edit_stylistadmin(Request $request) {

        if ($request->input("stylistadmin_id") == "") {
            return redirect("/admin/list_stylistadmin");
        }

        $stylistadmin_id = $request->input("stylistadmin_id");

        if ($request->input('action') == "更新") {

            $rules = [
                'name' => 'required',
                'email' => 'required | email',
                'password' => 'confirmed',
            ];

            $messages = [
                'name.required' => '名前は必ず入力してください。',
                'email.required' => 'メールアドレスは必ず入力してください。',
                'email.email' => 'メールアドレスを正しく入力してください。',
                'password.confirmed' => 'パスワードが確認用と違います。',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            $errors = $validator->errors();

            $request->flash();

            $now_stylistadmin = DB::table("stylist_admins")->where("id", $stylistadmin_id)->get();
            $stylistadmin = $now_stylistadmin[0];
            $email = $request->input('email');
            $name = $request->input('name');

            if ($validator->fails()) {
                return redirect()->to("admin/edit_stylistadmin?stylistadmin_id={$stylistadmin_id}")->withErrors($validator)->withInput();
            }


            $password = bcrypt($request->input('password'));
            $date = date("Y/m/d H:i:s");

            DB::table('stylist_admins')->where("id", $stylistadmin_id)
                    ->update([
                        'name' => $name,
                        'email' => $email,
                        'password' => $password,
                        'updated_at' => $date
            ]);

            return redirect("/admin/stylistadmin_editfinish");
        }

        $now_stylistadmin = DB::table("stylist_admins")->where("id", $stylistadmin_id)->get();

        // 該当するユーザーがいなかったらリダイレクト
        if ($now_stylistadmin == null) {
            return redirect("/admin/list_stylistadmin");
        }

        $stylistadmin = $now_stylistadmin[0];
        $email = $stylistadmin->email;

        return view("admin/edit_stylistadmin")->with([
                    "stylistadmin" => $stylistadmin,
        ]);
    }

    public function edit_applicant(Request $request) {

        if ($request->input("applicant_id") == "") {
            return redirect("/admin/list_applicant");
        }
        $applicant_id = $request->input("applicant_id");

        if ($request->input('action') == "更新") {
            $rules = [
                'name_1' => 'required',
                'name_2' => 'required',
                'name_kana_1' => 'required',
                'name_kana_2' => 'required',
                'post' => 'required | digits:7',
                'pref' => 'required',
                'address_1' => 'required',
                'address_2' => 'required',
                'email' => 'required | email',
                'tel' => 'required | digits_between:10,14',
            ];

            $messages = [
                'name_1.required' => '名前（姓）は必ず入力してください。',
                'name_2.required' => '名前（名）は必ず入力してください。',
                'name_kana_1.required' => 'フリガナ（姓）は必ず入力してください。',
                'name_kana_2.required' => 'フリガナ（名）は必ず入力してください。',
                'post.required' => '郵便番号は必ず入力してください。',
                'post.digits' => '郵便番号は必ず7桁の半角数字で入力してください。',
                'pref.required' => '都道府県は必ず入力してください。',
                'address_1.required' => '市区町村は必ず入力してください。',
                'address_2.required' => '番地は必ず入力してください。',
                'email.required' => 'メールアドレスは必ず入力してください。',
                'email.email' => 'メールアドレスを正しく入力してください。',
                'tel.required' => '電話番号は必ず入力してください。',
                'tel.digits_between' => '電話番号を半角数字で正しく入力してください。',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            $errors = $validator->errors();

            $request->flash();

            if ($validator->fails()) {
                return redirect()->to("admin/edit_applicant?applicant_id={$applicant_id}")->withErrors($validator)->withInput();
            }

            $name = $request->input('name_1') . "\t" . $request->input('name_2');
            $name_kana = $request->input('name_kana_1') . "\t" . $request->input('name_kana_2');
            $post = $request->input('post');
            $pref = $request->input('pref');
            $address_1 = $request->input('address_1');
            $address_2 = $request->input('address_2');
            if ($request->input('address_3') == "") {
                $address_3 = "";
            } else {
                $address_3 = $request->input('address_3');
            }
            $email = $request->input('email');
            $tel = $request->input('tel');
            $date = date("Y/m/d H:i:s");

            DB::table('applicants')->where("id", $applicant_id)
                    ->update([
                        'name' => $name,
                        'name_kana' => $name_kana,
                        'post' => $post,
                        'pref' => $pref,
                        'address_1' => $address_1,
                        'address_2' => $address_2,
                        'address_3' => $address_3,
                        'email' => $email,
                        'tel' => $tel,
                        'updated_at' => $date
            ]);
        }
        $now_applicant = DB::table("applicants")->where("id", $applicant_id)->get();

        // 該当するユーザーがいなかったらリダイレクト
        if (is_null($now_applicant)) {
            return redirect("/admin/list_applicant");
        }

        $applicant = $now_applicant[0];

        $aname = preg_split("/[\s,]+/", $applicant->name);
        $applicant->name_1 = $aname[0];
        $applicant->name_2 = $aname[1];

        $aname_kana = preg_split("/[\s,]+/", $applicant->name_kana);
        $applicant->name_kana_1 = $aname_kana[0];
        $applicant->name_kana_2 = $aname_kana[1];

        $abirthday = preg_split("/[\-\/\s:]+/", $applicant->birthday);
        $applicant->birthday_1 = $abirthday[0];
        $applicant->birthday_2 = $abirthday[1];
        $applicant->birthday_3 = $abirthday[2];
        return view("admin/edit_applicant")->with([
                    "applicant" => $applicant
        ]);
    }

    public function interview_applicant(Request $request) {
        if ($request->input("applicant_id") == "") {
            return redirect("/admin/list_applicant");
        }

        $applicant_id = $request->input("applicant_id");
        $now_applicant = DB::table("applicants")->where("id", $applicant_id)->get();

        // 該当するユーザーがいなかったらリダイレクト
        if (is_null($now_applicant)) {
            return redirect("/admin/list_applicant");
        }

        $applicant = $now_applicant[0];

        $abirthday = preg_split("/[\-\/\s:]+/", $applicant->birthday);
        $applicant->birthday_1 = $abirthday[0];
        $applicant->birthday_2 = $abirthday[1];
        $applicant->birthday_3 = $abirthday[2];

        if ($request->input('action') == "結果登録") {
            $rules = [
                'score_1' => 'required',
                'score_2' => 'required',
                'score_3' => 'required',
                'score_4' => 'required',
                'score_5' => 'required',
                'score_6' => 'required',
                'score_7' => 'required',
                'score_age' => 'required',
                'taiou_area' => 'required',
                'taiou_area_2' => 'required',
                'status' => 'required',
            ];

            $messages = [
                'score_1.required' => 'カットの評価は必ず入力してください。',
                'score_2.required' => 'カラーの評価は必ず入力してください。',
                'score_3.required' => 'パーマの評価は必ず入力してください。',
                'score_4.required' => '縮毛矯正の評価は必ず入力してください。',
                'score_5.required' => 'エクステの評価は必ず入力してください。',
                'score_6.required' => 'ヘアセットの評価は必ず入力してください。',
                'score_7.required' => '会話の評価は必ず入力してください。',
                'score_age.required' => '対応年齢層は必ず入力してください。',
                'taiou_area.required' => '対応エリア（都道府県）は必ず入力してください。',
                'taiou_area_2.required' => '対応エリア（市区町村）の評価は必ず入力してください。',
                'status.required' => '合格結果は必ず入力してください。',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            $errors = $validator->errors();

            //$request->flash();

            if ($validator->fails()) {
                return redirect()->to("admin/interview_applicant?applicant_id={$applicant_id}")
                                ->withErrors($validator)->withInput();
            }

            $score_1 = $request->input('score_1');
            $score_2 = $request->input('score_2');
            $score_3 = $request->input('score_3');
            $score_4 = $request->input('score_4');
            $score_5 = $request->input('score_5');
            $score_6 = $request->input('score_6');
            $score_7 = $request->input('score_7');
            $score_age = $request->input('score_age');
            if ($request->input('score_biko') == "") {
                $score_biko = "";
            } else {
                $score_biko = $request->input('score_biko');
            }
            $status = $request->input('status');
            $date = date("Y/m/d H:i:s");

            if ($request->input('fuka_1') == "1") {
                $can_do_1 = "";
            } else {
                $can_do_1 = "カット";
            }
            if ($request->input('fuka_2') == "1") {
                $can_do_2 = "";
            } else {
                $can_do_2 = "カラー";
            }
            if ($request->input('fuka_3') == "1") {
                $can_do_3 = "";
            } else {
                $can_do_3 = "パーマ";
            }
            if ($request->input('fuka_4') == "1") {
                $can_do_4 = "";
            } else {
                $can_do_4 = "縮毛矯正";
            }
            if ($request->input('fuka_5') == "1") {
                $can_do_5 = "";
            } else {
                $can_do_5 = "エクステ";
            }
            if ($request->input('fuka_6') == "1") {
                $can_do_6 = "";
            } else {
                $can_do_6 = "ヘアセット";
            }
            $can_do_7 = "";
            $can_do = "";
            $profile_img_path = "";

            DB::table('applicants')->where("id", $applicant_id)
                    ->update([
                        'score_1' => $score_1,
                        'score_2' => $score_2,
                        'score_3' => $score_3,
                        'score_4' => $score_4,
                        'score_5' => $score_5,
                        'score_6' => $score_6,
                        'score_7' => $score_7,
                        'score_age' => $score_age,
                        'score_biko' => $score_biko,
                        'status' => $status,
                        'updated_at' => $date,
            ]);
            if ($status == "面接合格") {
                $name = $request->input('name');
                $name_kana = $request->input('name_kana');
                $email = $request->input('email');
                $password = substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 7);

                Worker::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => bcrypt($password),
                ]);

                $worker_id = DB::table('workers')->where('email', $email)->get()[0]->id;
                $post = $request->input('post');
                $pref = $request->input('pref');
                $address_1 = $request->input('address_1');
                $address_2 = $request->input('address_2');
                if ($request->input('address_3') != "") {
                    $address_3 = $request->input('address_3');
                } else {
                    $address_3 = "";
                }
                $gender = $request->input('gender');
                $job = $request->input('job');
                $job_2 = $request->input('job_2');
                $job_3 = $request->input('job_3');
                $tel = $request->input('tel');
                $birthday = $request->input('birthday');
                $date_1 = date("Y-m-d");
                $date = date("Y/m/d H:i:s");
                $a = (int) date('Ymd', strtotime($birthday));
                $b = (int) date('Ymd', strtotime($date));
                $age = (int) (($b - $a) / 10000);

                $points = 0;
                $selfpr = "";
                $profile = "";
                $condition = "";
                $taiou_area = $request->input('taiou_area');
                $taiou_area_2 = $request->input('taiou_area_2');

                DB::table('worker_info')->insert([
                    'worker_id' => $worker_id,
                    'applicant_id' => $applicant_id,
                    'name' => $name,
                    'name_kana' => $name_kana,
                    'post' => $post,
                    'pref' => $pref,
                    'address_1' => $address_1,
                    'address_2' => $address_2,
                    'address_3' => $address_3,
                    'gender' => $gender,
                    'birthday' => $birthday,
                    'age' => $age,
                    'email' => $email,
                    'tel' => $tel,
                    'score_1' => $score_1,
                    'score_2' => $score_2,
                    'score_3' => $score_3,
                    'score_4' => $score_4,
                    'score_5' => $score_5,
                    'score_6' => $score_6,
                    'score_7' => $score_7,
                    'can_do' => $can_do,
                    'can_do_1' => $can_do_1,
                    'can_do_2' => $can_do_2,
                    'can_do_3' => $can_do_3,
                    'can_do_4' => $can_do_4,
                    'can_do_5' => $can_do_5,
                    'can_do_6' => $can_do_6,
                    'can_do_7' => $can_do_7,
                    'taiou_area' => $taiou_area,
                    'taiou_area_2' => $taiou_area_2,
                    'score_age' => $score_age,
                    'score_biko' => $score_biko,
                    'job' => $job,
                    'job_2' => $job_2,
                    'job_3' => $job_3,
                    'points' => $points,
                    'selfpr' => $selfpr,
                    'profile' => $profile,
                    'condition' => $condition,
                    //'created_time' => $date_1,
                    'created_at' => $date,
                    'updated_at' => $date,
                    'profile_img_path' => $profile_img_path,
                    'delete_flag' => '0',
                ]);

                DB::table('applicants')->where("id", $applicant_id)
                        ->update([
                            'status' => "ワーカー登録済",
                ]);

// 				\Mail::send(new \App\Mail\Contact([
// 					'to' => 'k-smallfield3271@docomo.ne.jp',
// 					'to_name' => $name,
// 					'from' => 'deutschsiegteworldcup@gmail.com',
// 					'from_name' => 'ルーム・サロン',
// 					'subject' => 'ルーム・サロンへのご応募ありがとうございます',
// 					'email' => $email,
// 					'name' => $name,
// 					'password' => $password,
// 				], 'passed'));
            }
            //return redirect("admin/worker_finish");
            return view("admin/worker_finish");
        }

        return view("admin/interview_applicant")->with([
                    "applicant" => $applicant
        ]);
    }

    public function list_worker(Request $request) {
        $worker_list = DB::table("worker_info")->paginate(10);
        $name = "";
        $pref = "";
        $msg = "";
        if ($request->input('action') == "検索") {
            $search1 = $request->input('name');
            $name = $search1;
            $search2 = $request->input('pref');
            $pref = $search2;
            //             if($request->old('pref')!=""){
            //                 $pref = $request->old('pref');
            //             }
            //$query = WorkerInfo::query();
            $query = DB::table("worker_info");
            if (!empty($search1)) {
                $query->where('name', 'like', '%' . $search1 . '%');
            }
            if (!empty($search2)) {
                $query->where('pref', 'like', '%' . $search2 . '%');
            }
            $worker_list = $query->paginate(10);
            $num = $query->count();
            if ($num == 0 && $request->input('page') < 2) {
                $msg = "検索結果は0件でした。";
            } else {
                $msg = "";
            }
        }
        return view("admin/list_worker")->with([
                    "worker_list" => $worker_list,
                    "name" => $name,
                    "pref" => $pref,
                    "msg" => $msg,
        ]);
    }

    public function list_stylistadmin(Request $request) {

        $stylistadmin_list = DB::table("stylist_admins")->paginate(10);
        $msg = "";
        $name = "";

        if ($request->input('action') == "検索") {
            $search1 = $request->input('name');
            $name = $search1;
            $query = StylistAdmin::query();

            if (!empty($search1)) {
                $query->where('name', 'like', '%' . $search1 . '%');
            }
            $stylistadmin_list = $query->paginate(10);
            $num = $query->count();

            if ($num == 0 && $request->input('page') < 2) {
                $msg = "検索結果が0件でした。";
            } else {
                $msg = "";
            }
        }

        return view("admin/list_stylistadmin")->with([
                    "stylistadmin_list" => $stylistadmin_list,
                    "msg" => $msg,
                    "name" => $name,
        ]);
    }

    public function list_user(Request $request) {

        $user_list = DB::table("user_info")->paginate(10);

        $name = "";
        $pref = "";
        $msg = "";

        if ($request->input('action') == "検索") {
            $search1 = $request->input('name');
            $name = $search1;
            $search2 = $request->input('pref');
            $pref = $search2;
            $query = UserInfo::query();

            if (!empty($search1)) {
                $query->where('name', 'like', '%' . $search1 . '%');
            }
            if (!empty($search2)) {
                $query->where('pref', 'like', '%' . $search2 . '%');
            }
            $user_list = $query->paginate(10);
            $num = $query->count();

            if ($num == 0 && $request->input('page') < 2) {
                $msg = "検索結果は0件でした。";
            } else {
                $msg = "";
            }
        }

        return view("admin/list_user")->with([
                    "user_list" => $user_list,
                    "name" => $name,
                    "pref" => $pref,
                    "msg" => $msg,
        ]);
    }

    public function search_user(Request $request) {

        $user_list = DB::table("user_info")->paginate(10);

        if ($request->input('action') == "検索") {
            $search1 = $request->input('name');
            $name = $search1;
            $search2 = $request->input('pref');
            $pref = $search2;
            $query = UserInfo::query();

            if (!empty($search1)) {
                $query->where('name', 'like', '%' . $search1 . '%');
            }
            if (!empty($search2)) {
                $query->where('pref', 'like', '%' . $search2 . '%');
            }
            $user_list = $query->paginate(10);
            $num = $query->count();

            if ($num == 0) {
                $msg = "検索結果は0件でした。";
            } else {
                $msg = "";
            }

            return view('admin/list_user')->with([
                        "user_list" => $user_list,
                        "name" => $name,
                        "pref" => $pref,
                        "msg" => $msg,
            ]);
        }
    }

    public function list_order(Request $request) {
        $order_list = DB::table("orders")->paginate(10);
        $user_name = "";
        $worker_name = "";
        $status = "";
        $msg = "";
        return view("admin/list_order")->with([
                    "order_list" => $order_list,
                    "user_name" => $user_name,
                    "worker_name" => $worker_name,
                    "status" => $status,
                    "msg" => $msg,
        ]);
    }

    public function search_order(Request $request) {
        if ($request->input('action') == "検索") {
            $search1 = $request->input('user_name');
            $user_name = $search1;
            $search2 = $request->input('worker_name');
            $worker_name = $search2;
            $search3 = $request->input('status');
            $status = $search3;
            //$query = Order::query();
            $query = DB::table("orders");

            if (!empty($search1)) {
                $query->where('user_name', 'like', '%' . $search1 . '%');
            }
            if (!empty($search2)) {
                $query->where('worker_name', 'like', '%' . $search2 . '%');
            }
            if (!empty($search3)) {
                $query->where('status', 'like', '%' . $search3 . '%');
            }
            $order_list = $query->paginate(10);
            $num = $query->count();

            if ($num == 0) {
                $msg = "検索結果は0件でした。";
            } else {
                $msg = "";
            }

            return view('admin/list_order')->with([
                        "order_list" => $order_list,
                        "user_name" => $user_name,
                        "status" => $status,
                        "worker_name" => $worker_name,
                        "msg" => $msg,
            ]);
        }
    }

    public function view_user(Request $request) {

        if ($request->input("user_id") == "") {
            return redirect("/admin/list_user");
        }

        $user_id = $request->input("user_id");
        $now_user = DB::table("user_info")->where("id", $user_id)->get();

        // 該当するユーザーがいなかったらリダイレクト
        if ($now_user == null) {
            return redirect("/admin/list_user");
        }

        $user = $now_user[0];

        return view("admin/view_user")->with([
                    "user" => $user
        ]);
    }

    public function view_worker(Request $request) {
        if ($request->input("worker_id") == "") {
            return redirect("/admin/list_worker");
        }

        $worker_id = $request->input("worker_id");
        $now_worker = DB::table("worker_info")->where("id", $worker_id)->get();
        // 該当するユーザーがいなかったらリダイレクト
        if ($now_worker == null) {
            return redirect("/admin/list_worker");
        }
        $worker = $now_worker[0];

        $applicant_id = $worker->applicant_id;
        $applicant = DB::table("applicants")->where("id", $applicant_id)->first();
        if ($applicant == null) {
            return redirect("/admin/list_worker");
        }

        return view("admin/view_worker")->with([
                    "worker" => $worker,
                    "applicant" => $applicant,
        ]);
    }

    public function view_order(Request $request) {
        if ($request->input("order_id") == "") {
            return redirect("/admin/list_order");
        }
        $order_id = $request->input("order_id");
        $now_order = DB::table("orders")->where("id", $order_id)->get();

        // 該当するユーザーがいなかったらリダイレクト
        if ($now_order == null) {
            return redirect("/admin/list_order");
        }

        $order = $now_order[0];

        $now_user = DB::table("users")->where("id", $order->user_id)->get();
        if ($now_user == null) {
            return redirect("/admin/list_order");
        }

        $now_worker = DB::table("workers")->where("id", $order->worker_id)->get();
        if ($now_worker == null) {
            return redirect("/admin/list_order");
        }

        $user = $now_user[0];
        $worker = $now_worker[0];

        return view("admin/view_order")->with([
                    "order" => $order,
                    "user" => $user,
                    "worker" => $worker,
        ]);
    }

    public function edit_user(Request $request) {

        if ($request->input("user_id") == "") {
            return redirect("/admin/list_user");
        }

        $user_id = $request->input("user_id");

        if ($request->input('action') == "更新") {
            $rules = [
                'name_1' => 'required',
                'name_2' => 'required',
                'name_kana_1' => 'required',
                'name_kana_2' => 'required',
                'post' => 'required | digits:7',
                'pref' => 'required',
                'address_1' => 'required',
                'address_2' => 'required',
                'email' => 'required | email',
                'gender' => 'required',
                'tel' => 'required | digits_between:10,14',
                'points' => 'required',
            ];
            $messages = [
                'name_1.required' => '名前（姓）は必ず入力してください。',
                'name_2.required' => '名前（名）は必ず入力してください。',
                'name_kana_1.required' => 'フリガナ（姓）は必ず入力してください。',
                'name_kana_2.required' => 'フリガナ（名）は必ず入力してください。',
                'post.required' => '郵便番号は必ず入力してください。',
                'post.digits' => '郵便番号は必ず7桁の半角数字で入力してください。',
                'pref.required' => '都道府県は必ず入力してください。',
                'address_1.required' => '市区町村は必ず入力してください。',
                'address_2.required' => '番地は必ず入力してください。',
                'email.required' => 'メールアドレスは必ず入力してください。',
                'email.email' => 'メールアドレスを正しく入力してください。',
                'gender.required' => '性別は必ず入力してください。',
                'tel.required' => '電話番号は必ず入力してください。',
                'tel.digits_between' => '電話番号を半角数字で正しく入力してください。',
                'points.required' => '保有ポイントは必ず入力してください。',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            $email = $request->input("email");
            $email_exists = false;
            if (!$validator->errors()->has('email')) {
                $email_cnt = DB::table('user_info')->where('email', $email)
                        ->where('id', '<>', $user_id)
                        ->count();
                if ($email_cnt > 0) {
                    $msg = "該当emailは他人より使われています。";
                    $validator->errors()->add('email', $msg);
                    $email_exists = true;
                }
            }

            $errors = $validator->errors();
            $request->flash();
            if ($validator->fails() || $email_exists) {
                return redirect()->to("admin/edit_user?user_id={$user_id}")->withErrors($errors)->withInput();
                //return redirect("admin/edit_user?user_id={$user_id}")->withErrors($validator)->withInput();
            }

            $name = $request->input('name_1') . "\t" . $request->input('name_2');
            $name_kana = $request->input('name_kana_1') . "\t" . $request->input('name_kana_2');
            $post = $request->input('post');
            $pref = $request->input('pref');
            $address_1 = $request->input('address_1');
            $address_2 = $request->input('address_2');
            if ($request->input('address_3') == "") {
                $address_3 = "";
            } else {
                $address_3 = $request->input('address_3');
            }
            $gender = $request->input('gender');
            $email = $request->input('email');
            $tel = $request->input('tel');
            $points = $request->input('points');
            $date = date("Y/m/d H:i:s");

            DB::table('user_info')->where("id", $user_id)
                    ->update([
                        'name' => $name,
                        'name_kana' => $name_kana,
                        'post' => $post,
                        'pref' => $pref,
                        'address_1' => $address_1,
                        'address_2' => $address_2,
                        'address_3' => $address_3,
                        'gender' => $gender,
                        'email' => $email,
                        'tel' => $tel,
                        'points' => $points,
                        'updated_at' => $date,
            ]);
            return redirect("/admin/user_editfinish");
        }

        $now_user = DB::table("user_info")->where("id", $user_id)->get();

        // 該当するユーザーがいなかったらリダイレクト
        if ($now_user == null) {
            return redirect("/admin/list_user");
        }

        $user = $now_user[0];

        $aname = preg_split("/[\s,]+/", $user->name);
        $user->name_1 = $aname[0];
        $user->name_2 = $aname[1];

        $aname_kana = preg_split("/[\s,]+/", $user->name_kana);
        $user->name_kana_1 = $aname_kana[0];
        $user->name_kana_2 = $aname_kana[1];

        $abirthday = preg_split("/[\-\/\s:]+/", $user->birthday);
        $user->birthday_1 = $abirthday[0];
        $user->birthday_2 = $abirthday[1];
        $user->birthday_3 = $abirthday[2];

        return view("admin/edit_user")->with([
                    "user" => $user,
        ]);
    }

    public function stylistadmin_editfinish(Request $request) {
        return view("admin/stylistadmin_editfinish");
    }

    public function worker_editfinish(Request $request) {
        return view("admin/worker_editfinish");
    }

    public function user_editfinish(Request $request) {
        return view("admin/user_editfinish");
    }

    public function info_editfinish(Request $request) {
        return view("admin/info_editfinish");
    }

    public function edit_worker(Request $request) {
        if ($request->input("worker_id") == "") {
            return redirect("/admin/list_worker");
        }

        $worker_id = $request->input("worker_id");

        if ($request->input('action') == "更新") {

            $rules = [
                'name_1' => 'required',
                'name_2' => 'required',
                'name_kana_1' => 'required',
                'name_kana_2' => 'required',
                'post' => 'required | digits:7',
                'pref' => 'required',
                'address_1' => 'required',
                'address_2' => 'required',
                'email' => 'required | email',
                'tel' => 'required | digits_between:10,14',
            ];

            $messages = [
                'name_1.required' => '名前（姓）は必ず入力してください。',
                'name_2.required' => '名前（名）は必ず入力してください。',
                'name_kana_1.required' => 'フリガナ（姓）は必ず入力してください。',
                'name_kana_2.required' => 'フリガナ（名）は必ず入力してください。',
                'post.required' => '郵便番号は必ず入力してください。',
                'post.digits' => '郵便番号は必ず7桁の半角数字で入力してください。',
                'pref.required' => '都道府県は必ず入力してください。',
                'address_1.required' => '市区町村は必ず入力してください。',
                'address_2.required' => '番地は必ず入力してください。',
                'email.required' => 'メールアドレスは必ず入力してください。',
                'email.email' => 'メールアドレスを正しく入力してください。',
                'tel.required' => '電話番号は必ず入力してください。',
                'tel.digits_between' => '電話番号を半角数字で正しく入力してください。',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            $errors = $validator->errors();

            $request->flash();

            if ($validator->fails()) {
                return redirect()->to("admin/edit_worker?worker_id={$worker_id}")->withErrors($validator)->withInput();
            }

            $name = $request->input('name_1') . "\t" . $request->input('name_2');
            $name_kana = $request->input('name_kana_1') . "\t" . $request->input('name_kana_2');
            $post = $request->input('post');
            $pref = $request->input('pref');
            $address_1 = $request->input('address_1');
            $address_2 = $request->input('address_2');
            if ($request->input('address_3') == "") {
                $address_3 = "";
            } else {
                $address_3 = $request->input('address_3');
            }
            $email = $request->input('email');
            $tel = $request->input('tel');
            $date = date("Y/m/d H:i:s");

            DB::table('worker_info')->where("id", $worker_id)
                    ->update([
                        'name' => $name,
                        'name_kana' => $name_kana,
                        'post' => $post,
                        'pref' => $pref,
                        'address_1' => $address_1,
                        'address_2' => $address_2,
                        'address_3' => $address_3,
                        'email' => $email,
                        'tel' => $tel,
                        'updated_at' => $date
            ]);
            return redirect("admin/worker_editfinish");
        }

        $now_worker = DB::table("worker_info")->where("id", $worker_id)->get();
        // 該当するユーザーがいなかったらリダイレクト
        if ($now_worker == null) {
            return redirect("/admin/list_worker");
        }
        $worker = $now_worker[0];

        $now_work = DB::table("workers")->where("id", $worker->worker_id)->get();
        $work = $now_work[0];

        $aname = preg_split("/[\s,]+/", $worker->name);
        $worker->name_1 = $aname[0];
        $worker->name_2 = $aname[1];

        $aname_kana = preg_split("/[\s,]+/", $worker->name_kana);
        $worker->name_kana_1 = $aname_kana[0];
        $worker->name_kana_2 = $aname_kana[1];

        $abirthday = preg_split("/[\-\/\s:]+/", $worker->birthday);
        $worker->birthday_1 = $abirthday[0];
        $worker->birthday_2 = $abirthday[1];
        $worker->birthday_3 = $abirthday[2];

        return view("admin/edit_worker")->with([
                    "worker" => $worker,
                    "work" => $work,
        ]);
    }

    public function list_pointpay(Request $request) {
        $point_list = DB::table("point_purchases")->paginate(10);

        if ($request->input('action') == "決済") {
            $id = $request->input('id');
            $user_id = $request->input('user_id');
            $now_user = DB::table('user_info')->where("user_id", $user_id)->get();
            if ($now_user == null) {
                return redirect("/admin/list_pointpay");
            }
            $now_points = DB::table("point_purchases")->where("id", $id)
                    ->where("status", "未付与")
                    ->get();
            if ($now_points == null) {
                return redirect("/admin/list_pointpay");
            }
            $buy_points = 0;
            foreach ($now_points as $rec) {
                $buy_points = $buy_points + $rec->buy_points;
            }
            $user = $now_user[0];
            $old_points = $user->points;
            $points = $buy_points + $old_points;
            $date = date("Y/m/d H:i:s");
            DB::table('point_purchases')->where("user_id", $user_id)
                    ->update([
                        'status' => "付与完了",
                        'updated' => $date,
            ]);
            DB::table('user_info')->where("user_id", $user_id)
                    ->update([
                        'points' => $points,
                        'updated' => $date,
            ]);
        }
        $name = "";
        $msg = "";
        if ($request->input('action') == "検索") {
            $search1 = $request->input('name');
            $name = $search1;
            $query = PointPurchase::query();

            if (!empty($search1)) {
                $query->where('name', 'like', '%' . $search1 . '%');
            }
            $point_list = $query->paginate(10);
            $num = $query->count();

            if ($num == 0 && $request->input('page') < 2) {
                $msg = "検索結果が0件でした。";
            } else {
                $msg = "";
            }
        }

        return view("admin/list_pointpay")->with([
                    "point_list" => $point_list,
                    "name" => $name,
                    "msg" => $msg,
        ]);
    }

    public function view_info(Request $request) {
        $point_time_info = DB::table("point_time_info")->get();
        // 該当するユーザーがいなかったらリダイレクト
        if ($point_time_info->count() == 0) {
            $msg = "ポイント・時間情報がありません。";
            $info = array();
        } else {
            $msg = "";
            $info = $point_time_info[0];
        }
        return view("admin/view_info")->with([
                    "info" => $info,
                    "msg" => $msg,
        ]);
    }

    public function edit_info(Request $request) {
        if ($request->input('action') == "更新") {
            $points_cut = $request->input('points_cut');
            $points_color = $request->input('points_color');
            $points_pama = $request->input('points_pama');
            $points_correction = $request->input('points_correction');
            $points_extension = $request->input('points_extension');
            $points_haircut = $request->input('points_haircut');
            $times_cut = $request->input('times_cut');
            $times_color = $request->input('times_color');
            $times_pama = $request->input('times_pama');
            $times_correction = $request->input('times_correction');
            $times_extension = $request->input('times_extension');
            $times_haircut = $request->input('times_haircut');
            $date = date("Y/m/d H:i:s");
            DB::table('point_time_info')
                    ->update([
                        'points_cut' => $points_cut,
                        'points_color' => $points_color,
                        'points_pama' => $points_pama,
                        'points_correction' => $points_correction,
                        'points_extension' => $points_extension,
                        'points_haircut' => $points_haircut,
                        'times_cut' => $times_cut,
                        'times_color' => $times_color,
                        'times_pama' => $times_pama,
                        'times_correction' => $times_correction,
                        'times_extension' => $times_extension,
                        'times_haircut' => $times_haircut,
                        'updated_at' => $date,
            ]);
            return redirect("/admin/info_editfinish");
        }

        $point_time_info = DB::table("point_time_info")->get();
        // 該当するユーザーがいなかったらリダイレクト
        if ($point_time_info->count() == 0) {
            return redirect("/admin/view_user");
        } else {
            $msg = "";
            $info = $point_time_info[0];
        }
        return view("admin/edit_info")->with([
                    "info" => $info,
                    "msg" => $msg,
        ]);
    }

}
