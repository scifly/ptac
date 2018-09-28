<?php
namespace App\Helpers;

use App\Models\App;
use App\Models\Corp;
use App\Models\User;
use App\Facades\Wechat;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

/**
 * Trait WechatTrait
 * @package App\Helpers
 */
trait WechatTrait {
    
    protected $apps = [
        'sc' => '成绩中心',
        'mc' => '消息中心',
    ];
    
    /**
     * 登录
     *
     * @param $returnUrl
     * @return bool|RedirectResponse|Redirector
     * @throws Exception
     */
    function signin($returnUrl) {
        
        $paths = explode('/', Request::path());
        $acronym = $paths[0];
        $corp = Corp::whereAcronym($acronym)->first();
        $app = App::whereCorpId($corp->id)->where('name', Constant::APPS[$paths[1]])->first();
        $agentid = $app->agentid;
        $secret = $app->secret;
        $code = Request::input('code');
        if (!$code) {
            return redirect(
                Wechat::getCodeUrl($corp->corpid, $agentid, Request::url())
            );
        }
        $token = Wechat::getAccessToken($corp->corpid, $secret);
        Log::debug(json_encode($token));
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
            Constant::WXERR[$result['errcode']]
        );
        $user = User::whereEnabled(1)->where('userid', $result['UserId'])->first();
        abort_if(
            !$user,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        session([
            'corpId' => $corp->id,
            'acronym' => $acronym
        ]);
        
        Auth::loginUsingId($user->id);
        
        return redirect($returnUrl);
        
    }
    
}