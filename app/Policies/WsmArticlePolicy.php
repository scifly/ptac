<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\User;
use App\Models\WsmArticle;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

/**
 * Class WsmArticlePolicy
 * @package App\Policies
 */
class WsmArticlePolicy {
    
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
     * @param WsmArticle|null $wsma
     * @param bool $abort
     * @return bool
     */
    function operation(User $user, WsmArticle $wsma = null, $abort = false) {
        
        abort_if(
            $abort && !$wsma,
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
                return $isSuperRole && in_array($wsma->wapsitemodule->wapsite->school_id, $this->schoolIds());
            default:
                return false;
        }
        
    }
    
}
