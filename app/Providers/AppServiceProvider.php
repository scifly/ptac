<?php
namespace App\Providers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

/**
 * Class AppServiceProvider
 * @package App\Providers
 */
class AppServiceProvider extends ServiceProvider {
    
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {

        /** greater_than */
        Validator::extend('greater_than', function(
            /** @noinspection PhpUnusedParameterInspection */
            $attribute, $value, $params, $validator
        ) {
            $other = Request::input($params[0]);
            return intval($value) > intval($other);
        });

        Validator::replacer('greater_than', function(
            /** @noinspection PhpUnusedParameterInspection */
            $message, $attribute, $rule, $params
        ) {
            return str_replace(
                ':other', __('validation.attributes.' . $params[0]),
                $message
            );
        });
        
        /** greater_than_or_equal_to */
        Validator::extend('greater_than_or_equal_to', function(
            /** @noinspection PhpUnusedParameterInspection */
            $attribute, $value, $params, $validator
        ) {
            $other = Request::input($params[0]);
            return intval($value) >= intval($other);
        });

        Validator::replacer('greater_than_or_equal_to', function(
            /** @noinspection PhpUnusedParameterInspection */
            $message, $attribute, $rule, $params
        ) {
            return str_replace(
                ':other', __('validation.attributes.' . $params[0]),
                $message
            );
        });
        
    }
    
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //
    }
    
}
