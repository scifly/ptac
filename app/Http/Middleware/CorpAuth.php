<?php
namespace App\Http\Middleware;

use App\Facades\Wechat;
use App\Helpers\{Constant, ModelTrait};
use App\Models\{App, Corp, Openid, User};
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
            $acronym = $paths[0];
            $corp = Corp::whereAcronym($acronym)->first();
            $appId = $paths[2] ?? null;
            # 登录先
            if (!Auth::id()) {
                $app = $appId ? App::find($appId) : $this->corpApp($corp->id);
                $base = Request::route('id') ? 'sns' : 'ent';
                $id = $base == 'sns' ? $app->appid : $corp->corpid;
                # step 1: 获取code
                if (!$code = Request::input('code')) {
                    return redirect(
                        Wechat::code(
                            $id, Request::url(),
                            $base == 'sns' ? null : $app->appid
                        )
                    );
                }
                # step 2: 获取access_token
                $token = Wechat::token(
                    $base, $id, $app->secret,
                    $base == 'sns' ? $code : null
                );
                if ($appId) {
                    # 微信公众号
                    if (!$openid = Openid::whereOpenid($token['openid'])->first()) {
                        # 注册
                        return redirect(
                            $acronym . '/wechat/signup/' . $appId . '?openid=' . $openid
                        );
                    }
                    $user = $openid->user;
                } else {
                    # 企业微信
                    $result = json_decode(
                        Wechat::invoke(
                            'ent', 'user', 'getuserinfo', [$token, $code]
                        ), true
                    );
                    throw_if(
                        $result['errcode'],
                        new Exception(Constant::WXERR[$result['errcode']])
                    );
                    throw_if(
                        !$user = User::where([
                            'enabled' => 1, 'userid' => $result['UserId'],
                        ])->first(),
                        new Exception(__('messages.not_found'))
                    );
                }
                session([
                    'corpId'  => $corp->id,
                    'appId'   => $appId,
                    'acronym' => $acronym,
                ]);
                Auth::loginUsingId($user->id);
            }
            $schoolId = Request::query('schoolId');
            if (
                !$schoolId && !session('schoolId') &&
                stripos(Request::path(), 'schools') === false
            ) {
                $schoolIds = Auth::user()->schoolIds(
                    Auth::id(), session('corpId')
                );
                if (count($schoolIds) > 1) {
                    session(['schools' => true]);
                    
                    return redirect(
                        join([
                            $acronym . '/wechat/schools',
                            $appId ? '/' . $appId : '',
                            '?app=' . $paths[1],
                        ])
                    );
                }
                $schoolId = $schoolIds[0];
            }
            session(['schoolId' => $schoolId]);
        } catch (Exception $e) {
            throw $e;
        }
        
        return $next($request);
        
    }
    
}