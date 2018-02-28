<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
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
    
    function __construct(SchoolType $st) {
    
        $this->middleware(['auth', 'checkrole']);
        $this->st = $st;
        
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
                $this->st->datatable()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建学校类型
     *
     * @return bool|\Illuminate\Http\JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存学校类型
     *
     * @param SchoolTypeRequest|\Illuminate\Http\Request $request
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
     * @return bool|\Illuminate\Http\JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $st = SchoolType::find($id);
        abort_if(!$st, HttpStatusCode::NOT_FOUND);
        
        return $this->output(['st' => $st]);
        
    }
    
    /**
     * 更新学校类型
     *
     * @param SchoolTypeRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(SchoolTypeRequest $request, $id) {
        
        $st = SchoolType::find($id);
        abort_if(!$st, HttpStatusCode::NOT_FOUND);
        
        return $this->result(
            $st->modify($request->all(), $id)
        );
        
    }
    
    /**
     * 删除学校类型
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $st = SchoolType::find($id);
        abort_if(!$st, HttpStatusCode::NOT_FOUND);
        
        return $this->result(
            $st->remove($id)
        );
        
    }
    
}
