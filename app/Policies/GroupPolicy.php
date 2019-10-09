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
    
        [$menuIds, $tabIds, $actionIds, $ids] = array_map(
            function ($key) { return explode(',', Request::input($key)); },
            ['menu_ids', 'tab_ids', 'action_ids', 'ids']
        );
        if (isset($menuIds, $tabIds, $actionIds)) {
            $schoolId = Request::input('school_id');
            $sGId = Group::whereName('学校')->first()->id;
            $aGroupIds = Group::whereSchoolId($schoolId)->pluck('id');
            $aMenuIds = (new Menu)->subIds(School::find($schoolId)->menu_id);
            $aTabIds = Tab::whereIn('group_id', [0, $sGId])->pluck('id');
            $dTabIds = Tab::whereIn('name', ['部门', '角色', '菜单', '超级用户'])->pluck('id');
            $aActionIds = Action::whereIn('tab_id', $aTabIds->diff($dTabIds))->pluck('id');
            $perm = $aGroupIds->flip()->has($group->id) && $aMenuIds->flip()->has($menuIds)
                && $aTabIds->flip()->has($tabIds) && $aActionIds->flip()->has($actionIds);
        }
        if ($ids) {
            $role = $user->role();
            if ($role == '运营') {
                $perm = true;
            } elseif (in_array($role, ['企业', '学校'])) {
                $perm = Group::whereIn('school_id', $this->schoolIds())
                    ->pluck('id')->flip()->has($ids);
            } else {
                $perm = false;
            }
        }
        
        return $this->action($user) && ($perm ?? true);
        
    }
    
}
