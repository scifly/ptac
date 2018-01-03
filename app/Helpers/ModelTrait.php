<?php
namespace App\Helpers;

use App\Models\Action;
use App\Policies\Route;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use ReflectionClass;

trait ModelTrait {
    
    static function removable(Model $model) {
        
        $relations = [];
        $class = get_class($model);
        $reflectionClass = new ReflectionClass($class);
        foreach ($reflectionClass->getMethods() as $method) {
            if ($method->isUserDefined() && $method->isPublic() && $method->class == $class) {
                $doc = $method->getDocComment();
                if ($doc && stripos($doc, 'Relations\Has') !== false) {
                    $relations[] = $method->getName();
                }
            }
        }
        foreach ($relations as $relation) {
            // if ($model->{$relation}) {
            //     return false;
            // }
            if(count($model->{$relation})){
                return false;
            }
        }
        return true;
        
    }

    /**
     * 获取当前控制器包含的方法所对应的路由对象数组
     *
     * @return array
     */
    static function uris() {

        $controller = class_basename(Request::route()->controller);
        $routes = Action::whereController(class_basename($controller))
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

