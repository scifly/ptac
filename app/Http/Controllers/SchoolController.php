<?php
namespace App\Http\Controllers;

use App\Http\Requests\SchoolRequest;
use App\Models\School as School;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 学校
 *
 * Class SchoolController
 * @package App\Http\Controllers
 */
class SchoolController extends Controller {
    
    protected $school;
    
    /**
     * SchoolController constructor.
     * @param School $school
     */
    function __construct(School $school) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->school = $school;
        if (!Request::has('ids')) {
            $this->approve($school);
        }
        
    }
    
    /**
     * 学校列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->school->index()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建学校
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存学校
     *
     * @param SchoolRequest $request
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function store(SchoolRequest $request) {
        
        return $this->result(
            $this->school->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑学校
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'school' => School::find($id),
        ]);
        
    }
    
    /**
     * 更新学校
     *
     * @param SchoolRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(SchoolRequest $request, $id = null) {
        
        return $this->result(
            $this->school->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除学校
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function destroy($id = null) {
        
        return $this->result(
            $this->school->remove($id)
        );
        
    }
    
}
