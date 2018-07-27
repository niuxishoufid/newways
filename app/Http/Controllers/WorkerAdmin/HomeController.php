<?php

namespace App\Http\Controllers\WorkerAdmin;  // \Adminを追加

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{

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
        $this->middleware('auth:worker_admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('worker_admin.home');
    }

    public function home(Request $request)
    {
        $now_applicant_list = DB::table("applicants")->where("status","応募")->get();

        $applicant_list = [];
        foreach($now_applicant_list as $now){
             $applicant_list[] = $now;
        }

        return view("admin/home")->with([
            "applicant_list" => $applicant_list
        ]);
    }
}
