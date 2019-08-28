<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
use App\Models\Action;
use App\Models\Menu;
use App\Models\Tab;
use App\Policies\Route;
use Auth;
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

/**
 * Class Controller
 * @package App\Http\Controllers
 */
class Controller extends BaseController {
    
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    /**
     * 输出view
     *
     * @param array $params 需要输出至view的变量数组
     * @return bool|JsonResponse
     * @throws Throwable
     */
    protected function output(array $params = []) {
        
        # 获取功能对象
        $tabId = Tab::whereName(
            class_basename(Request::route()->controller)
        )->first()->id;
        $params['uris'] = $this->uris($tabId);
        $params['user'] = Auth::user();
        $action = Action::where([
            'method' => Request::route()->getActionMethod(),
            'tab_id' => $tabId,
        ])->first();
        abort_if(
            !$action, HttpStatusCode::NOT_FOUND,
            __('messages.nonexistent_action')
        );
        $params['breadcrumb'] = $action->name;
        # 获取功能对应的View
        $view = $action->view;
        abort_if(
            !$view, HttpStatusCode::NOT_FOUND,
            __('messages.misconfigured_action')
        );
        # 获取功能对应的菜单/卡片对象
        $menu = Menu::find(session('menuId'));
        $tab = Tab::find(session('tabId'));
        if (Request::ajax()) {
            # 如果请求类型为Ajax
            if ($tab = Tab::find(Request::get('tabId'))) {
                # 如果Http请求的内容需要在卡片中展示
                abort_if(!$menu, HttpStatusCode::UNAUTHORIZED);
                !session('tabId') || session('tabId') !== $tab->id
                    ? session(['tabId' => $tab->id, 'tabChanged' => 1])
                    : Session::forget('tabChanged');
                session(['tabUrl' => Request::path()]);
                
                return response()->json([
                    'html'       => view($view, $params)->render(),
                    'js'         => $action->js,
                    'breadcrumb' => $params['breadcrumb'],
                ]);
            }
            # 如果Http请求的内容需要直接在Wrapper层（不包含卡片）中显示
            session(['menuId' => Request::query('menuId')]);
            Session::forget('tabId');
            
            return response()->json([
                'title'      => $params['breadcrumb'],
                'uri'        => Request::path(),
                'html'       => view($view, $params)->render(),
                'js'         => $action->js,
                'department' => $menu->department(session('menuId')),
            ]);
        } else {
            # 如果是非Ajax请求
            session([
                'menuId' => $menuId = session('menuId') ?? Request::query('menuId'),
                'tabId'  => Request::query('tabId'),
                'tabUrl' => Request::path(),
            ]);
            return $tab
                # 如果请求的内容需要在卡片中展示
                ? response()->redirectTo($menuId ? 'pages/' . $menuId : '/')
                # 如果请求的内容需要直接在Wrapper层（不包含卡片）中显示
                : view('layouts.web', [
                    'menu'       => $menu->htmlTree($menu->rootId()),
                    'tabs'       => [],
                    'content'    => view($view, $params)->render(),
                    'menuId'     => session('menuId'),
                    'js'         => 'js/home/page',
                    'department' => $menu->department($menu->id),
                ]);
        }
        # 如果用户没有登录
        // return Response()->redirectToRoute('login');
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
        $paths = explode('\\', get_called_class());
        $controller = $paths[sizeof($paths) - 1];
        unset($e);
        $action = Action::where([
            'tab_id' => Tab::whereName($controller)->first()->id,
            'method' => $method,
        ])->first();
        # 获取状态消息
        $message = $result
            ? ($success ?? __('messages.ok'))
            : ($failure ?? __('messages.fail'));
        # 获取Http状态码
        $statusCode = $result
            ? HttpStatusCode::OK
            : HttpStatusCode::INTERNAL_SERVER_ERROR;
        
        # 输出状态码及消息
        return Request::ajax()
            ? response()->json(['title' => $action ? $action->name : '', 'message' => $message], $statusCode)
            : $statusCode . ' : ' . $message;
        
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
                $args = [$model->{'find'}($request->route('id')), true];
            }
            $this->authorize($action, $args);
            
            return $next($request);
        });
        
    }
    
    /**
     * 返回指定控制器对应的所有路由
     *
     * @param integer $tabId - 控制器id
     * @return array
     */
    private function uris($tabId) {
        
        $routes = Action::whereTabId($tabId)
            ->where('route', '<>', null)
            ->pluck('route', 'method')
            ->toArray();
        $uris = [];
        foreach ($routes as $key => $value) {
            $uris[$key] = new Route($value);
        }
        
        return $uris;
        
    }
    
}
