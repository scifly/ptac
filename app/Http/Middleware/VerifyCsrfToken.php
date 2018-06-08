<?php
namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

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
        'zhjyaqpt/mc',
        'zhjyaqpt/ws',
        'zhjyaqpt/at',
        'zhjyaqpt/sc',
        'zhjyaqpt/sync',
    ];
}

