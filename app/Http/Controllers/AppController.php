<?php
namespace App\Http\Controllers;

use App\Http\Requests\AppRequest;
use App\Models\App;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 应用
 *
 * Class AppController
 * @package App\Http\Controllers
 */
class AppController extends Controller {
    
    protected $app;
    
    /**
     * AppController constructor.
     * @param App $app
     */
    function __construct(App $app) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->app = $app;
        $this->approve($app);
        
    }
    
    /**
     * 应用列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->app->index())
            : $this->output();
        
    }
    
    /**
     * 创建应用
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存应用
     *
     * @param AppRequest $request
     * @return JsonResponse|string
     */
    public function store(AppRequest $request) {
        
        return $this->result(
            $this->app->store(
                $request->all()
            ), __('messages.app.submitted')
        );
        
    }
    
    /**
     * 编辑应用
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'app' => $this->app->find($id)
        ]);
        
    }
    
    /**
     * 更新微信应用
     *
     * @param AppRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(AppRequest $request, $id) {
        
        return $this->result(
            $this->app->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 移除应用
     *
     * @param $id
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->app->remove($id)
        );
        
    }
    
}
