<?php
namespace App\Http\Controllers;

use App\Helpers\Constant;
use App\Models\{Action, Menu, Tab};
use Auth;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\{Auth\Access\AuthorizesRequests, Bus\DispatchesJobs, Validation\ValidatesRequests};
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\{Request, Session};
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
        
        try {
            $route = Request::route();
            [$controller, $method] = [
                class_basename($route->controller),
                $route->getActionMethod(),
            ];
            throw_if(
                !$ctlr = Tab::whereName($controller)->first(),
                new Exception(__('messages.tab.not_found'))
            );
            throw_if(
                !$action = Action::where(['method' => $method, 'tab_id' => $ctlr->id])->first(),
                new Exception(__('messages.action.not_found'))
            );
            throw_if(
                !$view = $action->view,
                new Exception(__('messages.action.misconfigured'))
            );
            $params['uris'] = $action->uris($ctlr->id);
            $params['breadcrumb'] = $action->name;
            $params['user'] = Auth::user();
            
            # 获取功能对应的菜单/卡片对象
            $menu = Menu::find($menuId = session('menuId'));
            $tabId = session('tabId');
            # 如果请求类型为Ajax
            if (Request::ajax()) {
                # 如果Http请求的内容需要在卡片中展示
                if ($tab = Tab::find(Request::query('tabId'))) {
                    throw_if(!$menu, new Exception(__('messages.bad_request')));
                    $tabId != $tab->id
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
                # 非Ajax请求
                if ($menuId) {
                    # 设置了menuId会话变量
                    return Tab::find($tabId)
                        # 如果请求的内容需要在卡片中展示
                        ? response()->redirectTo('pages/' . $menuId)
                        # 如果请求的内容需要直接在Wrapper层（不包含卡片）中显示
                        : view('layouts.web', [
                            'menu'       => $menu->htmlTree($menu->rootId()),
                            'tabs'       => [],
                            'content'    => view($view, $params)->render(),
                            'menuId'     => session('menuId'),
                            'js'         => 'js/home/page',
                            'department' => $menu->department($menuId),
                        ]);
                } elseif (
                    ($menuId = Request::query('menuId')) &&
                    ($tabId = Request::query('tabId'))
                ) {
                    # 没有设置menuId会话变量
                    session([
                        'menuId' => $menuId,
                        'tabId'  => $tabId,
                        'tabUrl' => Request::path(),
                    ]);
                    
                    return response()->redirectTo('pages/' . $menuId);
                }
            }
            
            # 如果用户没有登录
            return Response()->redirectToRoute('login');
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
    /**
     * 返回操作结果提示信息
     *
     * @param boolean $result
     * @param string|null $success
     * @param string|null $failure
     * @return JsonResponse|string
     */
    protected function result($result, $success = null, $failure = null) {
        
        # 获取功能名称
        $paths = explode('\\', get_called_class());
        $controller = $paths[sizeof($paths) - 1];
        $action = Action::where([
            'tab_id' => Tab::whereName($controller)->first()->id,
            'method' => (new Exception)->getTrace()[1]['function'],
        ])->first();
        # 获取状态消息
        $message = $result
            ? ($success ?? __('messages.ok'))
            : ($failure ?? __('messages.fail'));
        # 获取Http状态码
        $statusCode = $result
            ? Constant::OK
            : Constant::INTERNAL_SERVER_ERROR;
        
        # 输出状态码及消息
        return Request::ajax()
            ? response()->json([
                'title' => $action ? $action->name : '',
                'message' => $message
            ], $statusCode)
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
            /** @var \Illuminate\Http\Request $request */
            if ($id = $request->route('id')) {
                abort_if(
                    !$object = $model->{'find'}($id),
                    Constant::NOT_FOUND,
                    __('messages.not_found')
                );
            }
            
            $this->authorize($action, [$object ?? get_class($model)]);
            
            return $next($request);
        });
        
    }
    
}
