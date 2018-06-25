<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class GeneralClass extends Facade {
    
    protected static function getFacadeAccessor() { return 'generalclass'; }
    
}