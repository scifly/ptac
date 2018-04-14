<?php
namespace App\Http\Controllers;

use App\Http\Requests\CorpRequest;
use App\Models\Corp;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
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
    
    function __construct(Corp $corp) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->corp = $corp;
        
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
                $this->corp->datatable()
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
        
        $this->authorize(
            'create', Corp::class
        );
        
        return $this->output();
        
    }
    
    /**
     * 保存企业
     *
     * @param CorpRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(CorpRequest $request) {
        
        $this->authorize(
            'store', Corp::class
        );
        
        return $this->result(
            $this->corp->store(
                $request->all(), true
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
        
        $corp = $this->corp->find($id);
        $this->authorize('edit', $corp);
        
        return $this->output([
            'corp' => $corp,
        ]);
        
    }
    
    /**
     * 更新企业
     *
     * @param CorpRequest $request
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(CorpRequest $request, $id) {
        
        $corp = $this->corp->find($id);
        $this->authorize('update', $corp);
        
        return $this->result(
            $corp->modify(
                $request->all(), $id, true
            )
        );
        
    }
    
    /**
     * 删除企业
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $corp = $this->corp->find($id);
        $this->authorize('destroy', $corp);
        
        return $this->result(
            $corp->remove($id, true)
        );
        
    }
    
}
