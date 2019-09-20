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
        if ($schoolId = $this->field('school_id', $model)) {
            $perm &= $schoolId == $this->schoolId();
        }
        if (isset($model->{'user_id'})) {
            $perm &= ($model->{'user_id'} == Auth::id());
        }
    
        return $this->action($user) && $perm;
        
    }
    
}
