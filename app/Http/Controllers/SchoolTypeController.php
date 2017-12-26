<?php
namespace App\Http\Controllers;

use App\Http\Requests\SchoolTypeRequest;
use App\Models\SchoolType;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 学校类型
 *
 * Class SchoolTypeController
 * @package App\Http\Controllers
 */
class SchoolTypeController extends Controller {
    
    function __construct() {
    
        $this->middleware(['auth', 'checkrole']);
        
    }
    
    /**
     * 学校类型列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(SchoolType::datatable());
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
     * @return Response
     */
    public function store(SchoolTypeRequest $request) {
        
        return $this->result(SchoolType::create($request->all()));
        
    }
    
    /**
     * 编辑学校类型
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $schoolType = SchoolType::find($id);
        if (!$schoolType) { return $this->notFound(); }
        
        return $this->output(['schoolType' => $schoolType]);
        
    }
    
    /**
     * 更新学校类型
     *
     * @param SchoolTypeRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(SchoolTypeRequest $request, $id) {
        
        $schoolType = SchoolType::find($id);
        if (!$schoolType) { return $this->notFound(); }
        
        return $this->result($schoolType->update($request->all()));
        
    }
    
    /**
     * 删除学校类型
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $schoolType = SchoolType::find($id);
        if (!$schoolType) { return $this->notFound(); }
        
        return $this->result($schoolType->delete());
        
    }
    
}
