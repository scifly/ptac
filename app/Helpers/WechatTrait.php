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
        $corpid = Corp::whereAcronym($acronym)->first()->corpid;
        Log::debug('corpid: ' . $corpid);
        $app = App::whereCorpId($corpid)->where('name', $app)->first();
        $agentid = $app->agentid;
        $secret = $app->secret;
    
        $code = Request::input('code');
        if (!$code) {
            redirect(
                Wechat::getCodeUrl($corpid, $agentid, Request::url())
            );
        } else {
            $accessToken = Wechat::getAccessToken($corpid, $secret);
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