<?php

namespace App\Http\Controllers;

use App\Http\Requests\GradeRequest;
use App\Models\Educator;
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
        $ids = $request->input('educator_ids');

        $data['name'] = $request->input('name');
        $data['school_id'] = $request->input('school_id');
        $data['educator_ids'] = implode(',', $ids);
        $data['enabled'] = $request->input('enabled');

        $row = $this->grade->where([
                    'school_id' => $data['school_id'],
                    'name' => $data['name']
                ])->first();
        if(!empty($row)){
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '年级名称重复！';
        }else{
            if($this->grade->create($data))
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

        return view('grade.show', [
            'grade' => $grade,
            'educators' => $educators
        ]);
    }

    /**
     * 显示编辑年级记录的表单
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $grade = $this->grade->whereId($id)->first();

        $educators = User::whereHas('educator' , function($query) use ($grade) {

            $f = explode(",", $grade->educator_ids);
            $query->whereIn('id', $f);

        })->get(['id','username'])->toArray();

        $educatorIds = [];
        foreach ($educators as $value) {
            $educatorIds[$value['id']] = $value['username'];
        }
        return view('grade.edit', [
            'js' => 'js/grade/edit.js',
            'grade' => $grade,
            'educatorIds' => $educatorIds,
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
        $row = $this->grade->where(['school_id' => $data->school_id, 'name' => $data->name])->first();
        if(!empty($row) && $row->id != $id){

            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '年级名称重复！';

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
     * 删除指定年级记录
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->grade->findOrFail($id)->delete()) {
            $this->result['message'] = self::MSG_DEL_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);

    }
}
