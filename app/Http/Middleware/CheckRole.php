<?php
namespace App\Http\Middleware;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Models\Action;
use App\Models\ActionGroup;
use App\Models\Corp;
use App\Models\Department;
use App\Models\DepartmentUser;
use App\Models\Group;
use App\Models\GroupMenu;
use App\Models\Menu;
use App\Models\School;
use App\Models\Tab;
use App\Models\WapSite;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRole {
    
    protected $department, $menu;
    
    function __construct(Department $department, Menu $menu) {
        
        $this->department = $department;
        $this->menu = $menu;
        
    }
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        
        $route = trim($request->route()->uri());
        $user = Auth::user();
        $groupId = $user->group_id;
        $role = $user->group->name;
        $menuId = session('menuId');
        # 超级用户直接访问所有功能, 如果访问的是首页，则直接通过并进入下个请求
        if ($role == '运营' || $route == '/' || $route == 'home') {
            return $next($request);
        }
        # 菜单权限判断
        if (stripos($route, 'pages') > -1) {
            switch ($role) {
                case '企业':
                    $menuIds = $this->menu->subMenuIds(
                        Corp::whereDepartmentId($user->topDeptId())
                            ->first()->menu_id
                    );
                    $abort = !in_array($menuId, $menuIds) ?? false;
                    break;
                case '学校':
                    $menuIds = $this->menu->subMenuIds(
                        School::whereDepartmentId($user->topDeptId())
                            ->first()->menu_id
                    );
                    $abort = !in_array($menuId, $menuIds) ?? false;
                    break;
                case '教职员工':
                    $schoolId = Group::find($user->group_id)->school_id;
                    if (!$schoolId) {
                        $deptId = DepartmentUser::whereUserId($user->id)->first()->department_id;
                        $schoolDept = $this->department->schoolDeptId($deptId);
                        $schoolId = School::whereDepartmentId($schoolDept)->first()->id;
                    }
                    $menuIds = $this->menu->subMenuIds(
                        School::find($schoolId)->menu_id
                    );
                    $abort = !in_array($menuId, $menuIds) ?? false;
                    break;
                default:
                    $groupMenu = GroupMenu::whereMenuId($menuId)
                        ->where('group_id', $groupId)
                        ->first();
                    $abort = !$groupMenu ?? false;
                    break;
            }
            abort_if($abort, HttpStatusCode::FORBIDDEN, __('messages.forbidden'));
            
            return $next($request);
        }
        # 功能权限判断
        $controller = Action::whereRoute($route)->first()->controller;
        switch ($role) {
            case '企业':
                $tab = Tab::whereIn('group_id', [0, 2, 3])
                    ->where('controller', $controller)
                    ->first();
                $abort = !$tab ?? false;
                break;
            case '学校':
                $tab = Tab::whereIn('group_id', [0, 3])
                    ->where('controller', $controller)
                    ->first();
                $abort = !$tab ?? false;
                break;
            case '教职员工':
                $tab = Tab::whereIn('group_id', [0, 3])
                    ->where('controller', $controller)
                    ->first();
                $abort = !$tab ?? false;
                break;
            default:
                # 校级以下角色 action权限判断
                $actionId = Action::whereRoute($route)->first()->id;
                $groupAction = ActionGroup::whereActionId($actionId)
                    ->where('group_id', $groupId)
                    ->first();
                $abort = !$groupAction ?? false;
                break;
        }
        # 企业级管理员可访问的运营类功能
        if ($role == '企业') {
            $corpId = Corp::whereDepartmentId($user->topDeptId())->first()->id;
            $allowedCorpActions = $this->allowedActions(
                Constant::ALLOWED_CORP_ACTIONS, $corpId
            );
            if (in_array($request->path(), $allowedCorpActions)) {
                return $next($request);
            }
        }
        # 校级管理员可访问的企业类功能
        if ($role == '学校') {
            $schoolId = School::whereDepartmentId($user->topDeptId())->first()->id;
            $wapsiteId = WapSite::whereSchoolId($schoolId)->first()->id;
            $allowedSchoolActions = array_merge(
                $this->allowedActions(Constant::ALLOWED_SCHOOL_ACTIONS, $schoolId),
                $this->allowedActions(Constant::ALLOWED_WAPSITE_ACTIONS, $wapsiteId)
            );
            if (in_array($request->path(), $allowedSchoolActions)) {
                return $next($request);
            }
        }
        abort_if($abort, HttpStatusCode::FORBIDDEN, __('messages.forbidden'));
        
        return $next($request);
        
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
