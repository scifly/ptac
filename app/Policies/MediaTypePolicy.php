<?php
namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Models\MediaType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class MediaTypePolicy
 * @package App\Policies
 */
class MediaTypePolicy {
    
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
     * @param MediaType|null $mt
     * @param bool $abort
     * @return bool
     */
    public function operation(User $user, MediaType $mt = null, $abort = false) {
        
        abort_if(
            $abort && !$mt,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return $user->group->name == '运营';
        
    }
    
}
