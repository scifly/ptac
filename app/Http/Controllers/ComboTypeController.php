<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
use App\Http\Requests\ComboTypeRequest;
use App\Models\ComboType;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 套餐类型
 *
 * Class ComboTypeController
 * @package App\Http\Controllers
 */
class ComboTypeController extends Controller {
    
    protected $ct;
    
    function __construct(ComboType $ct) {
    
        $this->middleware(['auth', 'checkrole']);
        $this->ct = $ct;
        
    }
    
    /**
     * 套餐类型列表
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->ct->datatable()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建套餐类型
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        $this->authorize(
            'c', ComboType::class
        );
        
        return $this->output();
        
    }
    
    /**
     * 保存套餐类型
     *
     * @param ComboTypeRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(ComboTypeRequest $request) {
        
        $this->authorize(
            'c', ComboType::class
        );
        
        return $this->result(
            ComboType::create($request->all())
        );
        
    }
    
    /**
     * 编辑套餐类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $ct = $this->ct->find($id);
        abort_if(!$ct, HttpStatusCode::NOT_FOUND);
        $this->authorize('rud', $ct);
        
        return $this->output(['ct' => $ct]);
        
    }
    
    /**
     * 更新套餐类型
     *
     * @param ComboTypeRequest $request
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(ComboTypeRequest $request, $id) {
        
        $ct = $this->ct->find($id);
        $this->authorize('rud', $ct);
        
        return $this->result(
            $ct->update($request->all())
        );
        
    }
    
    /**
     * 删除套餐类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $ct = $this->ct->find($id);
        $this->authorize('rud', $ct);
        
        return $this->result($ct->delete());
        
    }
    
}
