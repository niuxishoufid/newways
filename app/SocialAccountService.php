<?php

namespace App;

use Laravel\Socialite\Contracts\User as ProviderUser;

//このクラスの役割は、
//ローカルuserと関連するSNSアカウントを作成または取得することだけで、メソッドは1つだけです。
class SocialAccountService {

    //現在のプロバイダーIDに関連したSNSアカウントが登録されているか確認するクエリを
    //linked_social_accountsテーブルに発行し、登録されていて、
    //SNSアカウントを含むローカルuserオブジェクトを返します
    public function findOrCreate(ProviderUser $providerUser, $provider) {
        $account = LinkedSocialAccount::where('provider_name', $provider)
                ->where('provider_id', $providerUser->getId())
                ->first();

        if ($account) {
            return $account->user;
        } else {
            $user = User::where('email', $providerUser->getEmail())->first();
            if (!$user) {
                $user = User::create([
                            'email' => $providerUser->getEmail(),
                            'name' => $providerUser->getName(),
                ]);
            }
            $user->accounts()->create([
                'provider_id' => $providerUser->getId(),
                'provider_name' => $provider,
            ]);
            return $user;
        }
    }

}
