<?php
namespace App\Http\Controllers;

use App\Http\Requests\ComboTypeRequest;
use App\Models\ComboType;
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
    
    /**
     * ComboTypeController constructor.
     * @param ComboType $ct
     */
    function __construct(ComboType $ct) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->ct = $ct;
        $this->approve($ct);
        
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
                $this->ct->index()
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
        
        return $this->output();
        
    }
    
    /**
     * 保存套餐类型
     *
     * @param ComboTypeRequest $request
     * @return JsonResponse
     */
    public function store(ComboTypeRequest $request) {
        
        return $this->result(
            $this->ct->store(
                $request->all()
            )
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
        
        return $this->output([
            'ct' => $this->ct->find($id),
        ]);
        
    }
    
    /**
     * 更新套餐类型
     *
     * @param ComboTypeRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(ComboTypeRequest $request, $id) {
        
        return $this->result(
            $this->ct->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除套餐类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->ct->remove($id)
        );
        
    }
    
}
