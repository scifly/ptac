<?php
namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Models\DepartmentType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class DepartmentTypePolicy
 * @package App\Policies
 */
class DepartmentTypePolicy {
    
    use HandlesAuthorization;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    /**
     * 权限判断
     *
     * @param User $user
     * @param DepartmentType|null $dt
     * @param bool $abort
     * @return bool
     */
    function operation(User $user, DepartmentType $dt = null, $abort = false) {
        
        abort_if(
            $abort && !$dt,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return $user->group->name == '运营';
        
    }
    
}