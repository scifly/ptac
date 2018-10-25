<?php

namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\PolicyTrait;
use App\Models\Corp;
use App\Models\Menu;
use App\Models\School;
use App\Models\User;
use App\Models\WapSite;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class MethodPolicy
 * @package App\Policies
 */
class MethodPolicy {
    
    use HandlesAuthorization, PolicyTrait;
    
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
     * @param User $user
     * @param Route $route
     * @return bool
     */
    public function act(User $user, Route $route) {
    
        $role = $user->role();
        if (!in_array($role, Constant::SUPER_ROLES)) {
            return $this->action($user);
        }

        $rootMenuId = $this->menu->rootMenuId();
        
        if ($role == '企业' && stripos($route->uri, 'corps') > -1) {
            $corpId = Corp::whereMenuId($rootMenuId)->first()->id;
            $allowedActions = $this->allowedActions(
                Constant::ALLOWED_CORP_ACTIONS, $corpId
            );
            return in_array($route, $allowedActions);
        }
        
        if (
            $role == '学校' &&
            (stripos($route->uri, 'schools') > -1 ||
            stripos($route->uri, 'wap_sites') > -1)
        ) {
            $schoolId = School::whereMenuId($rootMenuId)->first()->id;
            $wapsiteId = WapSite::whereSchoolId($schoolId)->first()->id;
            $allowedActions = array_merge(
                $this->allowedActions(Constant::ALLOWED_SCHOOL_ACTIONS, $schoolId),
                $this->allowedActions(Constant::ALLOWED_WAPSITE_ACTIONS, $wapsiteId)
            );
            return in_array($route, $allowedActions);
        }
        
        return true;

    }
    
    /**
     * @param array $actions
     * @param $id
     * @return array
     */
    private function allowedActions(array $actions, $id) {
        
        return array_map(
            function($str) use($id) {
                return sprintf($str, $id);
            },
            $actions
        );
        
    }

}
