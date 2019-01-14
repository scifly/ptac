<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
use App\Models\{Action, Menu, MenuTab, Tab};
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{Auth, Request, Session};
use Illuminate\View\View;
use Throwable;

/**
 * 首页
 *
 * Class HomeController
 * @package App\Http\Controllers
 */
class HomeController extends Controller {
    
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
        
        if (!($menuId = Request::query('menuId'))) {
            $menuId = Menu::whereParentId($this->menu->rootId())
                ->whereIn('uri', ['home', '/'])->first()->id;
            session(['menuId' => $menuId]);
        } else {
            !session('menuId') || session('menuId') !== $menuId
                ? session(['menuId' => $menuId, 'menuChanged' => true])
                : Session::forget('menuChanged');
        }
        $department = $this->menu->department($menuId);
        
        return Request::ajax()
            ? response()->json([
                'title'      => '首页',
                'uri'        => Request::path(),
                'html'       => view('home.home', ['department' => $department])->render(),
                'department' => $department,
            ])
            : view('layouts.web', [
                'menu'       => $this->menu->htmlTree($this->menu->rootId()),
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
            session(['menuId' => $id, 'menuChanged' => true]);
        } else {
            Session::forget('menuChanged');
        }
        # 获取指定菜单包含的卡片列表
        $tabIds = $this->mt->tabIdsByMenuId($id);
        # 获取当前用户可以访问的卡片（控制器）id
        $allowedTabIds = $this->tab->allowedTabIds();
        # 封装当前用户可以访问的卡片数组
        foreach ($tabIds as $tabId) {
            $tab = Tab::find($tabId);
            $action = Action::find($tab->action_id);
            if (!in_array($tabId, $allowedTabIds) || !$action->route) continue;
            $tabArray[] = [
                'id'     => 'tab_' . $tab->id,
                'name'   => $tab->comment,
                'icon'   => $tab->icon_id ? $tab->icon->name : null,
                'active' => false,
                'url'    => $action->route,
            ];
        }
        abort_if(
            empty($tabArray ?? []), HttpStatusCode::NOT_FOUND,
            __('messages.menu.misconfigured')
        );
        # 刷新页面时打开当前卡片, 不一定是第一个卡片
        $tabArray[0]['active'] = true;
        if (session('tabId')) {
            $tabArray[0]['active'] = false;
            $key = array_search(
                'tab_' . session('tabId'),
                array_column($tabArray, 'id')
            );
            $tabArray[$key]['active'] = true;
            if (!session('tabChanged') && !session('menuChanged')) {
                $tabArray[$key]['url'] = session('tabUrl');
            }
        }
        
        return Request::ajax()
            ? response()->json([
                'html'       => view('shared.site_content', ['tabs' => $tabArray])->render(),
                'department' => $this->menu->department($id),
                'title'      => $this->menu->find(session('menuId'))->name,
            ])
            : view('layouts.web', [
                'menu'       => $this->menu->htmlTree($this->menu->rootId()),
                'tabs'       => $tabArray,
                'menuId'     => $id,
                'department' => $this->menu->department($id),
            ]);
        
    }
    
}
