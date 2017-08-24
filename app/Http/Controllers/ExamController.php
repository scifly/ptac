<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExamRequest;
use App\Models\Exam;
use App\Models\Squad;
use App\Models\Subject;
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
        return $this->output(__METHOD__);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->output(__METHOD__);
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

        if(!empty($row) ){

            return $this->fail('名称重复！');
        }else{

            return $this->exam->create($data) ? $this->succeed() : $this->fail();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $exam = $this->exam->find($id);
        if (!$exam) { return parent::notFound(); }

        $classIds = explode(",", $exam->class_ids);
        $classes = Squad::whereIn('id', $classIds )
            ->get(['id','name']);
        $subjectIds = explode(",", $exam->subject_ids);
        $subjects = DB::table('subjects')
            ->whereIn('id', $subjectIds )
            ->get(['id','name']);
        return parent::output(__METHOD__, [
            'exam' => $exam,
            'classes' => $classes,
            'subjects' => $subjects,
        ]);

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
        $exam = $this->exam->find($id);

        if (!$exam) { return parent::notFound(); }

        $class = explode(",", $exam->class_ids);
        $classes = Squad::whereIn('id', $class )
            ->get(['id','name']);
        $selectedClasses = [];
        foreach ($classes as $value) {
            $selectedClasses[$value->id] = $value->name;
        }

        $subject = explode(",", $exam->subject_ids);
        $subjects = Subject::whereIn('id', $subject )
            ->get(['id','name']);
        $selectedSubjects = [];
        foreach ($subjects as $value) {
            $selectedSubjects[$value->id] = $value->name;
        }

        return parent::output(__METHOD__, [
            'exam' => $exam,
            'selectedClasses' => $selectedClasses,
            'selectedSubjects' => $selectedSubjects,
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

        if (!$data) { return parent::notFound(); }

        $classIds = $examRequest->input('class_ids');
        $subjectIds = $examRequest->input('subject_ids');

        $data->name = $examRequest->input('name');
        $data->remark = $examRequest->input('remark');
        $data->exam_type_id = $examRequest->input('exam_type_id');
        $data->class_ids = implode(',', $classIds);
        $data->subject_ids = implode(',', $subjectIds);
        $data->max_scores = $examRequest->input('max_scores');
        $data->pass_scores = $examRequest->input('pass_scores');
        $data->start_date = $examRequest->input('start_date');
        $data->end_date = $examRequest->input('end_date');
        $data->enabled = $examRequest->input('enabled');
        $row = $this->exam->where([
                'exam_type_id' => $data->exam_type_id,
                'name' => $data->name
            ])->first();
        if(!empty($row) && $row->id != $id){

            return $this->fail('名称重复！');
        }else{

            return $data->save() ? $this->succeed() : $this->fail();
        }
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
        $exam = $this->exam->find($id);

        if (!$exam) { return parent::notFound(); }
        return $exam->delete() ? parent::succeed() : parent::fail();
    }
}
