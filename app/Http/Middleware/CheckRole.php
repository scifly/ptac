<?php
namespace App\Http\Middleware;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Models\Action;
use App\Models\ActionGroup;
use App\Models\Corp;
use App\Models\Department;
use App\Models\Group;
use App\Models\GroupMenu;
use App\Models\Menu;
use App\Models\School;
use App\Models\Tab;
use App\Models\WapSite;
use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * Class CheckRole
 * @package App\Http\Middleware
 */
class CheckRole {
    
    protected $department, $menu;
    
    /**
     * CheckRole constructor.
     * @param Department $department
     * @param Menu $menu
     */
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
        # todo -
        $role = $user->group->name;
        $menuId = session('menuId');
    
        # 超级用户直接访问所有功能, 如果访问的是首页，则直接通过并进入下个请求
        if ($role == '运营' || $route == '/' || $route == 'home') {
            return $next($request);
        }
    
        $rootMenuId = $this->menu->rootMenuId();
        # 菜单权限判断
        if (stripos($route, 'pages') > -1) {
            if (in_array($role, ['企业', '学校'])) {
                $menuIds = $this->menu->subMenuIds($rootMenuId);
                $abort = !in_array($menuId, $menuIds) ?? false;
            } else {
                $groupMenu = GroupMenu::whereMenuId($menuId)
                    ->where('group_id', $groupId)
                    ->first();
                $abort = !$groupMenu ?? false;
            }
            abort_if(
                $abort,
                HttpStatusCode::FORBIDDEN,
                __('messages.forbidden')
            );
            
            return $next($request);
        }
        
        # 功能权限判断
        $controller = Action::whereRoute($route)->first()->tab->name;
        $corpGroupIds = array_merge([0], Group::whereIn('name', ['企业', '学校'])->pluck('id')->toArray());
        $schoolGroupIds = [0, Group::whereName('学校')->first()->id];
        if (in_array($role, ['企业', '学校'])) {
            $tab = Tab::whereIn('group_id', $role == '企业' ? $corpGroupIds : $schoolGroupIds)
                ->where('name', $controller)
                ->first();
            $abort = !$tab ?? false;
        } else {
            # 校级以下角色 action权限判断
            $actionId = Action::whereRoute($route)->first()->id;
            $groupAction = ActionGroup::whereActionId($actionId)
                ->where('group_id', $groupId)
                ->first();
            $abort = !$groupAction ?? false;
        }

        # 企业级管理员可访问的运营类功能
        if ($role == '企业') {
            $corpId = Corp::whereMenuId($rootMenuId)->first()->id;
            $allowedCorpActions = $this->allowedActions(
                Constant::ALLOWED_CORP_ACTIONS, $corpId
            );
            if (in_array($request->path(), $allowedCorpActions)) {
                return $next($request);
            }
        }

        # 校级管理员可访问的企业类功能
        if ($role == '学校') {
            $schoolId = School::whereMenuId($rootMenuId)->first()->id;
            $wapSite = WapSite::whereSchoolId($schoolId)->first();
            $allowedSchoolActions = [];
            if ($wapSite) {
                $wapSiteId = $wapSite->id;
                $allowedSchoolActions = array_merge(
                    $this->allowedActions(Constant::ALLOWED_SCHOOL_ACTIONS, $schoolId),
                    $this->allowedActions(Constant::ALLOWED_WAPSITE_ACTIONS, $wapSiteId)
                );
            }
            if (in_array($request->path(), $allowedSchoolActions)) {
                return $next($request);
            }
        }
        abort_if(
            $abort,
            HttpStatusCode::FORBIDDEN,
            __('messages.forbidden')
        );
        
        return $next($request);
        
    }
    
    /**
     * @param array $actions
     * @param $id
     * @return array
     */
    private function allowedActions(array $actions, $id) {
        
        return array_map(
            function ($str) use ($id) {
                return sprintf($str, $id);
            },
            $actions
        );
        
    }
    
}
