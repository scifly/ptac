<?php
namespace App\Http\Middleware;

use App\Models\Corp;
use Closure;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Class CorpAuth
 * @package App\Http\Middleware
 */
class CorpRole {
    
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
        $schoolIds = [];
        if ($user->custodian) {
            foreach ($user->custodian->students as $student) {
                $schoolIds[] = $student->squad->grade->school_id;
            }
        }
        !$user->educator ?: $schoolIds[] = $user->educator->school_id;
        # 如果当前用户既是教职员工亦是监护人
        if ($user->custodian && $user->educator) {
            # 如果当前用户在当前学校既是监护人也是教职员工
            $isEducator = $user->educator->school_id == session('schoolId');
            $isCustodian = in_array(session('schoolId'), array_unique($schoolIds ?? []));
            if ($isEducator && $isCustodian) {
                if (!Request::query('part') && stripos(Request::path(), 'roles') === false) {
                    if (!session('part')) {
                        $acronym = Corp::find(session('corpId'))->acronym;
                        return redirect($acronym . '/wechat/roles');
                    }
                } else {
                    session(['part' => Request::query('part')]);
                }
            } else {
                session(['part' =>  $isEducator ? 'educator' : 'custodian']);
            }
        }
        
        return $next($request);
        
    }
    
}
