<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Wechat
 * @package App\Facades
 */
class Wechat extends Facade {
    
    /**
     * @return string
     */
    protected static function getFacadeAccessor() { return 'wechat'; }

}