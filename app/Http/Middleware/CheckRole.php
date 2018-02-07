<?php
namespace App\Http\Middleware;

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
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CheckRole {
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        
        $route = trim($request->route()->uri());
//        if (!Session::exists('menuId') && $route != '/' && $route != 'home') {
//            $this->abort();
//        };
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
                    $menuIds = Menu::subMenuIds(
                        Corp::whereDepartmentId($user->topDeptId())
                            ->first()->menu_id
                    );
                    $abort = !in_array($menuId, $menuIds) ?? false;
                    break;
                case '学校':
                    $menuIds = Menu::subMenuIds(
                        School::whereDepartmentId($user->topDeptId())
                            ->first()->menu_id
                    );
                    $abort = !in_array($menuId, $menuIds) ?? false;
                    break;
                case '教职员工':
                    $school_id = Group::whereId($user->group_id)->first()->school_id;
                    if (!$school_id) {
                        $dept_id = DepartmentUser::whereUserId($user->id)->first()->department_id;
                        $schoolDept = Department::schoolDeptId($dept_id);
                        $school_id = School::whereDepartmentId($schoolDept)->first()->id;
                    }
                    $menuIds = Menu::subMenuIds(
                        School::find($school_id)
                            ->menu_id
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
            if ($abort) { $this->abort(); }
            
            return $next($request);
        }
        # 功能权限判断
//        if (strpos($route, '?')) {
//            $route = explode('?', $route);
//        }
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
        # 企业本身 企业管理模块权限
        if ($role == '企业') {
            $corpId = Corp::whereDepartmentId($user->topDeptId())->first()->id;
            $allowedCorpActions = [
                '/corps/edit/' . $corpId,
                '/corps/update/' . $corpId,
            ];
            if (in_array($request->path(), $allowedCorpActions)) {
                return $next($request);
            }
        }
        # 学校级角色  学校模块、微网站模块权限
        if ($role == '学校') {
            $schoolId = School::whereDepartmentId($user->topDeptId())->first()->id;
            $wapsiteId = WapSite::whereSchoolId($schoolId)->first()->id;
            $allowedSchoolActions = [
                '/schools/show/' . $schoolId,
                '/schools/edit/' . $schoolId,
                '/schools/update/' . $schoolId,
                '/wap_sites/show/' . $wapsiteId,
                '/wap_sites/edit/' . $wapsiteId,
                '/wap_sites/update/' . $wapsiteId,
            ];
            if (in_array($request->path(), $allowedSchoolActions)) {
                return $next($request);
            }
        }
        if ($abort) { $this->abort(); }
        
        return $next($request);
        
    }
    
    private function abort() {
        
        throw new HttpException(403);
        
    }
    
}
