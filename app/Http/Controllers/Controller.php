<?php
namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Menu;
use App\Models\School;
use App\Models\Tab;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Controller extends BaseController {
    
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    const HTTP_STATUSCODE_OK = 200;
    const HTTP_STATUSCODE_BAD_REQUEST = 400;
    const HTTP_STATUSCODE_UNAUTHORIZED = 401;
    const HTTP_STATUSCODE_FORBIDDEN = 403;
    const HTTP_STATUSCODE_NOT_FOUND = 404;
    const HTTP_STATUSCODE_METHOD_NOT_ALLOWED = 405;
    const HTTP_STATUSCODE_INTERNAL_SERVER_ERROR = 500;
    
    const MSG_OK = '操作成功';
    const MSG_FAIL = '操作失败';
    const MSG_CREATE_OK = '添加成功';
    const MSG_DEL_OK = '删除成功';
    const MSG_EDIT_OK = '保存成功';
    const MSG_BAD_REQUEST = '请求错误';
    const MSG_UNAUTHORIZED = '无权访问';
    const MSG_FORBIDDEN = '禁止访问';
    const MSG_NOT_FOUND = '找不到需要访问的页面';
    const MSG_METHOD_NOT_ALLOWED = '不支持该请求方法';
    const MSG_INTERNAL_SERVER_ERROR = '内部服务器错误';
    
    protected $result = [
        'statusCode' => self::HTTP_STATUSCODE_OK,
        'message'    => self::MSG_OK,
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
        if (!$action) { return $this->fail('功能不存在'); }

        # 获取功能对应的View
        $view = $action->view;
        if (!$view) { return $this->fail('功能配置错误'); }

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
                        'statusCode' => self::HTTP_STATUSCODE_UNAUTHORIZED,
                        'mId' => Request::get('menuId'),
                        'tId' => Request::get('tabId')
                    ]);
                }
                return response()->json([
                    'statusCode' => self::HTTP_STATUSCODE_OK,
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
                    'statusCode' => self::HTTP_STATUSCODE_OK,
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

    protected function notFound() {

        if (Request::ajax()) {
            return response()->json([
                'statusCode' => self::HTTP_STATUSCODE_NOT_FOUND,
                'message'    => self::MSG_NOT_FOUND,
            ]);
        }

        return abort(self::HTTP_STATUSCODE_NOT_FOUND, self::MSG_NOT_FOUND);

    }

    protected function succeed($msg = self::MSG_OK) {

        if (Request::ajax()) {
            return response()->json([
                'statusCode' => self::HTTP_STATUSCODE_OK,
                'message' => $msg,
            ]);
        }

        return self::HTTP_STATUSCODE_OK . ' : ' . $msg;

    }

    protected function fail($msg = self::MSG_FAIL) {

        if (Request::ajax()) {
            return response()->json([
                'statusCode' => self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR,
                'message'    => $msg,
            ]);
        }

        return abort(self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR, $msg);

    }

    /**
     * 返回操作结果提示信息
     *
     * @param $result
     * @param String $success
     * @param String $failure
     * @return JsonResponse|string
     */
    protected function result($result, String $success = self::MSG_OK, String $failure = self::MSG_FAIL) {
        
        if (Request::ajax()) {
            $this->result = $result
                ? ['statusCode' => self::HTTP_STATUSCODE_OK, 'message' => $success]
                : ['statusCode' => self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR, 'message' => $failure];
            return response()->json($this->result);
        }
        return $result
            ? self::HTTP_STATUSCODE_OK . ' : ' . $success
            : self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR . ' : ' . $failure;
        
    }
    
    public function getUserInfo() {
        
        $code = Request::query('code');
        $url = 'http://weixin.028lk.com/wap_sites/webindex?code=' . $code;
    
        return $code ? redirect($url) : 'no code !';
        
    }

}
