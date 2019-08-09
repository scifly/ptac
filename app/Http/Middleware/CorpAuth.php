<?php
namespace App\Http\Middleware;

use App\Facades\Wechat;
use App\Helpers\{Constant, HttpStatusCode, ModelTrait};
use App\Models\{Corp, User};
use Closure;
use Exception;
use Illuminate\Support\Facades\{Auth, Request};
use Throwable;

/**
 * Class CorpAuth
 * @package App\Http\Middleware
 */
class CorpAuth {
    
    use ModelTrait;
    
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws Throwable
     */
    public function handle($request, Closure $next) {
    
        try {
            $paths = explode('/', Request::path());
            # 登录先
            if (!Auth::id()) {
                $acronym = $paths[0];
                $corp = Corp::whereAcronym($acronym)->first();
                $app = $this->app($corp->id);
                $agentid = $app->agentid;
                $secret = $app->secret;
                if (!$code = Request::input('code')) {
                    return redirect(
                        Wechat::code($corp->corpid, $agentid, Request::url())
                    );
                }
                $token = Wechat::token('ent', $corp->corpid, $secret);
                if ($token['errcode']) {
                    abort(
                        HttpStatusCode::INTERNAL_SERVER_ERROR,
                        $token['errmsg']
                    );
                }
                $result = json_decode(
                    Wechat::invoke(
                        'ent', 'user', 'getuserinfo',
                        [$token['access_token'], $code]
                    ),
                    JSON_UNESCAPED_UNICODE
                );
                throw_if(
                    $result['errcode'],
                    new Exception(Constant::WXERR[$result['errcode']])
                );
                throw_if(
                    !$user = User::where(['enabled' => 1, 'userid' => $result['UserId']])->first(),
                    new Exception(__('messages.not_found'))
                );
                session([
                    'corpId' => $corp->id,
                    'acronym' => $acronym
                ]);
                Auth::loginUsingId($user->id);
            }
            # 学校列表。如果用户仅隶属于一所学校，则直接进入需要访问的页面。
            $user = Auth::user();
            throw_if(
                $user->role() == '学生',
                new Exception(__('messages.unauthorized'))
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
        } catch (Exception $e) {
            throw $e;
        }
        
        return $next($request);
        
    }
    
}