<?php
namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Models\Tab;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class TabPolicy
 * @package App\Policies
 */
class TabPolicy {
    
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
     * @param User $user
     * @param Tab|null $tab
     * @param bool $abort
     * @return bool
     */
    public function operation(User $user, Tab $tab = null, $abort = false) {
        
        abort_if(
            $abort && !$tab,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return $user->group->name == '运营';
        
    }
    
}
