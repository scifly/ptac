<?php
namespace App\Providers;

use App\Models\Action;
use App\Policies\Route;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {

        View::share('uris', $this->uris());
        
        /** greater_than */
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
        
        /** greater_than_or_equal_to */
        Validator::extend('greater_than_or_equal_to', function(
            /** @noinspection PhpUnusedParameterInspection */
            $attribute, $value, $params, $validator
        ) {
            $other = Input::get($params[0]);
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
    
    /**
     *
     *
     * @return array|null
     */
    private function uris() {
    
        if (!Request::route()) { return null; }
        $controller = class_basename(Request::route()->controller);
        $routes = Action::whereController($controller)
            ->where('route', '<>', null)
            ->pluck('route', 'method')
            ->toArray();
        $uris = [];
        foreach ($routes as $key => $value) {
            $uris[$key] = new Route($value);
        }
    
        return $uris;
    
    }
    
}
