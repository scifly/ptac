<?php
namespace App\Http\Controllers;

use App\Http\Requests\WapRequest;
use App\Models\Wap;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 微网站
 *
 * Class WapController
 * @package App\Http\Controllers
 */
class WapController extends Controller {
    
    protected $wap;
    
    /**
     * WapController constructor.
     * @param Wap $wap
     */
    public function __construct(Wap $wap) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->approve($this->wap = $wap);
        
    }
    
    /**
     * 微网站详情
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return $this->output(
            $this->wap->index()
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
            ? $this->wap->import()
            : $this->output([
                'wap' => $this->wap->find($id)
            ]);
        
    }
    
    /**
     * 更新微网站
     *
     * @param WapRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function update(WapRequest $request, $id) {
        
        return $this->result(
            $this->wap->modify(
                $request->all(), $id
            )
        );
        
    }
    
}