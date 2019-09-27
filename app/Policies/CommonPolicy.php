<?php
namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Class CommonPolicy
 * @package App\Policies
 */
class CommonPolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * @param User $user
     * @param Model $model
     * @param string $class
     * @return bool
     */
    function operation(User $user, $model = null, string $class = null) {
        
        $perm = true;
        [$schoolId, $userId, $ids] = array_map(
            function ($field) use ($model) {
                return $this->field($field, $model);
            }, ['school_id', 'user_id', 'ids']
        );
        !$schoolId ?: $perm &= $schoolId == $this->schoolId();
        !$userId ?: $perm &= $userId == Auth::id();
        // !$ids ?: $perm &=
    
        return $this->action($user) && $perm;
        
    }
    
}
