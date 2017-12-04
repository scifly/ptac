<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;

class RedirectIfAuthenticated {
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null) {
        
        if (Auth::guard($guard)->check()) {
            Log::debug($request->fullUrl());
            Log::debug('tabId: ' . session('tabId'));
            Log::debug('menuId: ' . session('menuId'));
            return redirect()->intended('/home');
        }
        return $next($request);
        
    }
}
