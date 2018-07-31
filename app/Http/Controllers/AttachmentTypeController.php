<?php
namespace App\Http\Controllers;

use App\Http\Requests\AttachmentTypeRequest;
use App\Models\AttachmentType;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 附件类型
 *
 * Class AttachmentTypeController
 * @package App\Http\Controllers
 */
class AttachmentTypeController extends Controller {
    
    protected $at;
    
    /**
     * AttachmentTypeController constructor.
     * @param AttachmentType $at
     */
    function __construct(AttachmentType $at) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->at = $at;
        $this->approve($at);
        
    }
    
    /**
     * 附件类型列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->at->index()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建附件类型
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存附件类型
     *
     * @param AttachmentTypeRequest $request
     * @return JsonResponse|string
     */
    public function store(AttachmentTypeRequest $request) {
        
        return $this->result(
            $this->at->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑附件类型
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'at' => $this->at->find($id),
        ]);
        
    }
    
    /**
     * 更新附件类型
     *
     * @param AttachmentType $request
     * @param $id
     * @return JsonResponse|string
     */
    public function update(AttachmentType $request, $id) {
        
        return $this->result(
            $this->at->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除附件类型
     *
     * @param $id
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->at->remove($id)
        );
        
    }
    
}
