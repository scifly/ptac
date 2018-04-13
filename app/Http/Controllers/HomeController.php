<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
use App\Models\Corp;
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
    
    const PAGEJS = 'js/home/page.js';
    const ROOT_MENU_ID = 1;
    
    protected $tab, $mt, $menu;
    
    public function __construct(Tab $tab, MenuTab $mt, Menu $menu) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->tab = $tab;
        $this->mt = $mt;
        $this->menu = $menu;
        
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
                ->first()->id;
            session(['menuId' => $menuId]);
            $menu = new Menu();
            
            return view('home.home', [
                'menu'    => $menu->menuHtml($menu->rootMenuId()),
                'content' => view('home.' . $view),
                'js'      => self::PAGEJS,
                'user'    => Auth::user(),
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
                    'statusCode' => HttpStatusCode::OK,
                    'title'      => '首页',
                    'uri'        => Request::path(),
                    'html'       => view('home.' . $view)->render(),
                ]);
            }
            
            return view('home.home', [
                'menu'    => $menu->menuHtml($menu->rootMenuId()),
                'menuId'  => $menuId,
                'content' => view('home.' . $view),
                'js'      => self::PAGEJS,
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
        $tabIds = $this->mt->tabIdsByMenuId($id);
        $isTabLegit = !empty($tabIds) ?? false;
        # 获取当前用户可以访问的卡片（控制器）id
        $allowedTabIds = $this->tab->allowedTabIds();
        # 封装当前用户可以访问的卡片数组
        $tabArray = [];
        foreach ($tabIds as $tabId) {
            if (!in_array($tabId, $allowedTabIds)) {
                continue;
            }
            $tab = Tab::find($tabId);
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
        abort_if(!$isTabLegit, HttpStatusCode::NOT_FOUND);
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
        # 获取并返回wrapper-content层中的html内容
        if (Request::ajax()) {
            $this->result['html'] = view('partials.site_content', ['tabs' => $tabArray])->render();
            
            return response()->json($this->result);
        }
        # 获取菜单列表
        $menu = new Menu();
        
        return view('home.page', [
            'menu'   => $menu->menuHtml($menu->rootMenuId()),
            'tabs'   => $tabArray,
            'menuId' => $id,
            'js'     => self::PAGEJS,
        ]);
        
    }
    
    /**
     * @return array
     */
    private function getVars(): array {
        
        $user = Auth::user();
        switch ($user->group->name) {
            case '运营':
                $view = 'company';
                break;
            case '企业':
                $view = 'corp';
                break;
            case '学校':
                $view = 'school';
                break;
            default:
                $view = 'school';
                break;
        }
        
        return [$view, $this->menu->rootMenuId()];
        
    }
    
}
