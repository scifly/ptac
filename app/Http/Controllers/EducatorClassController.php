<?php

namespace App\Http\Controllers;

use App\Http\Requests\EducatorClassRequest;

use App\Models\Educator;
use App\Models\EducatorClass;

use Illuminate\Support\Facades\Request;

class EducatorClassController extends Controller
{
    protected $educatorClass;

    protected $educator;


    /**
     * SubjectModulesController constructor.
     * @param EducatorClass $educatorClass
     * @param Educator $educator
     */
    function __construct(EducatorClass $educatorClass, Educator $educator)
    {
        $this->educatorClass = $educatorClass;
        $this->educator = $educator;
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Request::get('draw')) {
            return response()->json($this->educatorClass->datatable());
        }
        return parent::output(__METHOD__);
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return parent::output(__METHOD__);
    }

    /**
     *添加.
     *
     * @param EducatorClassRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(EducatorClassRequest $request)
    {
        $data = $request->all();
        $result = $this->educatorClass
            ->where('class_id',$data['class_id'])
            ->where('subject_id',$data['subject_id'])
            ->get()
            ->toArray();
        if($result!=null)
        {
            $this->result['statusCode'] = self::MSG_BAD_REQUEST;
            $this->result['message'] = '该条数据已经存在!';
            return response()->json($this->result);
        }else{
            return $this->educatorClass->create($data) ? $this->succeed() : $this->fail();
        }



    }

    /**
     * Display the specified resource.
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param EducatorClass $educatorClass
     */
    public function show($id)
    {
        return view('educator_class.show', ['educatorClass' => $this->educatorClass->findOrFail($id)]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param EducatorClass $educatorClass
     */
    public function edit($id)
    {
        $educatorClass = $this->educatorClass->find($id);
        if (!$educatorClass) { return $this->notFound(); }
        return $this->output(__METHOD__, ['educatorClass' => $educatorClass]);

    }

    /**
     * @param EducatorClassRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(EducatorClassRequest $request, $id)
    {
        $data = $request->except('_token');

        $result = $this->educatorClass
            ->where('class_id',$data['class_id'])
            ->where('subject_id',$data['subject_id'])
            ->first();

        if(!empty($result)&&($result->educator_id!= $data['educator_id']))
        {
            $this->result['statusCode'] = self::MSG_BAD_REQUEST;
            $this->result['message'] = '同一个班级的同一个科目不能有两个老师教!';

        }else{
            if ($this->educatorClass->findOrFail($id)->update($data))
            {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
                $this->result['message'] = self::MSG_EDIT_OK;
            } else {
                $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
                $this->result['message'] = '更新失败';
            }
        }

        return response()->json($this->result);

    }

    /**
     * 删除教职员工
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param EducatorClass $educatorClass
     */
    public function destroy($id)
    {
        $educatorClass = $this->educatorClass->find($id);
        if (!$educatorClass) { return $this->notFound(); }
        return $educatorClass->delete() ? $this->succeed() : $this->fail();
    }
}
