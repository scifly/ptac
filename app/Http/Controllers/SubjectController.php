<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubjectRequest;
use App\Models\Subject;
use Illuminate\Support\Facades\Request;

class SubjectController extends Controller
{
    protected $subject;

    protected $message;
    /**
     * SubjectController constructor.
     * @param Subject $subject
     */
    function __construct(Subject $subject){
        $this->subject = $subject;
        $this->message = [
            'statusCode' => 200,
            'message' => ''
        ];

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json($this->subject->datatable());
        }
        return view('subject.index', [
            'js' => 'js/subject/index.js',
            'dialog' => true
            ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('subject.create',['js' => 'js/subject/create.js']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $requestid
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $data = Request::except('_token');
        $data = Request::all();

        if($data !=null){
            $grade = $data['grade_ids'];
            $data['grade_ids'] = implode('|',$grade);

            $res = Subject::create($data);

            if (!$res) {
                return response()->json(['statusCode' => 202, 'message' => 'add filed']);
            }
            return response()->json(['statusCode' => 200, 'message' => 'nailed it!']);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $subject = Subject::where('id', $id);
        return view('subject.show', ['subject' => $subject]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $subject = Subject::whereId($id)->first();

        return view('subject.edit', [
            'js' => 'js/subject/edit.js',
            'subject' => $subject
        ]);

    }

    /**
     * 更新科目.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function update(SubjectRequest $request,$id)
    {
        $subject = Subject::find($id);
        $subject->name = $request->get('name');
        $subject->max_score = $request->get('max_score');
        $subject->pass_score = $request->get('pass_score');
        $subject->isaux = $request->get('isaux');
        $subject->grade_ids = implode('|',$request->get('grade_ids'));

        $subject->enabled = $request->get('enabled');

        $res = $subject->save();
        if (!$res) {
            $this->message['statusCode'] = 202;
            $this->message['message'] = 'add filed';
        } else {
            $this->message['statusCode'] = 200;
            $this->message['message'] = 'nailed it!';
        }
        return response()->json($this->message);

    }

    /**
     * 删除科目.
     *
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $res = Subject::destroy($id);
        if (!$res) {
            return response()->json(['statusCode' => 202, 'message' => 'add filed']);
        }
        return response()->json(['statusCode' => 200, 'message' => 'nailed it!']);
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
