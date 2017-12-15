<?php
namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Corp;
use App\Models\Department;
use App\Models\Menu;
use App\Models\MenuTab;
use App\Models\MenuType;
use App\Models\School;
use App\Models\Tab;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Throwable;

/**
 * 首页
 *
 * Class HomeController
 * @package App\Http\Controllers
 */
class HomeController extends Controller {

    protected $menu;
    protected $department;
    protected $action;
    protected $tab;

    public function __construct(
        Menu $menu,
        Action $action,
        Tab $tab,
        Department $department
    ) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->menu = $menu;
        $this->action = $action;
        $this->tab = $tab;
        $this->department = $department;

    }
    
    /**
     * 后台首页
     *
     * @throws Throwable
     */
    public function index() {
    
        $menuId = Request::query('menuId');
        $menu = $this->menu->find($menuId);
        if (!$menu) {
            $user = Auth::user();
            switch ($user->group->name) {
                case '运营':
                    $view = 'company';
                    $menuId = $this->menu
                        ->where('parent_id', 1)
                        ->whereIn('uri', ['home', '/'])
                        ->first()
                        ->id;
                    break;
                case '企业':
                    $view = 'corp';
                    $parentMenuId = Corp::whereDepartmentId($user->topDeptId($user))
                        ->first()
                        ->menu_id;
                    $menuId = $this->menu
                        ->where('parent_id', $parentMenuId)
                        ->where('uri', ['home', '/'])
                        ->first()
                        ->id;
                    break;
                case '学校':
                    $view = 'school';
                    $parentMenuId = School::whereDepartmentId($user->topDeptId($user))
                        ->first()->menu_id;
                    $menuId = $this->menu
                        ->where('parent_id', $parentMenuId)
                        ->where('uri', ['home', '/'])
                        ->first()
                        ->id;
                    break;
                default:
                    $view = 'school';
                    $toDeptId = $user->topDeptId($user);
//                    Log::debug($user->getDeptSchoolId($toDeptId));
                    $parentMenuId = School::whereDepartmentId($user->getDeptSchoolId($toDeptId))
                        ->first()->menu_id;
                    $menuId = $this->menu
                        ->where('parent_id', $parentMenuId)
                        ->where('uri', ['home', '/'])
                        ->first()
                        ->id;
                    break;
            }
            session(['menuId' => $menuId]);
            return view('home.home', [
                'menu' => $this->menu->getMenuHtml($this->menu->rootMenuId()),
                'content' => view('home.' . $view),
                'js' => 'js/home/page.js',
                'user' => Auth::user()
            ]);
        } else {
            if (!session('menuId') || session('menuId') !== $menuId) {
                session(['menuId' => $menuId]);
                session(['menuChanged' => true]);
            } else {
                Session::forget('menuChanged');
            }
            if (!$menu->parent->parent_id) {
                $view = 'company';
            } elseif (MenuType::find($menu->parent->menu_type_id)->name == '企业') {
                $view = 'corp';
            } else {
                $view = 'school';
            }
            if (Request::ajax()) {
                return response()->json([
                    'statusCode' => 200,
                    'title' => '首页',
                    'uri' => Request::path(),
                    'html' => view('home.' . $view)->render()
                ]);
            }
            return view('home.home', [
                'menu' => $this->menu->getMenuHtml($this->menu->rootMenuId()),
                'menuId' => $menuId,
                'js' => 'js/home/page.js',
                'user' => Auth::user()
            ]);
        }

    }
    
    /**
     * 菜单入口
     *
     * @param $id
     * @return Factory|JsonResponse|View
     * @throws Exception
     * @throws Throwable
     */
    public function menu($id) {

        if (!session('menuId') || session('menuId') !== $id) {
            session(['menuId' => $id]);
            session(['menuChanged' => true]);
        } else {
            Session::forget('menuChanged');
        }
        # 获取卡片列表
        $tabArray = [];
        $isTabLegit = true;
        $tabRanks = MenuTab::whereMenuId($id)->get()->sortBy('tab_order')->toArray();
        if (empty($tabRanks)) { $isTabLegit = false; };
        foreach ($tabRanks as $rank) {
            $tab = Tab::whereId($rank['tab_id'])->first();
            if (!empty($tab->action->route)) {
                $tabArray[] = [
                    'id'     => 'tab_' . $tab->id,
                    'name'   => $tab->name,
                    'icon'   => isset($tab->icon_id) ? $tab->icon->name : null,
                    'active' => false,
                    'url'    => $tab->action->route,
                ];
            } else {
                $isTabLegit = false;
                break;
            }
        }
        if ($isTabLegit) {
            # 刷新页面时打开当前卡片, 不一定是第一个卡片
            if (session('tabId')) {
                $key = array_search(
                    'tab_' . session('tabId'),
                    array_column($tabArray, 'id')
                );
                $tabArray[$key]['active'] = true;
                if (!session('tabChanged') && !session('menuChanged')) {
                    $tabArray[$key]['url'] = session('tabUrl');
                }
            } else {
                $tabArray[0]['active'] = true;
            }
        } else {
            $tabArray = [];
        }
        # 如果菜单没有配置或配置有误, 则显示菜单配置卡片
        if (!$isTabLegit) {
            session(['menuId' => 0]);
            $actionId = Action::whereEnabled(1)
                ->where('controller', 'MenuController')
                ->where('method', 'index')
                ->first()
                ->id;
            $tab = Tab::whereEnabled('1')
                ->where('controller', 'MenuController')
                ->where('action_id', $actionId)
                ->first();
            $tabArray[] = [
                'id'     => 'tab_' . $tab->id,
                'name'   => $tab->name,
                'icon'   => isset($tab->icon_id) ? $tab->icon->name : null,
                'active' => true,
                'url'    => $tab->action->route,
            ];
        }
        # 获取并返回wrapper-content层中的html内容
        try {
            $html = view('partials.site_content', ['tabs' => $tabArray])->render();
        } catch (Exception $e) {
            throw $e;
        }
        if (Request::ajax()) {
            return response()->json([
                'statusCode' => 200,
                'html' => $html 
            ]);
        }
        # 获取菜单列表
        $menu = $this->menu->getMenuHtml($this->menu->rootMenuId());
        return view('home.page', [
            'menu'   => $menu,
            'tabs'   => $tabArray,
            'menuId' => $id,
            'js'     => 'js/home/page.js',
            'user'   => Auth::user(),
        ]);

    }

}
