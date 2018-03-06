<?php
namespace App\Http\Controllers;

use App\Http\Requests\SchoolRequest;
use App\Models\School as School;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
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
    
    function __construct(School $school) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->school = $school;
        
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
                $this->school->datatable()
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
        
        $this->authorize(
            'cs', School::class
        );
        
        return $this->output();
        
    }
    
    /**
     * 保存学校
     *
     * @param SchoolRequest $request
     * @return JsonResponse|string
     * @throws AuthorizationException
     */
    public function store(SchoolRequest $request) {
    
        $this->authorize(
            'cs', School::class
        );
        
        return $this->result(
            $this->school->store(
                $request->all(), true
            )
        );
        
    }
    
    /**
     * 学校详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id) {
    
        $school = $this->school->find($id);
        $this->authorize('seu', $school);
        
        return $this->output([
            'school' => $school
        ]);
        
    }
    
    /**
     * 编辑学校
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $school = School::find($id);
        $this->authorize('seu', $school);
        
        return $this->output([
            'school' => $school,
        ]);
        
    }
    
    /**
     * 更新学校
     *
     * @param SchoolRequest $request
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(SchoolRequest $request, $id) {
        
        $school = School::find($id);
        $this->authorize('seu', $school);
        
        return $this->result(
            $school->modify($request->all(), $id, true)
        );
        
    }
    
    /**
     * 删除学校
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $school = School::find($id);
        
        return $this->result(
            $school->remove($id, true)
        );
        
    }
    
}
