<?php
namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Models\IconType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class IconTypePolicy
 * @package App\Policies
 */
class IconTypePolicy {
    
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
     * @param IconType|null $it
     * @param bool $abort
     * @return bool
     */
    public function operation(User $user, IconType $it = null, $abort = false) {
        
        abort_if(
            $abort && !$it,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return $user->group->name == '运营';
        
    }
    
}