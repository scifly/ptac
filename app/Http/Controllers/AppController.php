<?php
namespace App\Http\Controllers;

use App\Http\Requests\AppRequest;
use App\Models\App;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 公众号(企业应用)
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
        $this->approve($this->app = $app);
        
    }
    
    /**
     * 公众号(应用)列表
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
     * 创建公众号(应用)
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存公众号(应用)
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
     * 编辑公众号(应用)
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
     * 更新公众号(应用)
     *
     * @param AppRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(AppRequest $request, $id) {
        
        return $this->result(
            $this->app->modify(
                $request->all(), $id
            ), __('messages.app.submitted')
        );
        
    }
    
    /**
     * 移除公众号(应用)
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
