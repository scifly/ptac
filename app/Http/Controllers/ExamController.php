<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExamRequest;
use App\Models\Exam;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    protected $exam;

    /**
     * ExamController constructor.
     * @param Exam $exam
     * @internal param Exam $examType
     */
    function __construct(Exam $exam) {
        $this->exam = $exam;

    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     * @internal param Request $request
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json($this->exam->datatable());
        }
        return view('exam.index' ,
            [
                'js' => 'js/exam/index.js',
                'dialog' => true,
                'datatable' => true,
            ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('exam.create',[
            'js' => 'js/exam/create.js',
            'form' => true
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ExamRequest $examRequest
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     */
    public function store(ExamRequest $examRequest)
    {
        // request
        $data['name'] = $examRequest->input('name');
        $data['remark'] = $examRequest->input('remark');
        $data['exam_type_id'] = $examRequest->input('exam_type_id');
        $classIds = $examRequest->input('class_ids');
        $data['class_ids'] = implode(',', $classIds);

        $subjectIds = $examRequest->input('subject_ids');
        $data['subject_ids'] = implode(',', $subjectIds);

        $data['max_scores'] = $examRequest->input('max_scores');
        $data['pass_scores'] = $examRequest->input('pass_scores');
        $data['start_date'] = $examRequest->input('start_date');
        $data['end_date'] = $examRequest->input('end_date');
        $data['enabled'] = $examRequest->input('enabled');
        $row = $this->exam->where(['exam_type_id' => $data['exam_type_id'], 'name' => $data['name']])->first();
        if(!empty($row)){
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '名称重复！';
        }else{
            if($this->exam->create($data))
            {
                $this->result['message'] = self::MSG_CREATE_OK;
            } else {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
                $this->result['message'] = '';
            }
        }

        return response()->json($this->result);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Http\Response
     */
    public function show(Exam $exam)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Exam $exam
     */
    public function edit($id)
    {
        $exam = $this->exam->whereId($id)->first();

        $class = explode(",", $exam->class_ids);
        $classes = DB::table('classes')
            ->whereIn('id', $class )
            ->get(['id','name']);
        $classIds = [];
        foreach ($classes as $value) {
            $classIds[$value->id] = $value->name;
        }

        $subject = explode(",", $exam->subject_ids);
        $subjects = DB::table('subjects')
            ->whereIn('id', $subject )
            ->get(['id','name']);
        $subjectIds = [];
        foreach ($subjects as $value) {
            $subjectIds[$value->id] = $value->name;
        }


        return view('exam.edit', [
            'js' => 'js/exam/edit.js',
            'exam' => $exam,
            'classIds' => $classIds,
            'subjectIds' => $subjectIds,
            'form' => true
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ExamRequest $examRequest
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     * @internal param Exam $exam
     */
    public function update(ExamRequest $examRequest, $id)
    {
        // request
        $data = Exam::find($id);
        $data->name = $examRequest->input('name');
        $data->remark = $examRequest->input('remark');
        $data->exam_type_id = $examRequest->input('exam_type_id');
        $classIds = $examRequest->input('class_ids');
        $data->class_ids = implode(',', $classIds);

        $subjectIds = $examRequest->input('subject_ids');
        $data->subject_ids = implode(',', $subjectIds);

        $data->max_scores = $examRequest->input('max_scores');
        $data->pass_scores = $examRequest->input('pass_scores');
        $data->start_date = $examRequest->input('start_date');
        $data->end_date = $examRequest->input('end_date');
        $data->enabled = $examRequest->input('enabled');
        $row = $this->exam->where(['exam_type_id' => $data->exam_type_id, 'name' => $data->name])->first();
        if(!empty($row) && $row->id != $id){

            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '名称重复！';

        }else{
            if($data->save())
            {
                $this->result['message'] = self::MSG_EDIT_OK;
            } else {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
                $this->result['message'] = '';

            }
        }

        return response()->json($this->result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Exam $exam
     */
    public function destroy($id)
    {
        if ($this->exam->findOrFail($id)->delete()) {
            $this->result['message'] = self::MSG_DEL_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
    }
}
