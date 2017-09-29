<?php
namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Department;
use App\Models\DepartmentType;
use App\Models\GroupMenu;
use App\Models\GroupTab;
use App\Models\Menu;
use App\Models\MenuTab;
use App\Models\MenuType;
use App\Models\Tab;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

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
        // $this->middleware(['auth']);
        $this->menu = $menu;
        $this->action = $action;
        $this->tab = $tab;
        $this->department = $department;

    }

    public function index() {

        // $this->action->scan();
        // $this->tab->scan();
        $rootMenu = $this->menu->find(1);
        $menu = null;
        if (!$rootMenu) {
            $rootMenu = $this->menu->create([
                'name'         => '菜单',
                'menu_type_id' => MenuType::whereName('根')->first()->id,
                'enabled'      => 1,
            ]);
            $menu = $this->menu->create([
                'name'         => '首页',
                'parent_id'    => $rootMenu->id,
                'menu_type_id' => MenuType::whereName('其他')->first()->id,
                'enabled'      => 1,
            ]);
        } else {
            $menu = Menu::whereName('首页')->first();
        }
        $rootDepartment = $this->department->find(1);
        if (!$rootDepartment) {
            $department = $this->department->create([
                'name'               => '部门',
                'department_type_id' => DepartmentType::whereName('根')->first()->id,
                'enabled'            => 1,
            ]);
        }

        return redirect('pages/' . $menu->id);

    }

    public function menu($id) {

        if (!session('menuId') || session('menuId') !== $id) {
            session(['menuId' => $id]);
            session(['menuName' => Menu::whereId($id)->first()->name]);
            session(['pageUrl' => Request::fullUrl()]);
            session(['menuChanged' => true]);
        } else {
            Session::forget('menuChanged');
        }
        // # 获取session中用户信息
        // $user = Auth::user();
        // $departmentIds = [];
        // foreach ($user->departments as $d)
        // {
        //     $departmentIds[] = $d->id;
        // }
        // sort($departmentIds);
        // $rootId = $departmentIds[0];
        // $rootName = Department::whereId($rootId)->first()->name;
        // # 获取根菜单Id
        // $menuId = Menu::whereName($rootName)->first()->id;

        # 获取卡片列表
        $tabArray = [];
        $isTabLegit = true;
        $tabRanks = MenuTab::whereMenuId($id)->get()->sortBy('tab_order')->toArray();

        if (empty($tabRanks)) {
            $isTabLegit = false;
        };
        foreach ($tabRanks as $rank) {
            $tab = Tab::whereId($rank['tab_id'])->first();
            if (!empty($tab->action->route)) {
                $tabArray[] = [
                    'id'     => 'tab_' . $tab->id,
                    'name'   => $tab->name,
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
            $actionId = Action::whereEnabled(1)->where('controller', 'MenuController')->
            where('method', 'index')->first()->id;
            $tab = Tab::whereEnabled('1')->where('controller', 'MenuController')->
            where('action_id', $actionId)->first();
            $tabArray[] = [
                'id'     => 'tab_' . $tab->id,
                'name'   => $tab->name,
                'active' => true,
                'url'    => $tab->action->route,
            ];
        }
        # 获取菜单列表

        $menu = $this->menu->getMenuHtml($id);

        return view('home.page', [
            'menu'   => $menu,
            'tabs'   => $tabArray,
            'menuId' => $id,
            'js'     => 'js/home/page.js',
            // 'user'   => $user,
        ]);

    }

}
