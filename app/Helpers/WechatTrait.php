<?php
namespace App\Helpers;

use App\Models\App;
use App\Models\Corp;
use App\Models\User;
use App\Facades\Wechat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

trait WechatTrait {

    function signin($appName) {
        
        $acronym = explode('/', Request::path())[0];
        $corp = Corp::whereAcronym($acronym)->first();
        $app = App::whereCorpId($corp->id)->where('name', $appName)->first();
        $agentid = $app->agentid;
        $secret = $app->secret;
        
        $code = Request::input('code');
        if (!$code) {
            return redirect(
                Wechat::getCodeUrl($corp->corpid, $agentid, Request::url())
            );
        }
        $token = Wechat::getAccessToken($corp->corpid, $secret);
        if ($token['errcode']) {
            abort(
                HttpStatusCode::INTERNAL_SERVER_ERROR,
                $token['errmsg']
            );
        }
        $result = json_decode(
            Wechat::getUserInfo($token['access_token'], $code),
            JSON_UNESCAPED_UNICODE
        );
        abort_if(
            $result['errcode'],
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            Wechat::ERRMSGS[$result['errcode']]
        );
        $user = User::whereEnabled(1)->where('userid', $result['UserId'])->first();
        abort_if(
            !$user,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        session(['corpId' => $corp->id]);
        return Auth::loginUsingId($user->id);
        
    }
    
}