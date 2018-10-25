<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\User;
use App\Models\WapSiteModule;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

/**
 * Class WapSiteModulePolicy
 * @package App\Policies
 */
class WapSiteModulePolicy {
    
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
     * @param WapSiteModule $wsm
     * @param bool $abort
     * @return bool
     */
    function operation(User $user, WapSiteModule $wsm = null, $abort = false) {
        
        abort_if(
            $abort && !$wsm,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $role = $user->role();
        if ($role == '运营') { return true; }
        $isSuperRole = in_array($role, Constant::SUPER_ROLES);
        $action = explode('/', Request::path())[1];
        switch ($action) {
            case 'index':
            case 'create':
            case 'store':
                return $isSuperRole;
            case 'show':
            case 'edit':
            case 'update':
            case 'delete':
                return $isSuperRole && in_array($wsm->wapsite->school_id, $this->schoolIds());
            default:
                return false;
        }
        
    }
    
}
