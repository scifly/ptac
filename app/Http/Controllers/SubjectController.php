<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubjectRequest;
use App\Models\Subject;
use Illuminate\Support\Facades\Request;

class SubjectController extends Controller
{
    protected $subject;

    /**
     * SubjectController constructor.
     * @param Subject $subject
     */
    function __construct(Subject $subject){ $this->subject = $subject; }

    /**
     * 显示科目列表.
     * @return \Illuminate\Http\Response
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json($this->subject->datatable());
        }
        return view('subject.index', [
            'js' => 'js/subject/index.js',
            'dialog' => true,
            'datatable' => true,
            'form'=>true,
            ]);

    }

    /**
     * 显示创建新的科目.
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('subject.create',[
            'js' => 'js/subject/create.js',
            'form' => true
        ]);
    }

    /**
     * 添加新科目
     * @param  \Illuminate\Http\Request  $requestid
     * @return \Illuminate\Http\Response
     */
    public function store(SubjectRequest $request){

        $data = $request->except('_token');
        $data['grade_ids'] = implode(',',$data['grade_ids']);
        if($this->subject->create($data)){
            return response()->json([
                'statusCode' => self::HTTP_STATUSCODE_OK, 'message' => self::MSG_CREATE_OK,
            ]);
        }else{
            return response()->json([
                'statusCode' => self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR,
                'message' => '添加失败'
            ]);

        }

    }

    /**
     *科目详情
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('subject.show', ['subject' => $this->subject->findOrFail($id)]);
    }

    /**
     * 编辑
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $subject = $this->subject->findOrFail($id)->toArray();

        return view('subject.edit', [
            'js' => 'js/subject/edit.js',
            'subject' => $subject,
            'form' => true
        ]);

    }

    /**
     * 更新科目.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */-
    public function update(SubjectRequest $request,$id)
    {
        $data = $request->all();
        $data['grade_ids'] = implode('|',$request->get('grade_ids'));

        if ($this->subject->findOrFail($id)->update($request->all())) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_EDIT_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '更新失败';
        }
        return response()->json($this->result);

    }

    /**
     * 删除科目.
     *
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->subject->findOrFail($id)->delete()) {
            $this->result['message'] = self::MSG_DEL_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '删除失败';
        }
        return response()->json($this->result);
    }

    /**
     * 根据条件查询科目.
     *
     * @param $school_id
     * @return \Illuminate\Http\Response
     * @internal param Subject $subject
     */
    public function query($school_id)
    {
        $subjects = $this->subject->where('school_id',$school_id)->get(['id','name']);
        if ($subjects) {
            return response()->json(['statusCode' => 200, 'subjects' => $subjects]);
        }else{
            return response()->json(['statusCode' => 500, 'message' => '查询失败!']);
        }
    }
}
