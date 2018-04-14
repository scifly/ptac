<?php
namespace App\Http\Controllers;

use App\Http\Requests\CommTypeRequest;
use App\Models\CommType;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
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
    
    protected $ct;
    
    function __construct(CommType $ct) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->ct = $ct;
        
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
                $this->ct->datatable()
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
        
        $this->authorize(
            'create',
            CommType::class
        );
        
        return $this->output();
        
    }
    
    /**
     * 保存通信方式
     *
     * @param CommTypeRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(CommTypeRequest $request) {
        
        $this->authorize(
            'store',
            CommType::class
        );
        
        return $this->result(
            $this->ct->store(
                $request->all()
            )
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
        
        $ct = $this->ct->find($id);
        $this->authorize('edit', $ct);
        
        return $this->output([
            'ct' => $ct,
        ]);
        
    }
    
    /**
     * 更新通信方式
     *
     * @param CommTypeRequest $request
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(CommTypeRequest $request, $id) {
        
        $ct = $this->ct->find($id);
        $this->authorize('update', $ct);
        
        return $this->result(
            $ct->update($request->all())
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
        
        $ct = $this->ct->find($id);
        $this->authorize('destroy', $ct);
        
        return $this->result(
            $ct->delete()
        );
        
    }
    
}
