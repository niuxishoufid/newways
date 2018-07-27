<?php

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');

Route::get('/lang/{lang}', 'LanguageController@switchLang');

//Route::group(['prefix' => 'admin'], function() {
Route::prefix('admin')->name('admin.')->group(function() {
    Route::get('login', 'Admin\LoginController@showLoginForm')->name('login');
    Route::post('login', 'Admin\LoginController@login')->name('login.submit');
    Route::get('register', 'Admin\RegisterController@showRegisterForm')->name('register');
    Route::post('register', 'Admin\RegisterController@register')->name('register.submit');
    Route::post('logout', 'Admin\LoginController@logout')->name('logout');
    Route::get('/', 'Admin\HomeController@index');
    Route::get('/home', 'Admin\HomeController@index')->name('home');
    
    Route::get('list_admin', 'Admin\AdminController@list_admin');
    Route::get('edit_admin', 'Admin\AdminController@edit_admin');
    Route::post('edit_admin', 'Admin\AdminController@edit_admin');
    Route::get('admin_editfinish', 'Admin\AdminController@admin_editfinish');

});

Route::prefix('worker_admin')->name('worker_admin.')->group(function() {
    Route::get('login', 'WorkerAdmin\LoginController@showLoginForm')->name('login');
    Route::post('login', 'WorkerAdmin\LoginController@login')->name('login.submit');
    Route::get('register', 'WorkerAdmin\RegisterController@showRegisterForm')->name('register');
    Route::post('register', 'WorkerAdmin\RegisterController@register')->name('register.submit');
    Route::post('logout', 'WorkerAdmin\LoginController@logout')->name('logout');

    Route::get('/', 'WorkerAdmin\HomeController@index');
    Route::get('/home', 'WorkerAdmin\HomeController@index')->name('home');
});

Route::prefix('worker')->name('worker.')->group(function() {
    Route::get('login', 'Worker\LoginController@showLoginForm')->name('login');
    Route::post('login', 'Worker\LoginController@login')->name('login.submit');
    Route::get('register', 'Worker\RegisterController@showRegisterForm')->name('register');
    Route::post('register', 'Worker\RegisterController@register')->name('register.submit');
    Route::post('logout', 'Worker\LoginController@logout')->name('logout');

    Route::get('/', 'Worker\HomeController@index');
    Route::get('/home', 'Worker\HomeController@index')->name('home');
});
