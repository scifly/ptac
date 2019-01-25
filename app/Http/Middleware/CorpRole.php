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
        if ($user->custodian) {
            foreach ($user->custodian->students as $student) {
                $schoolIds[] = $student->squad->grade->school_id;
            }
        }
        if (
            $user->custodian && $user->educator &&
            $user->educator->school_id == session('schoolId') &&
            in_array(session('schoolId'), array_unique($schoolIds ?? []))
        ) {
            if (
                !Request::query('part') &&
                stripos(Request::path(), 'roles') === false
            ) {
                if (!session('part')) {
                    $acronym = Corp::find(session('corpId'))->acronym;
                    return redirect($acronym . '/wechat/roles');
                }
            } else {
                session(['part' => Request::query('part')]);
            }
        }
        
        return $next($request);
        
    }
    
}
