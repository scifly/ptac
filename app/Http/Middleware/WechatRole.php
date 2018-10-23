<?php
namespace App\Http\Middleware;

use App\Models\Corp;
use Closure;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Class WechatAuth
 * @package App\Http\Middleware
 */
class WechatRole {
    
    /**
     * Handle an incoming request.
     *
     * @param  Request $request
     * @param  Closure $next
     * @return mixed
     * @throws Exception
     */
    public function handle($request, Closure $next) {
        
        $user = Auth::user();
        if (
            $user->custodian && $user->educator &&
            $user->educator->school_id == session('schoolId')
        ) {
            session(['roles' => true]);
            return redirect(Corp::find(session('corpId'))->acronym . '/roles');
        }
    
        return $next($request);
        
    }
    
}
