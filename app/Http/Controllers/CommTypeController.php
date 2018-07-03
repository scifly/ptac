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
    
    protected $ct;
    
    /**
     * CommTypeController constructor.
     * @param CommType $ct
     */
    function __construct(CommType $ct) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->ct = $ct;
        $this->approve($ct);
        
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
                $this->ct->index()
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
        
        return $this->output([
            'ct' => $this->ct->find($id),
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
        
        return $this->result(
            $this->ct->modify(
                $request->all(), $id
            )
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
        
        return $this->result(
            $this->ct->remove($id)
        );
        
    }
    
}
