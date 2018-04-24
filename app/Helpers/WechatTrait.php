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
    
        $acronym = explode('/', Request::path())[0];
        $corp = Corp::whereAcronym($acronym)->first();
        $app = App::whereCorpId($corp->id)->where('name', $app)->first();
        $agentid = $app->agentid;
        $secret = $app->secret;
        
        Log::debug('corpid: ' . $corp->corpid);
        Log::debug('appsecret: ' . $app->secret);
        Log::debug('wtf: ' . json_encode(Request::all()));
        $code = Request::input('code');
        Log::debug('urlcode: ' . Wechat::getCodeUrl($corp->corpid, $agentid, Request::url()));
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