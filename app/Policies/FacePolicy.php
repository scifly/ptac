<?php
namespace App\Policies;

use App\Helpers\{Constant, HttpStatusCode, ModelTrait, PolicyTrait};
use App\Models\{Face, User};
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

/**
 * Class FacePolicy
 * @package App\Policies
 */
class FacePolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() { }
    
    /**
     * 权限判断
     *
     * @param User $user
     * @param Face $face
     * @param bool $abort
     * @return bool
     */
    function operation(User $user, Face $face = null, $abort = false) {
        
        abort_if(
            $abort && !$face,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $role = $user->role();
        if ($role == '运营') { return true; }
        $isSuperRole = in_array($role, Constant::SUPER_ROLES);
        $action = explode('/', Request::path())[1];
        
        return $isSuperRole ? true : $this->action($action);
        
    }
    
}