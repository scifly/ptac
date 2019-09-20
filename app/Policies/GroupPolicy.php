<?php
namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\{Action, Group, Menu, School, Tab, User};
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

/**
 * Class GroupPolicy
 * @package App\Policies
 */
class GroupPolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * @param User $user
     * @param Group|null $group
     * @return bool
     */
    function operation(User $user, Group $group = null) {
        
        if ($group) {
            $schoolId = Request::input('school_id');
            $sGId = Group::whereName('学校')->first()->id;
            [$menuIds, $tabIds, $actionIds] = array_map(
                function ($key) { return explode(',', Request::input($key)); },
                ['menu_ids', 'tab_ids', 'action_ids']
            );
            $aGroupIds = Group::whereSchoolId($schoolId)->pluck('id');
            $aMenuIds = collect((new Menu)->subIds(School::find($schoolId)->menu_id));
            $aTabIds = Tab::whereIn('group_id', [0, $sGId])->pluck('id');
            $dTabIds = Tab::whereIn('name', ['部门', '角色', '菜单', '超级用户'])->pluck('id');
            $aActionIds = Action::whereIn('tab_id', $aTabIds->diff($dTabIds))->pluck('id');
            $perm = $aGroupIds->has($group->id) && $aMenuIds->has($menuIds)
                && $aTabIds->has($tabIds) && $aActionIds->has($actionIds);
        }
        
        return $this->action($user) && ($perm ?? true);
        
    }
    
}
