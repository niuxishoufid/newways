<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinkedSocialAccountsTable extends Migration {

    /**
     * Run the migrations.
     * ユーザーが選択したSNSアカウントへのリンクを保存する
     * php artisan make:model LinkedSocialAccount --migration
     * @return void
     */
    public function up() {
        Schema::create('linked_social_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('user_id');
            //プロバイダー名
            $table->string('provider_name')->nullable();
            //そのプロバイダーに登録されているユーザーのID
            $table->string('provider_id')->unique()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('linked_social_accounts');
    }

}
