<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
use App\Models\Action;
use App\Models\Menu;
use App\Models\MenuTab;
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
    
    const ROOT_MENU_ID = 1;
    
    protected $tab, $mt, $menu;
    
    /**
     * HomeController constructor.
     * @param Tab $tab
     * @param MenuTab $mt
     * @param Menu $menu
     */
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
        if (!$menuId) {
            $menuId = Menu::whereParentId($this->menu->rootMenuId())
                ->whereIn('uri', ['home', '/'])
                ->first()->id;
            session(['menuId' => $menuId]);
            $department = $this->menu->department($menuId);
        } else {
            if (!session('menuId') || session('menuId') !== $menuId) {
                session(['menuId' => $menuId]);
                session(['menuChanged' => true]);
            } else {
                Session::forget('menuChanged');
            }
            $department = $this->menu->department($menuId);
            $params = ['department' => $department];
            $view = view('home.home', $params);
            if (Request::ajax()) {
                return response()->json([
                    'statusCode' => HttpStatusCode::OK,
                    'title'      => '首页',
                    'uri'        => Request::path(),
                    'html'       => $view->render(),
                    'department' => $department,
                ]);
            }
        }
        
        return view('layouts.web', [
            'menu'       => $this->menu->menuHtml($this->menu->rootMenuId()),
            'menuId'     => $menuId,
            'content'    => view('home.home'),
            'department' => $department,
            'user'       => Auth::user(),
        ]);
        
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
            $action = Action::find($tab->action_id);
            if (!empty($action->route)) {
                $tabArray[] = [
                    'id'     => 'tab_' . $tab->id,
                    'name'   => $tab->comment,
                    'icon'   => isset($tab->icon_id) ? $tab->icon->name : null,
                    'active' => false,
                    'url'    => $action->route,
                ];
            } else {
                $isTabLegit = false;
                break;
            }
        }
        abort_if(empty($tabArray), HttpStatusCode::UNAUTHORIZED, __('messages.unauthorized'));
        abort_if(!$isTabLegit, HttpStatusCode::NOT_FOUND, __('messages.not_found'));
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
            $this->result['html'] = view('shared.site_content', ['tabs' => $tabArray])->render();
            $this->result['department'] = $this->menu->department($id);
            $this->result['title'] = $this->menu->find(session('menuId'))->name;
            
            return response()->json($this->result);
        }
        
        return view('layouts.web', [
            'menu'       => $this->menu->menuHtml($this->menu->rootMenuId()),
            'tabs'       => $tabArray,
            'menuId'     => $id,
            'department' => $this->menu->department($id),
        ]);
        
    }
    
}
