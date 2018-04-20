<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class MenuPolicy {
    
    use HandlesAuthorization, ModelTrait;
    
    protected $menu;
    
    /**
     * Create a new policy instance.
     *
     * @param Menu $menu
     */
    public function __construct(Menu $menu) {
        
        $this->menu = $menu;
        
    }
    
    /**
     * 权限判断
     *
     * @param User $user
     * @param Menu $menu
     * @param bool $abort
     * @return bool
     */
    public function operation(User $user, Menu $menu = null, $abort = false) {
        
        abort_if(
            $abort && !$menu,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        if ($user->group->name == '运营') { return true; }
        $isSuperRole = in_array($user->group->name, Constant::SUPER_ROLES);
        $action = explode('/', Request::path())[1];
        if (in_array($action, ['index', 'create', 'store'])) {
            return $isSuperRole;
        }
        if (in_array($action, ['edit', 'update', 'delete', 'sort'])) {
            $isMenuAllowed = in_array($menu->id, $this->menuIds($this->menu));
            return $isSuperRole && $isMenuAllowed;
        }
        
        return false;
        
    }
    
}
