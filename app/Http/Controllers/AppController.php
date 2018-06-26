<?php
namespace App\Http\Controllers;

use App\Http\Requests\AppRequest;
use App\Models\App;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 微信企业应用
 *
 * Class AppController
 * @package App\Http\Controllers
 */
class AppController extends Controller {
    
    protected $app;
    
    function __construct(App $app) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->app = $app;
        $this->approve($app);
        
    }
    
    /**
     * 微信应用列表/同步
     *
     * @param AppRequest $request
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index(AppRequest $request) {
        
        return Request::method() == 'GET'
            ? ($request->query('corpId')
                ? $this->app->index($request)
                : $this->output()
            )
            : $this->app->sync($request);
        
    }
    
    /**
     * 编辑微信应用
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'app' => $this->app->find($id),
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
     * @throws Exception
     */
    public function destroy($id) {
        
        return $this->result(
            $this->app->remove($id)
        );
        
    }
    
}
