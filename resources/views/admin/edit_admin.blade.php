@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header bold" style="font-size:25px;">管理者の登録情報を編集</div>
    <div class="card-body">
        @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <form method="POST" action="/admin/edit_admin?admin_id={{ $admin->id }}" accept-charset="UTF-8">
            {{ csrf_field() }}
            <div class="form-group">
                <div class="col-sm-3 col-md-3">
                    <div class="form-layout-title form-style-required">名前</div>
                </div>
                <div class="col-sm-4 col-md-4">
                    <input class="form-field{{ $errors->has('name') ? ' error' : '' }}" type="text" name="name" id="name" value="@if(old('name')=='') {{$admin->name}} @else {{old('name')}} @endif" />
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-3 col-md-3">
                    <div class="form-layout-title form-style-required">メールアドレス</div>
                </div>
                <div class="col-sm-9 col-md-9">
                    <input class="form-field{{ $errors->has('email') ? ' error' : '' }}" type="text"  name="email" id="email" value="@if(old('email')=='') {{$admin->email}} @else {{old('email')}} @endif" />
                </div>
            </div>
            <hr>
            <div class="form-group">
                <div class="col-sm-3 col-md-3">
                    <div class="form-layout-title form-style-required">パスワード</div>
                </div>
                <div class="col-sm-9 col-md-9">
                    <input class="form-field{{ $errors->has('password') ? ' error' : '' }}" type="password" name="password" id="password" />
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-3 col-md-3">
                    <div class="form-layout-title form-style-required">パスワード（確認用）</div>
                </div>
                <div class="col-sm-9 col-md-9">
                    <input class="form-field" type="password" name="password_confirmation" id="password_confirm" />
                </div>
            </div>
            <div class="form-group center">
                <center><input name="action" id="submit_button" class="btn btn-success" type="submit" value="更新"></center>
            </div>
        </form>
    </div>
    <p><a href="#" onclick="window.history.back(); return false;" style="font-size: 15px;">直前のページに戻る</a></p>
</div>
@endsection
