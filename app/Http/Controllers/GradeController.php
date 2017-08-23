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
        return $this->output(__METHOD__);

//        if (Request::get('draw')) {
//            return response()->json($this->grade->datatable());
//        }
//        return view('grade.index' ,
//            [
//                'js' => 'js/grade/index.js',
//                'dialog' => true,
//                'datatable' => true,
//            ]);

    }


    /**
     * 显示创建年级的表单
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->output(__METHOD__);

//        return view('grade.create',[
//            'js' => 'js/grade/create.js',
//            'form' => true
//        ]);
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
        if(!empty($row) ){

            $this->fail('年级名称重复！');
        }else{

            return $this->grade->create($data) ? $this->succeed() : $this->fail();
        }


    }

    /**
     * 显示年级记录详情
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $educators = User::whereHas('educator' , function($query) use ($grade) {

                $f = explode(",", $grade->educator_ids);
                $query->whereIn('id', $f);

        })->get(['id','username'])->toArray();
        $grade = $this->grade->find($id);

        if (!$grade) { return parent::notFound(); }
        return parent::output(__METHOD__, [
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
        $grade = $this->grade->find($id);

        if (!$grade) { return parent::notFound(); }

        $educators = User::whereHas('educator' , function($query) use ($grade) {

            $f = explode(",", $grade->educator_ids);
            $query->whereIn('id', $f);

        })->get(['id','username'])->toArray();

        $selectedEducators = [];
        foreach ($educators as $value) {
            $selectedEducators[$value['id']] = $value['username'];
        }

        return parent::output(__METHOD__, [
            'grade' => $grade,
            'selectedEducators' => $selectedEducators,
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

        if (!$data) { return parent::notFound(); }

        $ids = $gradeRequest->input('educator_ids');

        $data->name = $gradeRequest->input('name');
        $data->school_id = $gradeRequest->input('school_id');
        $data->educator_ids = implode(',', $ids);
        $data->enabled = $gradeRequest->input('enabled');
        $row = $this->grade->where([
                'school_id' => $data->school_id,
                'name' => $data->name
            ])->first();
        if(!empty($row) && $row->id != $id){

            $this->fail('年级名称重复！');
        }else{

            return $data->save() ? $this->succeed() : $this->fail();
        }

    }

    /**
     * 删除指定年级记录
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $grade = $this->grade->find($id);

        if (!$grade) { return parent::notFound(); }
        return $grade->delete() ? parent::succeed() : parent::fail();

    }
}
