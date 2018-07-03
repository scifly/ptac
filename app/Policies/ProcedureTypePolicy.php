<?php
namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Models\ProcedureType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class ProcedureTypePolicy
 * @package App\Policies
 */
class ProcedureTypePolicy {
    
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
     *
     *
     * @param User $user
     * @param ProcedureType|null $pt
     * @param bool $abort
     * @return bool
     */
    public function operation(User $user, ProcedureType $pt = null, $abort = false) {
        
        abort_if(
            $abort && !$pt,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return $user->group->name == '运营';
        
    }
    
}
