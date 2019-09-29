<?php
namespace App\Policies;

use App\Helpers\{Constant, ModelTrait, PolicyTrait};
use App\Models\User;
use Doctrine\Common\Inflector\Inflector;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;
use ReflectionException;

/**
 * Class CommonPolicy
 * @package App\Policies
 */
class CommonPolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * @param User $user
     * @param $model
     * @return bool
     * @throws ReflectionException
     */
    function operation(User $user, $model = null) {
        
        $perm = true;
        [$schoolId, $userId, $ids] = array_map(
            function ($field) use ($model) {
                return $this->field($field, $model);
            }, ['school_id', 'user_id', 'ids']
        );
        !$schoolId ?: $perm &= $schoolId == $this->schoolId();
        !$userId ?: $perm &= $userId == Auth::id();
        if ($ids) {
            $table = explode('/', Request::path())[0];
            $model = $this->model(
                Inflector::classify(Inflector::singularize($table))
            );
            $condition = ['school_id' => $this->schoolId()];
            if (
                Schema::hasColumn($table, 'user_id') &&
                !in_array($user->role(), Constant::SUPER_ROLES)
            ) {
                $condition['user_id'] = $user->id;
            }
            $perm &= $model->where($condition)->pluck('id')
                ->flip()->has(array_values($ids));
        }
    
        return $this->action($user) && $perm;
        
    }
    
}
