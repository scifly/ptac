<?php
namespace App\Http\Middleware;

use App\Facades\Wechat;
use App\Helpers\{Constant, HttpStatusCode, ModelTrait};
use App\Models\{Corp, User};
use Closure;
use Exception;
use Illuminate\Support\Facades\{Auth, Request};

/**
 * Class CorpAuth
 * @package App\Http\Middleware
 */
class CorpAuth {
    
    use ModelTrait;
    
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
            $app = $this->app($corp->id);
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
        # 学校列表。如果用户仅隶属于一所学校，则直接进入需要访问的页面。
        $user = Auth::user();
        abort_if(
            $user->role() == '学生',
            HttpStatusCode::UNAUTHORIZED,
            __('messages.unauthorized')
        );
        if (
            !Request::query('schoolId') &&
            stripos(Request::path(), 'schools') === false
        ) {
            if (!session('schoolId')) {
                $schoolIds = $user->schoolIds($user->id, session('corpId'));
                if (count($schoolIds) > 1) {
                    session(['schools' => true]);
                    return redirect($paths[0] . '/wechat/schools?app=' . $paths[1]);
                }
                session(['schoolId' => $schoolIds[0]]);
            }
        } else {
            session(['schoolId' => Request::query('schoolId')]);
        }
        
        return $next($request);
        
    }
    
}