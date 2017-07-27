<?php

namespace App\Http\Controllers;

use App\Http\Requests\GradeRequest;
use App\Models\Grade;
use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\Request;


/**
 * @property array message
 */
class GradeController extends Controller
{
    protected $grade;

    function __construct(Grade $grade) {
        $this->grade = $grade;
        $this->message = [
            'statusCode' => 200,
            'message' => '!'
        ];
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     * @internal param Request $request
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json($this->grade->datatable());
        }
        return view('grade.index' ,
            [
                'js' => 'js/grade/index.js',
                'dialog' => true,
                'datatable' => true,
            ]);

    }


    /**
     * 显示创建年级的表单
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('grade.create',[
            'js' => 'js/grade/create.js',
            'form' => true
        ]);
    }

    /**
     * 保存新创建的年级记录
     * @param GradeRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(GradeRequest $request)
    {
        // request
        $data['name'] = $request->input('name');
        $data['school_id'] = $request->input('school_id');
        $ids = $request->input('educator_ids');
        $data['educator_ids'] = implode(',', $ids);
        $data['enabled'] = $request->input('enabled');

        if($this->grade->create($data))
        {
            $this->message = [
                'statusCode' => 200,
                'message' => '添加成功!'
            ];

        }else{
            $this->message = [
                'statusCode' => 200,
                'message' => '添加失败!'
            ];

        }
        return response()->json($this->message);

    }

    /**
     * 显示年级记录详情
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $grade = Grade::whereId($id)->first();
        $educators = User::whereHas('educator' , function($query) use ($grade) {

                $f = explode(",", $grade->educator_ids);
                $query->whereIn('id', $f);

        })->get(['id','username'])->toArray();
        return view('grade.show', ['grade' => $grade, 'educators' => $educators]);
    }

    /**
     * 显示编辑年级记录的表单
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $grade = Grade::whereId($id)->first();
        return view('grade.edit', [
            'js' => 'js/grade/edit.js',
            'grade' => $grade,
            'form' => true
        ]);
    }

    /**
     * 更新指定年级记录
     * @param GradeRequest $gradeRequest
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     */
    public function update(GradeRequest $gradeRequest ,$id)
    {
        // find the record by id
        // update the record with the request data
        $data = Grade::find($id);

        $data->name = $gradeRequest->input('name');
        $data->school_id = $gradeRequest->input('school_id');
        $ids = $gradeRequest->input('educator_ids');
        $data->educator_ids = implode(',', $ids);
        $data->enabled = $gradeRequest->input('enabled');

        if($data->save())
        {
            $this->message = [
                'statusCode' => 200,
                'message' => '添加成功!'
            ];

        }else{
            $this->message = [
                'statusCode' => 200,
                'message' => '添加失败!'
            ];

        }
        return response()->json($this->message);
    }

    /**
     * 删除指定年级记录
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $res = $this->grade->findOrFail($id)->delete();
        if (!$res) {
            $this->message = [
                'statusCode' => 200,
                'message' => '删除成功!'
            ];

        }else{
            $this->message = [
                'statusCode' => 200,
                'message' => '删除失败!'
            ];

        }
        return response()->json($this->message);

    }
}
