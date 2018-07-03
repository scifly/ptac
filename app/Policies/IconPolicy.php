<?php
namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Models\Icon;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class IconPolicy
 * @package App\Policies
 */
class IconPolicy {
    
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
     * @param Icon|null $icon
     * @param bool $abort
     * @return bool
     */
    public function operation(User $user, Icon $icon = null, $abort = false) {
        
        abort_if(
            $abort && !$icon,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return $user->group->name == '运营';
        
    }
    
}
