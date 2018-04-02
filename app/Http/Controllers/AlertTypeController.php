<?php
namespace App\Http\Controllers;

use App\Http\Requests\AlertTypeRequest;
use App\Models\AlertType;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 警告类型
 *
 * Class AlertTypeController
 * @package App\Http\Controllers
 */
class AlertTypeController extends Controller {
    
    protected $at;
    
    function __construct(AlertType $at) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->at = $at;
        
    }
    
    /**
     * 警告类型列表
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->at->datatable()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建警告类型
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        $this->authorize(
            'cs', AlertType::class
        );
        
        return $this->output();
        
    }
    
    /**
     * 保存警告类型
     *
     * @param AlertTypeRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(AlertTypeRequest $request) {
        
        $this->authorize(
            'cs', AlertType::class
        );
        
        return $this->result(
            $this->at->create($request->all())
        );
        
    }
    
    /**
     * 编辑警告类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $at = $this->at->find($id);
        $this->authorize('eud', $at);
        
        return $this->output([
            'at' => $at,
        ]);
        
    }
    
    /**
     * 更新警告类型
     *
     * @param AlertTypeRequest $request
     * @param $id
     * @return bool|JsonResponse
     * @throws AuthorizationException
     */
    public function update(AlertTypeRequest $request, $id) {
        
        $at = $this->at->find($id);
        $this->authorize('eud', $at);
        
        return $this->result(
            $at->update($request->all())
        );
        
    }
    
    /**
     * 删除警告类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $at = $this->at->find($id);
        $this->authorize('eud', $at);
        
        return $this->result(
            $at->delete()
        );
        
    }
    
}
