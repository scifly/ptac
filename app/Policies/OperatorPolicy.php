<?php
namespace App\Policies;

use App\Helpers\{Constant, ModelTrait};
use App\Models\{Group, User};
use Exception;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

/**
 * Class OperatorPolicy
 * @package App\Policies
 */
class OperatorPolicy {
    
    use HandlesAuthorization, ModelTrait;
    
    /**
     * @param User $user
     * @param User|null $operator
     * @return bool
     * @throws Exception
     */
    function operation(User $user, User $operator = null) {
    
        $perm = true;
        $role = $user->role();
        if (!$ids = Request::input('ids')) {
            if (!$groupId = Request::input('user')['group_id']) {
                $groupId = $operator ? $operator->group_id : null;
            }
            if ($groupId) {
                if ($role == '运营') {
                    $roles = Constant::SUPER_ROLES;
                } elseif ($role == '企业') {
                    $roles = ['企业', '学校'];
                } else {
                    $roles = ['学校'];
                }
                $perm &= in_array(Group::find($groupId)->name, $roles);
            }
            $schoolId = Request::input('school_id');
            !$schoolId ?: $perm &= in_array($schoolId, $this->schoolIds());
        } else {
            $perm &= collect(explode(',', $this->visibleUserIds()))->has($ids);
        }
        
        return in_array($role, Constant::SUPER_ROLES) && $perm;
        
    }
    
}
