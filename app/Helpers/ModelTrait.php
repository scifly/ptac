<?php
namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use ReflectionClass;

trait ModelTrait {
    
    public function removable(Model $model, $id) {
        
        $relations = [];
        $class = get_class($model);
        $instance = $model->find($id);
        if (!$instance) { return false; }
        $reflectionClass = new ReflectionClass($class);
        
        foreach ($reflectionClass->getMethods() as $method) {
            if ($method->isUserDefined() && $method->isPublic() && $method->class == $class) {
                $doc = $method->getDocComment();
                if ($doc && stripos($doc, 'Relations\Has') !== false) {
                    $relations[] = $method->getName();
                }
            }
        }
        dd($relations);
        foreach ($relations as $relation) {
            if ($instance->{$relation}) { return false; }
        }
        return true;
        
    }
    
}

