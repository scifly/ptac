<?php

namespace App\Http\Middleware;

use App\Models\Action;
use App\Models\ActionGroup;
use App\Models\Group;
use App\Models\User;
use Closure;

class CheckRole {
    
    protected $group, $action, $actionGroup;
    
    function __construct(Group $group, Action $action, ActionGroup $actionGroup) {
        
        $this->group = $group;
        $this->action = $action;
        $this->actionGroup = $actionGroup;
        
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        
        /** @var User $user */
        $user = $request->user();
        $user->group_id;
        return $next($request);
        
    }
    
}
