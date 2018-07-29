<?php
namespace App\Http\Middleware;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Closure;

class Language
{

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Session::has('applocale') and array_key_exists(Session::get('applocale'), Config::get('languages'))) {
            App::setLocale(Session::get('applocale'));
        } else { // This is optional as Laravel will automatically set the fallback language if there is none specified
            App::setLocale("zh_CN");
            $locale = App::getLocale();
            if ($locale and array_key_exists($locale, Config::get('languages')))
                App::setLocale($locale);
            else
                App::setLocale(Config::get('app.fallback_locale'));
        }
        return $next($request);
    }
}

