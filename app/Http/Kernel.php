<?php
namespace App\Http;

use App\Http\Middleware\{CheckRole,
    CorpAuth,
    CorpRole,
    EncryptCookies,
    RedirectIfAuthenticated,
    TrimStrings,
    VerifyCsrfToken};
use Illuminate\Auth\Middleware\{Authenticate, AuthenticateWithBasicAuth, Authorize};
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\{CheckForMaintenanceMode, ConvertEmptyStringsToNull, ValidatePostSize};
use Illuminate\Routing\Middleware\{SubstituteBindings, ThrottleRequests};
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Laravel\Passport\Http\Middleware\CreateFreshApiToken;

/**
 * Class Kernel
 * @package App\Http
 */
class Kernel extends HttpKernel {
    
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        CheckForMaintenanceMode::class,
        ValidatePostSize::class,
        TrimStrings::class,
        ConvertEmptyStringsToNull::class,
    ];
    
    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            CreateFreshApiToken::class,
        ],
        'api' => [
            'throttle:60,1',
            'bindings',
            // 'auth:api'
        ],
    ];
    
    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth'       => Authenticate::class,
        'auth.basic' => AuthenticateWithBasicAuth::class,
        'bindings'   => SubstituteBindings::class,
        'can'        => Authorize::class,
        'guest'      => RedirectIfAuthenticated::class,
        'throttle'   => ThrottleRequests::class,
        'checkrole'  => CheckRole::class,
        'corp.auth'  => CorpAuth::class,
        'corp.role'  => CorpRole::class,
    ];
}
