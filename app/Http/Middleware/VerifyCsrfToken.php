<?php
namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

/**
 * Class VerifyCsrfToken
 * @package App\Http\Middleware
 */
class VerifyCsrfToken extends BaseVerifier {
    
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'wlrj/mc',
        'wlrj/ws',
        'wlrj/at',
        'wlrj/sc',
        'wlrj/sync',
        'lkrj/mc',
        'lkrj/ws',
        'lkrj/at',
        'lkrj/sc',
        'lkrj/sync',
        'zhjyaqpt/mc',
        'zhjyaqpt/ws',
        'zhjyaqpt/at',
        'zhjyaqpt/sc',
        'zhjyaqpt/sync',
        'cdmsgjxx/mc',
        'cdmsgjxx/ws',
        'cdmsgjxx/at',
        'cdmsgjxx/sc',
        'cdmsgjxx/sync',
    ];
}

