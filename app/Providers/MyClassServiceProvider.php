<?php
use Illuminate\Support\ServiceProvider;

class MyClassServiceProvider extends ServiceProvider {
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->app->bind('MyClassAlias', function(){
            return new App\Models\MyClass;
        });
    }
}