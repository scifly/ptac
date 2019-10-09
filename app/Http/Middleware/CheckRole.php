<?php
namespace App\Http\Middleware;

use App\Helpers\ModelTrait;
use App\Models\{Action, ActionGroup, Group, GroupMenu, Menu, Tab};
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

/**
 * Class CheckRole
 * @package App\Http\Middleware
 */
class CheckRole {
    
    protected $menu;
    
    /**
     * CheckRole constructor.
     * @param Menu $menu
     */
    function __construct(Menu $menu) {
        
        $this->menu = $menu;
        
    }
    
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws Throwable
     */
    public function handle($request, Closure $next) {
        
        try {
            $route = trim($request->route()->uri());
            $user = Auth::user();
            $role = $user->role();
            $hypos = ['企业', '学校'];
            $abort = in_array($role, $hypos);
            $where = ['group_id' => $user->group_id];
            if ($role == '运营' || in_array($route, ['/', 'home'])) {
                $abort = false;
            } elseif ($request->is('pages/*')) {
                # 菜单权限
                $menuId = session('menuId');
                $abort = $abort
                    ? $this->menu->subIds($this->menu->rootId())->flip()->has($menuId)
                    : !GroupMenu::where(array_merge(['menu_id' => $menuId], $where))->first();
            } else {
                # 功能权限
                [$cGIds, $sGIds] = array_map(
                    function ($roles) {
                        $builder = Group::whereIn('name', $roles);
                        return collect([0])->merge($builder->pluck('id'));
                    }, $hypos, ['学校']
                );
                $groupIds = $role == '企业' ? $cGIds : $sGIds;
                $action = Action::whereRoute($route)->first();
                $abort = $abort
                    ? !Tab::whereIn('group_id', $groupIds)->where('name', $action->tab->name)->first()
                    : !ActionGroup::where(array_merge(['action_id' => $action->id], $where))->first();
            }
            throw_if($abort, new Exception(__('messages.forbidden')));
        } catch (Exception $e) {
            throw $e;
        }
        
        return $next($request);
    
    }
    
}
