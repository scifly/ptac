<?php
namespace App\Http\Controllers;

use App\Http\Requests\CorpRequest;
use App\Models\Corp;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 企业
 *
 * Class CorpController
 * @package App\Http\Controllers
 */
class CorpController extends Controller {
    
    protected $corp;
    
    /**
     * CorpController constructor.
     * @param Corp $corp
     */
    function __construct(Corp $corp) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->corp = $corp;
        $this->approve($corp);
        
    }
    
    /**
     * 企业列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->corp->index()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建企业
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存企业
     *
     * @param CorpRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(CorpRequest $request) {
        
        return $this->result(
            $this->corp->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑企业
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'corp' => $this->corp->find($id),
        ]);
        
    }
    
    /**
     * 更新企业
     *
     * @param CorpRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(CorpRequest $request, $id) {
        
        return $this->result(
            $this->corp->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除企业
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->corp->remove($id)
        );
        
    }
    
}
