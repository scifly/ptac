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
     * @param ExamTypeRequest $examTypeRequest
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     */
    public function store(ExamTypeRequest $examTypeRequest)
    {
        // request
        $data = $examTypeRequest->all();
        $row = $this->examType->where(['name' => $data['name']])->first();
        if(!empty($row) ){

            return $this->fail('名称重复！');
        }else{

            return $this->examType->create($data) ? $this->succeed() : $this->fail();
        }
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
        $examType = $this->examType->find($id);
        if (!$examType) { return parent::notFound(); }
        return parent::output(__METHOD__, [
            'examType' => $examType,
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param ExamType $examType
     */
    public function edit($id)
    {
        $examType = $this->examType->find($id);

        if (!$examType) { return parent::notFound(); }
        return parent::output(__METHOD__, [
            'examType' => $examType,
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

        if (!$data) { return parent::notFound(); }

        $data->name = $examTypeRequest->input('name');
        $data->remark = $examTypeRequest->input('remark');
        $data->enabled = $examTypeRequest->input('enabled');
        $row = $this->examType->where(['name' => $data->name])->first();
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
     * @internal param ExamType $examType
     */
    public function destroy($id)
    {
        $examType = $this->examType->find($id);

        if (!$examType) { return parent::notFound(); }
        return $examType->delete() ? parent::succeed() : parent::fail();
    }
}
