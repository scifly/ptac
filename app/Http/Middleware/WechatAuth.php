<?php
namespace App\Http\Middleware;

use App\Facades\Wechat;
use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Models\App;
use App\Models\Corp;
use App\Models\School;
use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class WechatAuth {
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
    
        $paths = explode('/', Request::path());
        if (!Auth::id()) {
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
            session([
                'corpId' => $corp->id,
                'acronym' => $acronym
            ]);
            Auth::loginUsingId($user->id);
        }
        
        if (!Request::query('schoolId')) {
            if (!session('schoolId')) {
                $user = Auth::user();
                $schoolIds = $user->schoolIds($user->id, session('corpId'));
                if (count($schoolIds) > 1) {
                    return redirect('schools?app=' . $paths[1]);
                }
                session(['schoolId' => $schoolIds[0]]);
            }
        } else {
            session(['schoolId' => Request::query('schoolId')]);
        }
        
        return $next($request);
        
    }
    
}
