<?php
namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Menu;
use App\Models\Tab;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Throwable;

class Controller extends BaseController {
    
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    # Informational 1xx
    const CONTINUE = 100;
    const SWITCHING_PROTOCOLS = 101;
    # Success 2xx
    const OK = 200;
    const CREATED = 201;
    const ACCEPTED = 202;
    const NONAUTHORITATIVE_INFORMATION = 203;
    const NO_CONTENT = 204;
    const RESET_CONTENT = 205;
    const PARTIAL_CONTENT = 206;
    # Redirection 3xx
    const MULTIPLE_CHOICES = 300;
    const MOVED_PERMANENTLY = 301;
    const MOVED_TEMPORARILY = 302;
    const SEE_OTHER = 303;
    const NOT_MODIFIED = 304;
    const USE_PROXY = 305;
    # Client Error 4xx
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const PAYMENT_REQUIRED = 402;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const METHOD_NOT_ALLOWED = 405;
    const NOT_ACCEPTABLE = 406;
    const PROXY_AUTHENTICATION_REQUIRED = 407;
    const REQUEST_TIMEOUT = 408;
    const CONFLICT = 409;
    const GONE = 410;
    const LENGTH_REQUIRED = 411;
    const PRECONDITION_FAILED = 412;
    const REQUEST_ENTITY_TOO_LARGE = 413;
    const REQUESTURI_TOO_LARGE = 414;
    const UNSUPPORTED_MEDIA_TYPE = 415;
    const REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    const EXPECTATION_FAILED = 417;
    const IM_A_TEAPOT = 418;
    # Server Error 5xx
    const INTERNAL_SERVER_ERROR = 500;
    const NOT_IMPLEMENTED = 501;
    const BAD_GATEWAY = 502;
    const SERVICE_UNAVAILABLE = 503;
    const GATEWAY_TIMEOUT = 504;
    const HTTP_VERSION_NOT_SUPPORTED = 505;
    
    protected $result = [
        'statusCode' => self::OK,
        'message'    => '操作成功',
    ];
    
    /**
     * 输出view
     *
     * @param array $params 需要输出至view的变量数组
     * @return bool|JsonResponse
     * @throws Throwable
     */
    protected function output(array $params = []) {

        # 获取功能对象
        $method = Request::route()->getActionMethod();
        $controller = class_basename(Request::route()->controller);
        $action = Action::whereMethod($method)->where('controller', $controller)->first();
        if (!$action) { return $this->fail(__('messages.nonexistent_action')); }

        # 获取功能对应的View
        $view = $action->view;
        if (!$view) { return $this->fail(__('messages.misconfigured_action')); }

        # 获取功能对应的菜单/卡片对象
        $menu = Menu::find(session('menuId'));
        $tab = Tab::find(session('tabId'));

        # 如果请求类型为Ajax
        if (Request::ajax()) {
            $tab = Tab::find(Request::get('tabId'));
            # 如果Http请求的内容需要在卡片中展示
            if ($tab) {
                if (!session('tabId') || session('tabId') !== $tab->id) {
                    session(['tabId' => $tab->id]);
                    session(['tabChanged' => 1]);
                } else {
                    Session::forget('tabChanged');
                }
                session(['tabUrl' => Request::path()]);
                if ($menu) {
                    $params['breadcrumb'] = $menu->name . ' / ' . $tab->name . ' / ' . $action->name;
                } else {
                    return response()->json([
                        'statusCode' => self::UNAUTHORIZED,
                        'mId' => Request::get('menuId'),
                        'tId' => Request::get('tabId')
                    ]);
                }
                return response()->json([
                    'statusCode' => self::OK,
                    'html'       => view($view, $params)->render(),
                    'js'         => $action->js,
                    'breadcrumb' => $params['breadcrumb'],
                ]);
            # 如果Http请求的内容需要直接在Wrapper层（不包含卡片）中显示
            } else {
                session(['menuId' => Request::query('menuId')]);
                Session::forget('tabId');
                $menu = Menu::find(session('menuId'));
                $params['breadcrumb'] = $menu->name . ' / ' . $action->name;
                return response()->json([
                    'statusCode' => self::OK,
                    'title' => $params['breadcrumb'],
                    'uri' => Request::path(),
                    'html' => view($view, $params)->render(),
                    'js' => $action->js
                ]);
            }
        }
        # 如果是非Ajax请求，且用户已登录
        if (session('menuId')) {
            # 如果请求的内容需要在卡片中展示
            if ($tab) {
                return response()->redirectTo('pages/' . session('menuId'));
            # 如果请求的内容需要直接在Wrapper层（不包含卡片）中显示
            } else {
                $params['breadcrumb'] = $menu->name . ' / ' . $action->name;
                return view('home.page', [
                    'menu' => Menu::menuHtml(Menu::rootMenuId()),
                    'tabs' => [],
                    'content' => view($view, $params)->render(),
                    'menuId' => session('menuId'),
                    'js' => 'js/home/page.js',
                ]);
            }
        }
        # 如果是非Ajax请求，且用户已登录，但没有设置menuId会话变量
        if (Request::query('menuId') && Request::query('tabId')) {
            session(['menuId' => Request::query('menuId')]);
            session(['tabId' => Request::query('tabId')]);
            session(['tabUrl' => Request::path()]);
            return response()->redirectTo('pages/' . session('menuId'));
        }

        # 如果用户没有登录
        return Response()->redirectToRoute('login');
        
    }

    protected function notFound($message = null) {

        $statusCode = self::NOT_FOUND;
        $message = $message ?? __('messages.not_found');
        if (Request::ajax()) {
            return response()->json([
                'statusCode' => $statusCode,
                'message'    => $message,
            ]);
        }

        return abort($statusCode, $message);

    }
    
    /**
     * 返回"操作成功"消息
     *
     * @param string $message
     * @return JsonResponse|string
     */
    protected function succeed($message = null) {

        $statusCode = self::OK;
        $message = $message ?? __('messages.ok');
        if (Request::ajax()) {
            return response()->json([
                'statusCode' => $statusCode,
                'message' => $message,
            ]);
        }

        return $statusCode . ' : ' . $message;

    }

    protected function fail($message = null) {

        $statusCode = self::INTERNAL_SERVER_ERROR;
        $message = $message ?? __('messages.fail');
        if (Request::ajax()) {
            return response()->json([
                'statusCode' => $statusCode,
                'message'    => $message,
            ]);
        }

        return abort($statusCode, $message);

    }

    /**
     * 返回操作结果提示信息
     *
     * @param $result
     * @param String $success
     * @param String $failure
     * @return JsonResponse|string
     */
    protected function result($result, String $success = null, String $failure = null) {
        
        $statusCode = $result ? self::OK : self::INTERNAL_SERVER_ERROR;
        $message = $result
            ? ($success ?? __('messages.ok'))
            : ($failure ?? __('messages.fail'));
        if (Request::ajax()) {
            return response()->json([
                'statusCode' => $statusCode,
                'message' => $message
            ]);
        }
        return $statusCode . ' : ' . $message;
        
    }
    
    public function getUserInfo() {
        
        $code = Request::query('code');
        $url = 'http://weixin.028lk.com/wap_sites/webindex?code=' . $code;
    
        return $code ? redirect($url) : 'no code !';
        
    }

}
