<?php
namespace App\Http\Controllers;

use App\Http\Requests\MajorRequest;
use App\Models\Major;
use App\Models\Subject;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 专业
 *
 * Class MajorController
 * @package App\Http\Controllers
 */
class MajorController extends Controller {
    
    protected $major, $subject;
    
    function __construct(Major $major, Subject $subject) {
    
        $this->middleware(['auth']);
        $this->major = $major;
        $this->subject = $subject;
        
    }
    
    /**
     * 专业列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->major->datatable());
        }
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 创建专业
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output(__METHOD__, [
            'subjects' => $this->subject->subjects(1),
        ]);
        
    }
    
    /**
     * 保存专业
     *
     * @param MajorRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(MajorRequest $request) {
        
        return $this->major->store($request) ? $this->succeed() : $this->fail();
        
    }

    /**
     * 编辑专业
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $major = $this->major->find($id);
        if (!$major) {
            return $this->notFound();
        }
        $majorSubjects = $major->subjects;
        $selectedSubjects = [];
        foreach ($majorSubjects as $subject) {
            $selectedSubjects[$subject->id] = $subject->name;
        }
        
        return $this->output(__METHOD__, [
            'major'            => $major,
//            'subjects' => $this->subject->subjects(1),
            'selectedSubjects' => $selectedSubjects,
        ]);
        
    }
    
    /**
     * 更新专业
     *
     * @param MajorRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function update(MajorRequest $request, $id) {
        
        $major = $this->major->find($id);
        if (!$major) { return $this->notFound(); }
        
        return $major->modify($request, $id) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除专业
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $major = $this->major->find($id);
        if (!$major) { return $this->notFound(); }
        
        return $major->remove($id) ? $this->succeed() : $this->fail();
        
    }
    
}
