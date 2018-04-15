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
    
    public function operation(User $user, SchoolType $st = null, $abort = false) {
        
        abort_if(
            $abort && !$st,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return $user->group->name == '运营';
        
    }
    
}
