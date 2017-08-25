<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubjectRequest;
use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Support\Facades\Request;

class SubjectController extends Controller {
    
    protected $subject;
    
    /**
     * SubjectController constructor.
     * @param Subject $subject
     */
    function __construct(Subject $subject) { $this->subject = $subject; }
    
    /**
     * 显示科目列表.
     * @return \Illuminate\Http\Response
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->subject->datatable());
        }
        return parent::output(__METHOD__);
        
    }
    
    /**
     * 显示创建新的科目.
     * @return \Illuminate\Http\Response
     */
    public function create() {

        return parent::output(__METHOD__);
    }
    
    /**
     * 添加新科目
     * @param SubjectRequest $request
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     */
    public function store(SubjectRequest $request) {
        
        $data = $request->except('_token');
        if ($this->subject->existed($request)) {
            return $this->fail('已经有此记录');
        }
        return $this->subject->create($data) ? parent::succeed() : parent::fail();
        
    }
    
    /**
     *科目详情
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Subject $subject
     */
    public function show($id) {
        $subjects = $this->subject->whereId($id)
            ->first(['name','school_id','isaux','max_score','pass_score','enabled']);
        $subjects->school_id = $subjects->school->name;
        $subjects->isaux = $subjects->isaux==1 ? '是' : '否' ;
        $subjects->enabled = $subjects->enabled==1 ? '已启用' : '已禁用' ;
        if ($subjects) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['showData'] = $subjects;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
//        return view('subject.show', ['subject' => $this->subject->findOrFail($id)]);
        
    }
    
    /**
     * 编辑
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Subject $subject
     */
    public function edit($id) {
        
        $subject = $this->subject->findOrFail($id)->toArray();
        $ids = explode(',', $subject['grade_ids']);
        $selectedGrades = [];
        foreach ($ids as $id) {
            $grade = Grade::whereId($id)->first();
            $selectedGrades[$id] = $grade['name'];
        }
        if (!$subject) { return parent::notFound(); }
        return parent::output(__METHOD__, [
            'subject' => $subject,
            'selectedGrades' => $selectedGrades
        ]);

    }

    /**
     * 更新科目.
     *
     * @param SubjectRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param $SubjectRequest
     * @internal param Subject $subject
     */
    public function update(SubjectRequest $request, $id) {
        $subject = $this->subject->find($id);
        if (!$subject) { return $this->notFound(); }
        if ($this->subject->existed($request, $id)) {
            return $this->fail('已经有此记录');
        }
        return $subject->update($request->all()) ? $this->succeed() : $this->fail();
    }
    
    /**
     * 删除科目.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Subject $subject
     */
    public function destroy($id)
    {
        $subject = $this->subject->find($id);
        if (!$subject) { return parent::notFound(); }
        return $subject->delete() ? parent::succeed() : parent::fail();

    }
    
    /**
     * 根据条件查询科目
     * @param $school_id
     * @return \Illuminate\Http\Response
     * @internal param Subject $subject
     */
    public function query($school_id) {
        $subjects = $this->subject->where('school_id', $school_id)->get(['id', 'name']);
        if ($subjects) {
            return response()->json(['statusCode' => 200, 'subjects' => $subjects]);
        } else {
            return response()->json(['statusCode' => 500, 'message' => '查询失败!']);
        }
    }
}
