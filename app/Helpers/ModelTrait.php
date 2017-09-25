<?php
namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use ReflectionClass;

trait ModelTrait {
    
    public function removable(Model $model) {
        
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
            if ($model->{$relation}) { return false; }
        }
        return true;
        
    }
    
}

