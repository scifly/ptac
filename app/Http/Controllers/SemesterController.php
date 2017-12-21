<?php
namespace App\Http\Controllers;

use App\Http\Requests\SemesterRequest;
use App\Models\Semester;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 学期
 *
 * Class SemesterController
 * @package App\Http\Controllers
 */
class SemesterController extends Controller {
    
    protected $semester;
    
    function __construct(Semester $semester) {
    
        $this->middleware(['auth']);
        $this->semester = $semester;
    
    }
    
    /**
     * 学期列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        if (Request::get('draw')) {
            return response()->json($this->semester->datatable());
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建学期
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        return $this->output();
        
    }
    
    /**
     * 保存学期
     *
     * @param SemesterRequest $request
     * @return JsonResponse
     */
    public function store(SemesterRequest $request) {
        return $this->semester->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
   
    /**
     * 编辑学期
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        $semester = $this->semester->find($id);
        if (!$semester) {
            return $this->notFound();
        }
        
        return $this->output(['semester' => $semester]);
        
    }
    
    /**
     * 更新学期
     *
     * @param SemesterRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(SemesterRequest $request, $id) {
        $semester = $this->semester->find($id);
        if (!$semester) {
            return $this->notFound();
        }
        
        return $semester->update($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除学期
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        $semester = $this->semester->find($id);
        if (!$semester) { return $this->notFound(); }
        
        return $semester->delete() ? $this->succeed() : $this->fail();
        
    }
    
}
