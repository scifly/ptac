<?php
namespace App\Http\Controllers;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\Wechat\WXBizMsgCrypt;
use App\Models\Corp;
use App\Models\Menu;
use App\Models\MenuTab;
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
        
        return view('home.page', [
            'menu'       => $this->menu->menuHtml($this->menu->rootMenuId()),
            'menuId'     => $menuId,
            'content'    => view('home.home'),
            'department' => $department,
            'js'         => self::PAGEJS,
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
            $this->result['department'] = $this->menu->department($id);
            $this->result['title'] = $this->menu->find(session('menuId'))->name;
            
            return response()->json($this->result);
        }
   
        return view('home.page', [
            'menu'       => $this->menu->menuHtml($this->menu->rootMenuId()),
            'tabs'       => $tabArray,
            'menuId'     => $id,
            'department' => $this->menu->department($id),
            'js'         => self::PAGEJS,
        ]);
        
    }
    
    /**
     * （微信端）打开学校列表
     *
     * @return Factory|View
     */
    public function wIndex() {
    
        $app = Request::query('app');
        $user = Auth::user();
        $schoolIds = $user->schoolIds($user->id, session('corpId'));
        return view('wechat.schools', [
            'app' => Constant::APPS[$app],
            'schools' => School::whereIn('id', $schoolIds)->pluck('name', 'id'),
            'url' => $app . '?schoolId='
        ]);
        
    }
    
    /**
     * 接收通讯录变更事件
     */
    public function sync() {
        
        $paths = explode('/', Request::path());
        $corp = Corp::whereAcronym($paths[0])->first();
        
        // 假设企业号在公众平台上设置的参数如下
        $encodingAesKey = $corp->encoding_aes_key;
        $token = $corp->token;
        $corpId = $corp->corpid;
        /*
        ------------使用示例一：验证回调URL---------------
        *企业开启回调模式时，企业号会向验证url发送一个get请求
        假设点击验证时，企业收到类似请求：
        * GET /cgi-bin/wxpush?msg_signature=5c45ff5e21c57e6ad56bac8758b79b1d9ac89fd3&timestamp=1409659589&nonce=263014780&echostr=P9nAzCzyDtyTWESHep1vC5X9xho%2FqYX3Zpb4yKa9SKld1DsH3Iyt3tP3zNdtp%2B4RPcs8TgAE7OaBO%2BFZXvnaqQ%3D%3D
        * HTTP/1.1 Host: qy.weixin.qq.com
        
        接收到该请求时，企业应
        1.解析出Get请求的参数，包括消息体签名(msg_signature)，时间戳(timestamp)，随机数字串(nonce)以及公众平台推送过来的随机加密字符串(echostr),
        这一步注意作URL解码。
        2.验证消息体签名的正确性
        3. 解密出echostr原文，将原文当作Get请求的response，返回给公众平台
        第2，3步可以用公众平台提供的库函数VerifyURL来实现。
        
        */
        // $sVerifyMsgSig = HttpUtils.ParseUrl("msg_signature");
        $sVerifyMsgSig = Request::query('msg_signature');
        // $sVerifyTimeStamp = HttpUtils.ParseUrl("timestamp");
        $sVerifyTimeStamp = Request::query('timestamp');
        // $sVerifyNonce = HttpUtils.ParseUrl("nonce");
        $sVerifyNonce = Request::query('nonce');
        // $sVerifyEchoStr = HttpUtils.ParseUrl("echostr");
        $sVerifyEchoStr = Request::query('echostr');

        // 需要返回的明文
        $sEchoStr = "";
        $wxcpt = new WXBizMsgCrypt($token, $encodingAesKey, $corpId);
        $errCode = $wxcpt->VerifyURL($sVerifyMsgSig, $sVerifyTimeStamp, $sVerifyNonce, $sVerifyEchoStr, $sEchoStr);
        if ($errCode == 0) {
            var_dump($sEchoStr);
            //
            // 验证URL成功，将sEchoStr返回
            // HttpUtils.SetResponce($sEchoStr);
        } else {
            print("ERR: " . $errCode . "\n\n");
        }
    }
    
}
