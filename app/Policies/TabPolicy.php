<?php
namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Models\Tab;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

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
    
    
    public function operation(User $user, Tab $tab = null, $abort = false) {
        
        abort_if(
            $abort && !$tab,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return $user->group->name == '运营';
        
    }
    
}
