<?php
namespace App\Http\Controllers;

use App\Http\Requests\SubjectRequest;
use App\Models\Grade;
use App\Models\Major;
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
    
    protected $subject, $major, $grade;
    
    function __construct(Subject $subject, Major $major, Grade $grade) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->subject = $subject;
        $this->major = $major;
        $this->grade = $grade;
        $this->approve($subject);
        
    }
    
    /**
     * 科目列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->subject->datatable()
            );
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
        
        return $this->output([
            'majors' => $this->major->majorList(),
            'grades' => $this->grade->gradeList(),
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
        
        return $this->result(
            $this->subject->store($request)
        );
        
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
            'majors'         => $this->major->majorList(),
            'grades'         => $this->grade->gradeList(),
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
        
        return $this->result(
            $this->subject->modify($request, $id)
        );
        
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
        
        return $this->result(
            $this->subject->remove($id)
        );
        
    }
    
}
