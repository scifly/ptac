<?php
namespace App\Providers;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {

        Validator::extend('greater_than', function(
            /** @noinspection PhpUnusedParameterInspection */
            $attribute, $value, $params, $validator
        ) {
            $other = Input::get($params[0]);
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
