<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\User;
use App\Models\WapSite;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

/**
 * Class WapSitePolicy
 * @package App\Policies
 */
class WapSitePolicy {
    
    use HandlesAuthorization, PolicyTrait, ModelTrait;
    
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
     * @param WapSite|null $ws
     * @param bool $abort
     * @return bool
     */
    function operation(User $user, WapSite $ws = null, $abort = false) {
        
        abort_if(
            $abort && !$ws,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $role = $user->role();
        if ($role == '运营') { return true; }
        $isSuperRole = in_array($role, Constant::SUPER_ROLES);
        $action = explode('/', Request::path())[1];
        switch ($action) {
            case 'index':
                return $isSuperRole;
            case 'edit':
            case 'update':
                return $isSuperRole && in_array($ws->school_id, $this->schoolIds());
            default:
                return false;
        }
        
    }
    
}
