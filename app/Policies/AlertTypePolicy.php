<?php
namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Models\AlertType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class AlertTypePolicy
 * @package App\Policies
 */
class AlertTypePolicy {
    
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
     * 判断权限
     *
     * @param User $user
     * @param AlertType|null $at
     * @param bool $abort
     * @return bool
     */
    function operation(User $user, AlertType $at = null, $abort = false) {
        
        abort_if(
            $abort && !$at,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return $user->role() == '运营';
        
    }
    
}
