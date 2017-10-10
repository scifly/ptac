<?php
namespace App\Http\Controllers;

use App\Http\Requests\AppRequest;
use App\Models\App;
use App\Models\Corp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use App\Facades\Wechat;
/**
 * 微信企业应用
 *
 * Class AppController
 * @package App\Http\Controllers
 */
class AppController extends Controller {
    
    protected $app;
    
    function __construct(App $app) {
        $this->app = $app;
        // $this->middleware('auth');
    }
    
    /**
     * 微信应用列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
    
        if (Request::method() == 'POST') {
            return $this->app->store();
        }
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 创建微信应用
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存微信应用
     *
     * @param AppRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AppRequest $request) {
        
        return $this->app->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 微信应用详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $app = $this->app->find($id);
        if (!$app) {
            return $this->notFound();
        }
        
        return $this->output(__METHOD__, ['app' => $app]);
        
    }
    
    /**
     * 编辑微信应用
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        
        $app = $this->app->find($id);
        if (!$app) { return $this->notFound(); }
        
        return $this->output(__METHOD__, ['app' => $app]);
        
    }
    
    /**
     * 更新指定的微信应用记录
     *
     * @param AppRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(AppRequest $request, $id) {
        
        $app = $this->app->find($id);
        if (!$app) { return $this->notFound(); }
        
        return $app->modify($request->all(), $id)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 获取指定应用的menu
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function menu($id) {
        $app = $this->app->find($id);
        if (!$app) { return $this->notFound(); }
        $accessToken = Wechat::getAccessToken($app->corp_id, $app->secret, $app->agentid);
    
        $menu = json_decode(Wechat::getMenu($accessToken, $app->agentid));
        $a = $app->update(['menu' => json_encode($menu->button)]);
        
        return $this->output(__METHOD__, ['menu' => $menu]);
    }
    /**
     * 删除指定的微信应用记录
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        $app = $this->app->find($id);
        if (!$app) {
            return $this->notFound();
        }
        
        return $app->delete() ? $this->succeed() : $this->fail();
        
    }
    
}
