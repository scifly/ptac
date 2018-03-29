<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OperatorPolicy {
    
    use HandlesAuthorization;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    public function create(User $user) {
        
        return in_array($user->group->name, Constant::SUPER_ROLES);
        
    }
    
    public function edit(User $user, User $operator) {
    
        return $this->checkPerm($user, $operator);
        
    }
    
    public function destroy(User $user, User $operator) {
    
        return $this->checkPerm($user, $operator);
        
    }
    
    /**
     * 检查权限
     *
     * @param User $user
     * @param User $operator
     * @return bool
     */
    private function checkPerm(User $user, User $operator): bool {
        
        abort_if(
            !$operator,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        if (in_array($user->group->name, Constant::SUPER_ROLES)) {
            switch ($user->group->name) {
                case '运营':
                    return true;
                case '企业':
                    return $operator->group->name != '运营';
                case '学校':
                    return $operator->group->name == '学校';
                default:
                    break;
            }
        }
        
        return false;
        
    }
    
}
