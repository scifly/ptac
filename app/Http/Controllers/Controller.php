<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
use App\Models\Action;
use App\Models\Menu;
use App\Models\Tab;
use App\Policies\Route;
use Exception;
use Illuminate\Database\Eloquent\Model;
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
    
    protected $result = [
        'statusCode' => HttpStatusCode::OK,
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
        $params['uris'] = $this->uris($controller);
        $action = Action::whereMethod($method)->where('controller', $controller)->first();
        abort_if(
            !$action,
            HttpStatusCode::NOT_FOUND,
            __('messages.nonexistent_action')
        );
        # 获取功能对应的View
        $view = $action->view;
        abort_if(
            !$view,
            HttpStatusCode::NOT_FOUND,
            __('messages.misconfigured_action')
        );
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
                    $params['breadcrumb'] = $action->name;
                } else {
                    return response()->json([
                        'statusCode' => HttpStatusCode::UNAUTHORIZED,
                        'mId'        => Request::get('menuId'),
                        'tId'        => Request::get('tabId'),
                    ]);
                }
                
                return response()->json([
                    'statusCode' => HttpStatusCode::OK,
                    'html'       => view($view, $params)->render(),
                    'js'         => $action->js,
                    'breadcrumb' => $params['breadcrumb'],
                ]);
            }
            # 如果Http请求的内容需要直接在Wrapper层（不包含卡片）中显示
            session(['menuId' => Request::query('menuId')]);
            Session::forget('tabId');
            $params['breadcrumb'] = $action->name;
            
            return response()->json([
                'statusCode' => HttpStatusCode::OK,
                'title'      => $params['breadcrumb'],
                'uri'        => Request::path(),
                'html'       => view($view, $params)->render(),
                'js'         => $action->js,
                'department' => $menu->department(session('menuId')),
            ]);
        }
        # 如果是非Ajax请求，且用户已登录
        if (session('menuId')) {
            # 如果请求的内容需要在卡片中展示
            if ($tab) {
                return response()->redirectTo('pages/' . session('menuId'));
                # 如果请求的内容需要直接在Wrapper层（不包含卡片）中显示
            } else {
                $params['breadcrumb'] = $action->name;
                
                return view('home.page', [
                    'menu'       => $menu->menuHtml($menu->rootMenuId()),
                    'tabs'       => [],
                    'content'    => view($view, $params)->render(),
                    'menuId'     => session('menuId'),
                    'js'         => 'js/home/page',
                    'department' => $menu->department($menu->id),
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
    
    /**
     * 返回指定控制器对应的所有路由
     *
     * @param $controller
     * @return array
     */
    private function uris($controller) {
        
        $routes = Action::whereController($controller)
            ->where('route', '<>', null)
            ->pluck('route', 'method')
            ->toArray();
        $uris = [];
        foreach ($routes as $key => $value) {
            $uris[$key] = new Route($value);
        }
        
        return $uris;
        
    }
    
    /**
     * 返回操作结果提示信息
     *
     * @param mixed $result
     * @param String $success
     * @param String $failure
     * @return JsonResponse|string
     */
    protected function result($result, String $success = null, String $failure = null) {
        
        # 获取功能名称
        $e = new Exception();
        $method = $e->getTrace()[1]['function'];
        $path = explode('\\', get_called_class());
        $controller = $path[sizeof($path) - 1];
        unset($e);
        $title = Action::whereMethod($method)
            ->where('controller', $controller)
            ->first()->name;
        # 获取Http状态码
        $statusCode = $result
            ? HttpStatusCode::OK
            : HttpStatusCode::INTERNAL_SERVER_ERROR;
        # 获取状态消息
        $message = $result
            ? ($success ?? __('messages.ok'))
            : ($failure ?? __('messages.fail'));
        # 输出状态码及消息
        if (Request::ajax()) {
            return $result
                ? response()->json([
                    'statusCode' => $statusCode,
                    'message'    => $message,
                    'title'      => $title,
                ])
                : abort($statusCode, $message);
        }
        
        return $result
            ? $statusCode . ' : ' . $message
            : abort($statusCode, $message);
        
    }
    
    /**
     * 控制器方法授权
     *
     * @param String $action
     * @param Model $model
     */
    protected function approve(Model $model, $action = 'operation') {
        
        $this->middleware(function ($request, $next) use ($model, $action) {
            $args = [get_class($model)];
            /** @var \Illuminate\Http\Request $request */
            if ($request->route('id')) {
                $args = [$model->find($request->route('id')), true];
            }
            $this->authorize($action, $args);
            
            return $next($request);
        });
        
    }
    
}
