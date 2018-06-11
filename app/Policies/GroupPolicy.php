<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Models\Action;
use App\Models\Group;
use App\Models\Menu;
use App\Models\School;
use App\Models\Tab;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class GroupPolicy {
    
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
     * @param Group|null $group
     * @param bool $abort
     * @return bool
     */
    function operation(User $user, Group $group = null, $abort = false) {
        
        abort_if(
            $abort && !$group,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        if ($user->group->name == '运营') { return true; }
        # 仅超级用户（运营/企业/学校）才能进行角色/权限管理
        if (!in_array($user->group->name, Constant::SUPER_ROLES)) { return false; }
        $action = explode('/', Request::path())[1];
        $isGroupAllowed = $isMenuAllowed = $isTabAllowed = $isActionAllowed = false;
        if (in_array($action, ['store', 'update'])) {
            $menuIds = explode(',', Request::input('menu_ids'));
            $tabIds = Request::input('tab_ids');
            $actionIds = Request::input('action_ids');
            $schoolId = Request::input('school_id');
            $allowedMenuIds = $this->menu->subMenuIds(School::find($schoolId)->menu_id);
            $isMenuAllowed = empty(array_diff($menuIds, $allowedMenuIds));
            $allowedTabIds = Tab::whereIn('group_id', [0, Group::whereName('学校')->first()->id])
                ->get()->pluck('id')->toArray();
            $isTabAllowed = empty(array_diff($tabIds, $allowedTabIds));
            $deniedTabIds = Tab::whereIn('name', ['部门', '角色', '菜单', '超级用户'])
                ->get()->pluck('id')->toArray();
            $allowedControllers = Tab::whereIn('id', array_diff($allowedTabIds, $deniedTabIds))
                ->get()->pluck('controller')->toArray();
            $allowedActionIds = Action::whereIn('controller', $allowedControllers)
                ->get()->pluck('id')->toArray();
            $isActionAllowed = empty(array_diff($actionIds, $allowedActionIds));
        }
        if (in_array($action, ['edit', 'update', 'destroy'])) {
            $allowedGroupIds = Group::whereIn('school_id', $this->schoolIds())
                ->get()->pluck('id')->toArray();
            $isGroupAllowed = in_array($group->id, $allowedGroupIds);
        }
        
        switch ($action) {
            case 'index':
            case 'create':
                return true;
            case 'store':
                return $isMenuAllowed && $isTabAllowed && $isActionAllowed;
            case 'edit':
            case 'delete':
                return $isGroupAllowed;
            case 'update':
                return $isGroupAllowed && $isMenuAllowed && $isTabAllowed && $isActionAllowed;
            default:
                return false;
        }
        
    }
    
}
