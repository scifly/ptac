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
        
        $code = Request::input('code');
        if (!$code) {
            return redirect(
                Wechat::getCodeUrl($corp->corpid, $agentid, Request::url())
            );
        } else {
            $accessToken = Wechat::getAccessToken($corp->corpid, $secret);
            $result = json_decode(
                Wechat::getUserInfo($accessToken, $code),
                JSON_UNESCAPED_UNICODE
            );
            Log::debug(json_encode($result));
            abort_if(
                $result['errcode'] != 0,
                HttpStatusCode::INTERNAL_SERVER_ERROR,
                __('messages.internal_server_error')
            );
            $user = User::whereUserid($result['UserId'])->first();
            Log::debug('userId: ' . ($user ? 'yes' : 'no'));
            abort_if(
                !$user,
                HttpStatusCode::NOT_FOUND,
                __('messages.not_found')
            );
            
            return Auth::loginUsingId($user->id);
        }
        
    }
    
}