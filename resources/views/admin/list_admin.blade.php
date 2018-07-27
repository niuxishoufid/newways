@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header bold" style="font-size:25px;">管理者一覧</div>
    <div class="card-body">
        <p><a href="/admin/register">新規管理者アカウントの作成</a></p>

        <p>「編集」をクリックすると、管理者情報を閲覧・編集できます。</p>
        <p style="font-size: 15px; color:#C92629;">{{ $msg }}</p>
        <form method="get" action="/admin/list_admin">
            {{ csrf_field() }}
            <input type="text" name="name" placeholder="{{ __('messages.名前') }}" value="{{ $name }}">
            <input name="action" id="submit_button" type="submit" value="{{ __('messages.検索') }}">
        </form>            
        <table id="account_list" class="table table-striped">
            <thead>
                <tr>
                    <th class="row1"></th>
                    <th class="row1"></th>
                    <th class="row2">{{ __('messages.名前') }}</th>
                    <th class="row3">{{ __('messages.メール') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($admin_list as $admin)
                <tr>
                    <td><a href="/admin/del_admin?admin_id={{$admin->id}}">{{ __('messages.削除') }}</a></td>
                    <td><a href="/admin/edit_admin?admin_id={{$admin->id}}">{{ __('messages.編集') }}</a></td>
                    <td>{{$admin->name}}</td>
                    <td>{{$admin->email}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{$admin_list->appends(Request::except('page'))->links()}}
    </div>
    <p><a href="#" onclick="window.history.back(); return false;" style="font-size: 15px;">直前のページに戻る</a></p>
</div>
@endsection
