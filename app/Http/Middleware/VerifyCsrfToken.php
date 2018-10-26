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
        'wlrj/wechat',
        'wlrj/mobile_sites',
        'wlrj/attendances',
        'wlrj/message_centers',
        'wlrj/score_centers',
        'wlrj/home_works',
        'wlrj/sync',
        'wlrj/notify',
        
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

