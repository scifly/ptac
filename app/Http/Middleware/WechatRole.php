<?php
namespace App\Http\Middleware;

use App\Models\Corp;
use App\Models\Group;
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
            if (!Request::query('is_educator')) {
                if (!session('is_educator')) {
                    $acronym = Corp::find(session('corpId'))->acronym;
                    return redirect($acronym . '/roles');
                }
            } else {
                session(['is_educator' => Request::query('is_educator')]);
            }
        }
        
        return $next($request);
        
    }
    
}
