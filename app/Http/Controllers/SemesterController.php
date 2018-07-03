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
    
    /**
     * SemesterController constructor.
     * @param Semester $semester
     */
    function __construct(Semester $semester) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->semester = $semester;
        $this->approve($semester);
        
    }
    
    /**
     * 学期列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->semester->index()
            );
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
     * @return JsonResponse|string
     */
    public function store(SemesterRequest $request) {
        
        return $this->result(
            $this->semester->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑学期
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'semester' => $this->semester->find($id),
        ]);
        
    }
    
    /**
     * 更新学期
     *
     * @param SemesterRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(SemesterRequest $request, $id) {
        
        return $this->result(
            $this->semester->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除学期
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        return $this->result(
            $this->semester->remove($id)
        );
        
    }
    
}
