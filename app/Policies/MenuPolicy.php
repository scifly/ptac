<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MenuPolicy {
    
    use HandlesAuthorization, ModelTrait;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    /**
     * (c)reate, (s)tore, (s)ort
     *
     * @param User $user
     * @return bool
     */
    public function css(User $user) {
    
        return in_array(
            $user->group->name,
            Constant::SUPER_ROLES
        );
        
    }
    
    /**
     * (e)dit, (u)pdate, (d)estroy
     *
     * @param User $user
     * @param Menu $menu
     * @return bool
     */
    public function eudmr(User $user, Menu $menu) {
        
        abort_if(
            !$menu,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        switch ($user->group->name) {
            case '运营':
                return true;
            case '企业':
            case '学校':
                return in_array($menu->id, $this->menuIds());
            default:
                # 校级以下角色没有管理菜单的权限
                return false;
        }
        
    }
    
}
