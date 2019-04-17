<?php
namespace App\Http\Middleware;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Models\{Action, ActionGroup, Corp, Department, Group, GroupMenu, Menu, School, Tab, WapSite};
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
     * @param  Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        
        $route = trim($request->route()->uri());
        $user = Auth::user();
        $groupId = $user->group_id;
        $role = $user->role();
        $menuId = session('menuId');
    
        # 超级用户直接访问所有功能, 如果访问的是首页，则直接通过并进入下个请求
        if ($role == '运营' || in_array($route, ['/', 'home'])) {
            return $next($request);
        }
    
        $rootMenuId = $this->menu->rootId();
        # 菜单权限判断
        if (stripos($route, 'pages') > -1) {
            if (in_array($role, ['企业', '学校'])) {
                $menuIds = $this->menu->subIds($rootMenuId);
                $abort = !in_array($menuId, $menuIds) ?? false;
            } else {
                $groupMenu = GroupMenu::where([
                    'menu_id' => $menuId,
                    'group_id' => $groupId
                ])->first();
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
            $abort = !Tab::whereIn('group_id', $role == '企业' ? $corpGroupIds : $schoolGroupIds)
                ->where('name', $controller)->first();
        } else {
            # 校级以下角色 action权限判断
            $abort = !ActionGroup::where([
                'action_id' => Action::whereRoute($route)->first()->id,
                'group_id' => $groupId
            ])->first();
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
        Log::debug('you are here');
        Log::debug('abort: ' . ($abort ? 'true' : 'false'));
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
