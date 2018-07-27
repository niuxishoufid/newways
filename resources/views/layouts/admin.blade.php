<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ trans('messages.back_manage_title') }}</title>

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>

        <!-- Fonts -->
        <link rel="dns-prefetch" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

        <!-- Styles -->
        <style>
            @font-face {
                font-family: "Yu Gothic";
                src: local("Yu Gothic Medium");
                font-weight: 400;
            }
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: "Yu Gothic", "游ゴシック", YuGothic, "游ゴシック体", "ヒラギノ角ゴ Pro W3", "メイリオ", sans-serif;
                font-weight: 100;
                margin: 0;
            }
            a:hover{
                opacity:0.7;
            }
            .content {
                text-align: center;
            }
            .title {
                font-size: 84px;
            }
            .sidebar{
                border:1px solid #DDD;
                margin-right:1%;
            }
            .m-b-md {
                margin-bottom: 30px;
            }
            nav{
                font-size: 14px;
            }
            .nav-title a{
                font-weight:800;
                padding-left:4px !important;
            }
            .nav-item a{
                padding-left:12px;
            }
            .card{
                font-size:13px;
            }
            .card-body{
                font-size:13px;
            }            
            .row1{
                width:10%;
            }
            .row2{
                width:45%;
            }
            .row3{
                width:45%;
            }
            .flex-center{
                height:60px;
            }
            .top-right{
                float: right;
                margin:20px 5% 0 0;
            }
            .top-right a{
                vertical-align: middle;
                color: #888;
                font-size:14px;
            }
            .m-b-md {
                margin-bottom: 30px;
            }
            .apply h3{
                color:#F4A643;
                padding:15px 0 15px 15px;
                border:2px solid #F4A643;
                margin-bottom:20px;
            }
            .apply p{
                font-size:14px;
                padding:10px 0 10px 15px;
            }
            .hissu{
                color:#EC070B;
            }
            .form-field{
                width:100%;
            }
            .form-group{
                padding:15px 0 30px;
            }
            .card-body{
                font-size: 13px;
            }
            .btn-success{
                width:60px;
                height:30px;
                font-size: 15px;
            }
            .alert{
                font-size: 13px;
            }
            .error{
                border-color: #EE1114;
            }            
        </style>    
    </head>
    <body>
        <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'NEWWAYS') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.login') }}">{{ __('Login') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.register') }}">{{ __('Register') }}</a>
                        </li>
                        @else
                        <!-- 言語切り替え -->
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Config::get('languages')[App::getLocale()] }} <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                @foreach (Config::get('languages') as $lang => $language)
                                @if ($lang != App::getLocale())
                                <li>
                                    <a href="{{ url('/lang/'.$lang) }}">{{$language}}</a>
                                </li>
                                @endif
                                @endforeach
                            </ul>
                        </li>

                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('admin.logout') }}"
                                   onclick="event.preventDefault();
    document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        <div class="row">
            <div class="col-sm-4 col-md-3 col-lg-2 hidden-xs-down bg-faded sidebar">
                <ul class="nav nav-pills flex-column">
                    <li class="nav-item nav-title">
                        <a class="nav-link">
                            採用関連
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/list_applicant" class="nav-link">
                            応募一覧
                        </a>
                    </li>
                </ul>
                <ul class="nav nav-pills flex-column">
                    <li class="nav-item nav-title">
                        <a href="/admin/home" class="nav-link">
                            ダッシュボード
                        </a>
                    </li>
                </ul>
                <ul class="nav nav-pills flex-column">
                    <li class="nav-item nav-title">
                        <a class="nav-link">
                            各種情報
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/edit_admin" class="nav-link">
                            管理者一覧・編集
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/list_stylistadmin" class="nav-link">
                            スタイリスト管理者一覧・編集
                        </a>
                    </li>              
                    <li class="nav-item">
                        <a href="/admin/list_worker" class="nav-link">
                            スタイリスト一覧・編集
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/list_user" class="nav-link">
                            ユーザー一覧・編集
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/list_order" class="nav-link">
                            依頼一覧
                        </a>
                    </li>
                </ul>
                <ul class="nav nav-pills flex-column">
                    <li class="nav-item nav-title">
                        <a class="nav-link">
                            ポイント関連
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/list_pointpay" class="nav-link">
                            ポイント購入者情報
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/view_info" class="nav-link">
                            ポイント・時間情報
                        </a>
                    </li>         
                </ul>
            </div>
            <div class="col-sm-7 col-md-8 col-lg-9">
                @yield('content')
            </div>
        </div>
    </body>
</html>
