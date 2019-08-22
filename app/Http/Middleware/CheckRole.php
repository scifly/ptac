<?php
namespace App\Http\Middleware;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Models\{Action, ActionGroup, Department, Group, GroupMenu, Menu, School, Tab, WapSite};
use Closure;
use Illuminate\Http\Request;
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
        if ($role == '运营' || in_array($route, ['/', 'home'])) return $next($request);
    
        # 菜单权限
        $rootMenuId = $this->menu->rootId();
        if ($request->is('pages/*')) {
            abort_if(
                in_array($role, ['企业', '学校'])
                    ? !in_array($menuId, $this->menu->subIds($rootMenuId))
                    : !GroupMenu::where(['menu_id' => $menuId, 'group_id' => $groupId])->first(),
                HttpStatusCode::FORBIDDEN, __('messages.forbidden')
            );
    
            return $next($request);
        }
        
        # 功能权限
        [$cGIds, $sGIds] = array_map(
            function ($names) {
                $builder = Group::whereIn('name', $names);
                return array_merge([0], $builder->pluck('id')->toArray());
            }, ['企业', '学校'], ['学校']
        );
        $groupIds = $role == '企业' ? $cGIds : $sGIds;
        $action = Action::whereRoute($route)->first();
        $abort = in_array($role, ['企业', '学校'])
            ? !Tab::whereIn('group_id', $groupIds)->where('name', $action->tab->name)->first()
            : !ActionGroup::where(['action_id' => $action->id, 'group_id' => $groupId])->first();

        # 校级管理员可访问的企业类功能
        if ($role == '学校') {
            $schoolId = School::whereMenuId($rootMenuId)->first()->id;
            if ($wapSite = WapSite::whereSchoolId($schoolId)->first()) {
                $actions = array_map(
                    function ($route) use ($wapSite) {
                        return sprintf($route, $wapSite->id);
                    }, Constant::ALLOWED_WAPSITE_ACTIONS
                );
            }
            if (in_array($request->path(), $actions ?? [])) {
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
    
}
