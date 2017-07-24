<?php

namespace App\Http\Controllers;

use App\Http\Requests\GradeRequest;
use App\Models\Grade;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;

class GradeController extends Controller
{
    protected $grade;

    function __construct(Grade $grade) { $this->grade = $grade; }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     * @internal param Request $request
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json($this->grade->datatable());
        }
        return view('grade.index' , ['js' => 'js/grade/index.js']);

    }


    /**
     * 显示创建年级的表单
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('grade.create',['js' => 'js/grade/create.js']);
    }

    /**
     * 保存新创建的年级记录
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request|Request $request
     */
    public function store(GradeRequest $gradeRequest)
    {
        // request
        $data = $gradeRequest->input('name');


        return response()->json(['statusCode' => 200, 'Message' => 'nailed it!']);
    }

    /**
     * 显示年级记录详情
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        // find the record by id
//        return view('company.show', ['company' => $company]);
    }

    /**
     * 显示编辑年级记录的表单
     * @return \Illuminate\Http\Response
     */
    public function edit() {

        return view('grade.edit', ['js' => 'js/grade/edit.js']);

    }

    /**
     * 更新指定年级记录
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     */
    public function update()
    {
        // find the record by id
        // update the record with the request data
        return response()->json([]);
    }

    /**
     * 删除指定年级记录
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        return response()->json([]);
    }
}
