<?php
namespace App\Http\Controllers;

use App\Http\Requests\GradeRequest;
use App\Models\Grade;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 年级
 *
 * Class GradeController
 * @package App\Http\Controllers
 */
class GradeController extends Controller {
    
    protected $grade;
    
    /**
     * GradeController constructor.
     * @param Grade $grade
     */
    function __construct(Grade $grade) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->approve($this->grade = $grade);
        
    }
    
    /**
     * 年级列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->grade->index())
            : $this->output();
        
    }
    
    /**
     * 创建年级
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存年级
     *
     * @param GradeRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(GradeRequest $request) {
        
        return $this->result(
            $this->grade->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑年级
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'grade' => Grade::find($id),
        ]);
        
    }
    
    /**
     * 更新年级
     *
     * @param GradeRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(GradeRequest $request, $id = null) {
        
        return $this->result(
            $this->grade->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除年级
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->grade->remove($id)
        );
        
    }
    
}
