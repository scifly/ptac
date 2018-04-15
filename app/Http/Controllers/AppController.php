<?php
namespace App\Http\Controllers;

use Throwable;
use App\Models\App;
use App\Http\Requests\AppRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;

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
     * 微信应用列表
     *
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function index() {
        
        if (Request::method() == 'POST') {
            return $this->app->store();
        } else {
            if (Request::query('corpId')) {
                return $this->app->appList(
                    Request::query('corpId')
                );
            }
            return $this->output();
        }
    
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
     * 获取指定应用的menu
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function sync($id) {
        
        return $this->output([
            'menu' => $this->app->find($id)->menu
        ]);
        
    }
    
}
