<?php
use Illuminate\Support\Facades\Facade;

class MyClass extends Facade {
    
    protected static function getFacadeAccessor() {
        return 'MyClassAlias';
    } // most likely you want MyClass here
    
}