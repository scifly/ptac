<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Datatable
 * @package App\Facades
 */
class Datatable extends Facade {
    
    /**
     * @return string
     */
    protected static function getFacadeAccessor() { return 'datatable'; }
    
}