<?php
namespace App\Http\Controllers;

use App\Http\Requests\AttachmentTypeRequest;
use App\Models\AttachmentType;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
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
    
    function __construct(AttachmentType $at) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->at = $at;
        
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
                $this->at->datatable()
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
        
        $this->authorize(
            'create', AttachmentType::class
        );
        
        return $this->output();
        
    }
    
    /**
     * 保存附件类型
     *
     * @param AttachmentTypeRequest $request
     * @return JsonResponse|string
     * @throws AuthorizationException
     */
    public function store(AttachmentTypeRequest $request) {
        
        $this->authorize(
            'store', AttachmentType::class
        );
        
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
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function edit($id) {
        
        $at = $this->at->find($id);
        $this->authorize('edit', $at);
        
        return $this->output([
            'mt' => $at,
        ]);
        
    }
    
    /**
     * 更新附件类型
     *
     * @param AttachmentType $request
     * @param $id
     * @return JsonResponse|string
     * @throws AuthorizationException
     */
    public function update(AttachmentType $request, $id) {
        
        $at = $this->at->find($id);
        $this->authorize('update', $at);
        
        return $this->result(
            $at->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除附件类型
     *
     * @param $id
     * @return JsonResponse|string
     * @throws AuthorizationException
     * @throws Exception
     */
    public function destroy($id) {
        
        $at = $this->at->find($id);
        $this->authorize('destroy', $at);
        
        return $this->result(
            $at->remove($id)
        );
        
    }
    
}
