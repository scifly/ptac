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

    function getUserid($app) {
        static $v = 0;
        
        $acronym = explode('/', Request::path())[0];
        $corp = Corp::whereAcronym($acronym)->first();
        $app = App::whereCorpId($corp->id)->where('name', $app)->first();
        $agentid = $app->agentid;
        $secret = $app->secret;
        
        $v += 1;
        Log::debug('visited: ' . $v);
        Log::debug('request no.' . $v . Request::url());
        $code = Request::input('code');
        if (!$code) {
            redirect(
                Wechat::getCodeUrl($corp->corpid, $agentid, Request::url())
            );
        } else {
            $accessToken = Wechat::getAccessToken($corp->corpid, $secret);
            $userInfo = json_decode(
                Wechat::getUserInfo($accessToken, $code),
                JSON_UNESCAPED_UNICODE
            );
            $user = User::whereUserid($userInfo['UserId'])->first();
            abort_if(!$user, HttpStatusCode::NOT_FOUND, __('messages.unauthorized'));
            Auth::loginUsingId($user->id);
        }
        
    }
    
}