<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Socialite;

class OAuthAuthorizationController extends Controller
{
    //
    public function redirectToProvider($driver) {
        return Socialite::driver($driver)->redirect();
    }

    public function handleProviderCallback($driver) {
        $user =  Socialite::driver($driver)->user();
        // dd($user)
    }

}