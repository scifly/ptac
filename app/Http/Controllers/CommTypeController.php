<?php
namespace App\Http\Controllers;

use App\Http\Requests\CommTypeRequest;
use App\Models\CommType;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 通信方式
 *
 * Class CommTypeController
 * @package App\Http\Controllers
 */
class CommTypeController extends Controller {
    
    function __construct() {
    
        $this->middleware(['auth', 'checkrole']);
        
    }
    
    /**
     * 通信方式列表
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                CommType::datatable()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建通信方式
     *
     * @return JsonResponse
     * @throws \Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存通信方式
     *
     * @param CommTypeRequest $request
     * @return JsonResponse
     */
    public function store(CommTypeRequest $request) {
        
        return $this->result(
            CommType::create($request->all())
        );
        
    }
    
    /**
     * 编辑通信方式
     *
     * @param $id
     * @return JsonResponse
     * @throws \Throwable
     */
    public function edit($id) {
        
        $commType = CommType::find($id);
        if (!$commType) { return $this->notFound(); }
        
        return $this->output([
            'commType' => $commType
        ]);
        
    }
    
    /**
     * 更新通信方式
     *
     * @param CommTypeRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(CommTypeRequest $request, $id) {
        
        $commType = CommType::find($id);
        if (!$commType) { return $this->notFound(); }
        
        return $this->result(
            $commType->update($request->all())
        );
        
    }
    
    /**
     * 删除通信方式
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $commType = CommType::find($id);
        if (!$commType) { return $this->notFound(); }
        
        return $this->result(
            $commType->delete()
        );
        
    }
    
}
