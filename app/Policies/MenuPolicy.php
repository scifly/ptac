<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Models\Corp;
use App\Models\Menu;
use App\Models\School;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MenuPolicy {
    
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
    
        return in_array($user->group->name, Constant::SUPER_ROLES);
        
    }
    
    public function eudmr(User $user, Menu $menu) {
        
        abort_if(
            !$menu,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        switch ($user->group->name) {
            case '运营': return true;
            case '企业':
                $corp = Corp::whereDepartmentId($user->topDeptId())->first();
                $allowedMenuIds = $menu->subMenuIds($corp->menu_id);
                return in_array($menu->id, $allowedMenuIds);
            case '学校':
                $school = School::whereDepartmentId($user->topDeptId())->first();
                $allowedMenuIds = $menu->subMenuIds($school->menu_id);
                return in_array($menu->id, $allowedMenuIds);
            default: return false;
        }
        
    }
    
    
}
