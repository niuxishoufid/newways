<?php

namespace App\Http\Controllers\Worker;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller {

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
    public function __construct() {
        $this->middleware('auth:worker');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('worker.home');
    }

    public function home() {
        $worker_id = Auth::id();
        $order_list = DB::table("orders")->where("worker_id", $worker_id)->where("status", "依頼受付")->get();

        return view("worker/home")->with([
                    "order_list" => $order_list
        ]);
    }

}
