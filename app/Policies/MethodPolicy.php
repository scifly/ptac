<?php

namespace App\Policies;

use App\Helpers\Constant;
use App\Models\Action;
use App\Models\ActionGroup;
use App\Models\Corp;
use App\Models\School;
use App\Models\User;
use App\Models\WapSite;
use Illuminate\Auth\Access\HandlesAuthorization;

class MethodPolicy {
    
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() { }

    public function act(User $user, Route $route) {
    
    
        $role = $user->group->name;
        if (!in_array($role, Constant::SUPER_ROLES)) {
            $actionGroup = ActionGroup::whereGroupId($user->group_id)
                ->where('action_id', Action::whereRoute($route->uri)->first()->id)
                ->first();
            return $actionGroup ? true : false;
        }
    
        if ($role == '企业' && stripos($route->uri, 'corps') > -1) {
            $corpId = Corp::whereDepartmentId($user->topDeptId())->first()->id;
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
            $schoolId = School::whereDepartmentId($user->topDeptId())->first()->id;
            $wapsiteId = WapSite::whereSchoolId($schoolId)->first()->id;
            $allowedActions = array_merge(
                $this->allowedActions(Constant::ALLOWED_SCHOOL_ACTIONS, $schoolId),
                $this->allowedActions(Constant::ALLOWED_WAPSITE_ACTIONS, $wapsiteId)
            );
            return in_array($route, $allowedActions);
        }
        
        return true;

    }
    
    private function allowedActions(array $actions, $id) {
        
        return array_map(
            function($str) use($id) {
                return sprintf($str, $id);
            },
            $actions
        );
        
    }

}
