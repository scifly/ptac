<?php
namespace App\Http\Controllers;

use App\Http\Requests\WapSiteRequest;
use App\Models\WapSite;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 微网站
 *
 * Class WapSiteController
 * @package App\Http\Controllers
 */
class WapSiteController extends Controller {
    
    protected $ws;
    
    /**
     * WapSiteController constructor.
     * @param WapSite $ws
     */
    public function __construct(WapSite $ws) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->ws = $ws;
        $this->approve($ws);
        
    }
    
    /**
     * 微网站详情
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return $this->output(
            $this->ws->index()
        );
        
    }
    
    /**
     * 编辑微网站
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return Request::method() == 'POST'
            ? $this->ws->upload()
            : $this->output(['ws' => $this->ws->find($id)]);
        
    }
    
    /**
     * 更新微网站
     *
     * @param WapSiteRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function update(WapSiteRequest $request, $id) {
        
        return $this->result(
            $this->ws->modify($request, $id)
        );
        
    }
    
}