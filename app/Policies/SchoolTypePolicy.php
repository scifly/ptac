<?php
namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Models\SchoolType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SchoolTypePolicy {
    
    use HandlesAuthorization;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    public function cs(User $user) {
        
        return $user->group->name == '运营';
        
    }
    
    public function eud(User $user, SchoolType $st) {
        
        abort_if(
            !$st,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return $user->group->name == '运营';
        
    }
    
}
