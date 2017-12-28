<?php
namespace App\Http\Middleware;

use App\Models\Action;
use App\Models\ActionGroup;
use App\Models\Corp;
use App\Models\Group;
use App\Models\GroupMenu;
use App\Models\Menu;
use App\Models\School;
use App\Models\Tab;
use App\Models\WapSite;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
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
        
        $route = $request->route()->uri();
        if (!Session::exists('menuId') && $route != '/') {
            $this->abort();
        };
        $user = Auth::user();
        $groupId = $user->group_id;
        $role = $user->group->name;
        $menuId = session('menuId');
        $menu = new Menu();
        # 超级用户直接访问所有功能
        if ($role == '运营' || $route == '/' || $route == '/home') {
            return $next($request);
        }
        # 菜单权限判断
        if (stripos($route, 'pages') > -1) {
            switch ($role) {
                case '企业':
                    $menuIds = $menu->subMenuIds(
                        Corp::whereDepartmentId($user->topDeptId())
                            ->first()->menu_id
                    );
                    $abort = !in_array($menuId, $menuIds) ?? false;
                    break;
                case '学校':
                    $menuIds = $menu->subMenuIds(
                        School::whereDepartmentId($user->topDeptId())
                            ->first()->menu_id
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
            if ($abort) {
                $this->abort();
            }
            
            return $next($request);
        }
        # 功能权限判断
        switch ($role) {
            case '企业':
            case '学校':
                $tab = Tab::whereGroupId($user->group_id)->orWhere('group_id', 0)
                    ->where('controller', Action::whereRoute($route)->first()->controller)
                    ->first();
                $abort = !$tab ?? false;
                break;
            default:
                # 校级以下角色 action权限判断
                $groupAction = ActionGroup::whereActionId(
                    Action::whereRoute($route)->first()->id
                )->where('group_id', $groupId)->first();
                $abort = !$groupAction ?? false;
                break;
        }
        # 企业本身 企业管理模块权限
        if ($role == '企业') {
            $corpId = Corp::whereDepartmentId($user->topDeptId())->first()->id;
            $allowedCorpActions = [
                '/corps/show/' . $corpId,
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
