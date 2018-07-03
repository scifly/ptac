<?php
namespace App\Http\Controllers;

use App\Http\Requests\SchoolTypeRequest;
use App\Models\SchoolType;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 学校类型
 *
 * Class SchoolTypeController
 * @package App\Http\Controllers
 */
class SchoolTypeController extends Controller {
    
    protected $st;
    
    /**
     * SchoolTypeController constructor.
     * @param SchoolType $st
     */
    function __construct(SchoolType $st) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->st = $st;
        $this->approve($st);
        
    }
    
    /**
     * 学校类型列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->st->index()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建学校类型
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存学校类型
     *
     * @param SchoolTypeRequest $request
     * @return JsonResponse|string
     */
    public function store(SchoolTypeRequest $request) {
        
        return $this->result(
            $this->st->store($request->all())
        );
        
    }
    
    /**
     * 编辑学校类型
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'st' => $this->st->find($id),
        ]);
        
    }
    
    /**
     * 更新学校类型
     *
     * @param SchoolTypeRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(SchoolTypeRequest $request, $id = null) {
        
        return $this->result(
            $this->st->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除学校类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id = null) {
        
        return $this->result(
            $this->st->remove($id)
        );
        
    }
    
}