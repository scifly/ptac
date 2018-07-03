<?php
namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Models\MenuType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class MenuTypePolicy
 * @package App\Policies
 */
class MenuTypePolicy {
    
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
     * @param MenuType|null $mt
     * @param bool $abort
     * @return bool
     */
    public function operation(User $user, MenuType $mt = null, $abort = false) {
        
        abort_if(
            $abort && !$mt,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return $user->group->name == '运营';
        
    }
    
}
