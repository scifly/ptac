<?php
namespace App\Http\Controllers;

use App\Models\Corp;
use App\Models\GroupTab;
use App\Models\Menu;
use App\Models\MenuTab;
use App\Models\MenuType;
use App\Models\School;
use App\Models\Tab;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
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

    protected $tab;
    
    public function __construct(Tab $tab) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->tab = $tab;

    }

    /**
     * 后台首页
     *
     * @throws Throwable
     */
    public function index() {
    
        $menuId = Request::query('menuId');
        $menu = Menu::find($menuId);
        if (!$menu) {
            list($view, $parentMenuId) = self::getVars();
            $menuId = Menu::whereParentId($parentMenuId)
                ->whereIn('uri', ['home', '/'])
                ->first()
                ->id;
            session(['menuId' => $menuId]);
            return view('home.home', [
                'menu' => Menu::menuHtml(Menu::rootMenuId()),
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
                'menu' => Menu::menuHtml(Menu::rootMenuId()),
                'menuId' => $menuId,
                'content' => view('home.' . $view),
                'js' => 'js/home/page.js',
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
        $tabIds = MenuTab::whereMenuId($id)
            ->orderBy('tab_order')
            ->pluck('tab_id')
            ->toArray();
        $allowedTabIds = $this->tab->allowedTabIds();
        if (empty($tabIds)) { $isTabLegit = false; };
        foreach ($tabIds as $tabId) {
            $tab = Tab::find($tabId);
            if (!in_array($tabId, $allowedTabIds)) { continue; }
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
            abort(404);
        }
        # 获取并返回wrapper-content层中的html内容
        if (Request::ajax()) {
            return response()->json([
                'statusCode' => 200,
                'html' => view('partials.site_content', ['tabs' => $tabArray])->render()
            ]);
        }
        # 获取菜单列表
        $menu = Menu::menuHtml(Menu::rootMenuId());

        return view('home.page', [
            'menu'   => $menu,
            'tabs'   => $tabArray,
            'menuId' => $id,
            'js'     => 'js/home/page.js',
        ]);

    }

    /**
     * @return array
     */
    private static function getVars(): array {

        $user = Auth::user();
        switch ($user->group->name) {
            case '运营':
                $view = 'company';
                $parentMenuId = 1;
                break;
            case '企业':
                $view = 'corp';
                $parentMenuId = Corp::whereDepartmentId($user->topDeptId())
                    ->first()->menu_id;
                break;
            case '学校':
                $view = 'school';
                $parentMenuId = School::whereDepartmentId($user->topDeptId())
                    ->first()->menu_id;
                break;
            default:
                $view = 'school';
                $topDeptId = $user->topDeptId();
                $parentMenuId = School::whereDepartmentId($user->schoolDeptId($topDeptId))
                    ->first()->menu_id;
                break;
        }

        return array($view, $parentMenuId);

    }
    
}
