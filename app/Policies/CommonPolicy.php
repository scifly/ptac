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
     * @return bool
     */
    function operation(User $user, Model $model = null) {
        
        $perm = true;
        [$schoolId, $userId] = array_map(
            function ($field) use ($model) {
                return $this->field($field, $model);
            }, ['school_id', 'user_id']
        );
        !$schoolId ?: $perm &= $schoolId == $this->schoolId();
        !$userId ?: $perm &= $userId == Auth::id();
    
        return $this->action($user) && $perm;
        
    }
    
}
