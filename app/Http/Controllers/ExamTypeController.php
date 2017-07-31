<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExamTypeRequest;
use App\Models\ExamType;
use Illuminate\Support\Facades\Request;


class ExamTypeController extends Controller
{
    protected $examType;

    function __construct(ExamType $examType) {
        $this->examType = $examType;

    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     * @internal param Request $request
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json($this->examType->datatable());
        }
        return view('exam_type.index' ,
            [
                'js' => 'js/exam_type/index.js',
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
        return view('exam_type.create',[
            'js' => 'js/exam_type/create.js',
            'form' => true
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ExamTypeRequest $examTypeRequest
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     */
    public function store(ExamTypeRequest $examTypeRequest)
    {
        // request
        $data = $examTypeRequest->all();
        $row = $this->examType->where(['name' => $data['name']])->first();
        if(!empty($row)){
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '名称重复！';
        }else{
            if($this->examType->create($data))
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
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param ExamType $examType
     */
    public function show($id)
    {
        $examType = ExamType::whereId($id)->first();

        return view('exam_type.show', [
            'examType' => $examType,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ExamType  $examType
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $examType = $this->examType->whereId($id)->first();


        return view('exam_type.edit', [
            'js' => 'js/exam_type/edit.js',
            'examType' => $examType,
            'form' => true

        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ExamTypeRequest $examTypeRequest
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     * @internal param ExamType $examType
     */
    public function update(ExamTypeRequest $examTypeRequest, $id)
    {
        $data = ExamType::find($id);

        $data->name = $examTypeRequest->input('name');
        $data->remark = $examTypeRequest->input('remark');
        $data->enabled = $examTypeRequest->input('enabled');
        $row = $this->examType->where(['name' => $data->name])->first();
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
     * @internal param ExamType $examType
     */
    public function destroy($id)
    {
        if ($this->examType->findOrFail($id)->delete()) {
            $this->result['message'] = self::MSG_DEL_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
    }
}
