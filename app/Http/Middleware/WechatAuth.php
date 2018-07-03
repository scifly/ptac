<?php
namespace App\Http\Middleware;

use Closure;
use App\Models\App;
use App\Models\User;
use App\Models\Corp;
use App\Facades\Wechat;
use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Class WechatAuth
 * @package App\Http\Middleware
 */
class WechatAuth {
    
    /**
     * Handle an incoming request.
     *
     * @param  Request $request
     * @param  Closure $next
     * @return mixed
     * @throws Exception
     */
    public function handle($request, Closure $next) {
    
        $paths = explode('/', Request::path());
        # 登录先
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
        }
        # 学校列表。如果用户仅可见到一所学校，则直接进入需要访问的页面。
        $user = Auth::user();
        abort_if(
            $user->group->name == '学生',
            HttpStatusCode::UNAUTHORIZED,
            __('messages.unauthorized')
        );
        if (!Request::query('schoolId')) {
            if (!session('schoolId')) {
                $schoolIds = $user->schoolIds($user->id, session('corpId'));
                if (count($schoolIds) > 1) {
                    return redirect($paths[0] . '/schools?app=' . $paths[1]);
                }
                session(['schoolId' => $schoolIds[0]]);
            }
        } else {
            session(['schoolId' => Request::query('schoolId')]);
        }
        
        return $next($request);
        
    }
    
}
