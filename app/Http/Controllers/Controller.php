<?php
namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Menu;
use App\Models\Tab;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

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
     * 根据__METHOD__输出对应的view
     *
     * @param string $method 带控制器名称的方法名称
     * @param array $params 需要输出至view的变量数组
     * @return bool|\Illuminate\Http\JsonResponse
     */
    protected function output($method, array $params = []) {
        
        # 获取功能名称
        $arr = explode('::', $method);
        $m = $arr[1];
        # 获取控制器名称
        $c = explode('\\', $arr[0]);
        $c = $c[sizeof($c) - 1];
        # 获取功能对象
        $action = Action::whereMethod($m)->where('controller', $c)->first();
        if (!$action) {
            return $this->fail($method . '不存在');
        }
        # 获取功能对应的View
        $view = $action->view;
        if (!$view) {
            return $this->fail($method . '配置错误');
        }
        $menu = Menu::whereId(session('menuId'))->first();
        $tab = Tab::whereId(Request::get('tabId'))->first();
        # 保存状态为active的卡片ID
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
            // Todo: redirect to login
            $menuName = session('menuName');
            // $menuName = $this->menuName;
            $params['breadcrumb'] =
                "<span style=\"color: red\">菜单 - <strong>{$menuName}</strong> - 配置错误, 请检查后</span>" .
                '<a href="' . session('pageUrl') . '">重试</a>';
        }
        return response()->json([
            'html'       => view($view, $params)->render(),
            'js'         => $action->js,
            'breadcrumb' => $params['breadcrumb'],
        ]);
        
    }
    
    protected function fail($msg = self::MSG_FAIL) {
        
        $this->result = [
            'statusCode' => self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR,
            'message'    => $msg,
        ];
        return response()->json($this->result);
    }
    
    protected function notFound() {
        
        $this->result = [
            'statusCode' => self::HTTP_STATUSCODE_BAD_REQUEST,
            'message'    => self::MSG_BAD_REQUEST,
        ];
        return response()->json($this->result);
        
    }
    
    protected function succeed($msg = self::MSG_OK) {
        
        $this->result = [
            'statusCode' => self::HTTP_STATUSCODE_OK,
            'message'    => $msg,
        ];
        return response()->json($this->result);
        
    }
    
}
