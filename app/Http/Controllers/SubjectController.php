<?php
namespace App\Http\Controllers;

use App\Http\Requests\SubjectRequest;
use App\Models\Grade;
use App\Models\Major;
use App\Models\MajorSubject;
use App\Models\Subject;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 科目
 *
 * Class SubjectController
 * @package App\Http\Controllers
 */
class SubjectController extends Controller {
    
    function __construct() {
        
        $this->middleware(['auth', 'checkrole']);
        
    }
    
    /**
     * 科目列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(Subject::datatable());
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建科目
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        $this->authorize('c', Subject::class);
        
        return $this->output([
            'majors' => Major::majors(),
            'grades' => Grade::grades(),
        ]);
        
    }
    
    /**
     * 保存科目
     *
     * @param SubjectRequest $request
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function store(SubjectRequest $request) {
        
        $this->authorize('c', Subject::class);
        
        return $this->result(Subject::store($request));
        
    }
    
    /**
     * 编辑科目
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $subject = Subject::find($id);
        $this->authorize('rud', $subject);
        
        $gradeIds = explode(',', $subject['grade_ids']);
        $selectedGrades = [];
        foreach ($gradeIds as $gradeId) {
            $grade = Grade::find($gradeId);
            $selectedGrades[$gradeId] = $grade['name'];
        }
        $selectedMajors = [];
        foreach ($subject->majors as $major) {
            $selectedMajors[$major->id] = $major->name;
        }
        
        return $this->output([
            'subject'        => $subject,
            'selectedGrades' => $selectedGrades,
            'selectedMajors' => $selectedMajors,
            'majors'         => Major::majors(),
            'grades'         => Grade::grades(),
        ]);
        
    }
    
    /**
     * 更新科目
     *
     * @param SubjectRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function update(SubjectRequest $request, $id) {
        
        $subject = Subject::find($id);
        $this->authorize('rud', $subject);
        
        return $this->result($subject->modify($request, $id));
        
    }
    
    /**
     * 删除科目
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function destroy($id) {
        
        $subject = Subject::find($id);
        $this->authorize('rud', $subject);
        
        return $this->result($subject->remove($id));
        
    }
    
}
