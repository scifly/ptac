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
        return view('educator_class.index', [
            'js' => 'js/educator_class/index.js',
            'dialog' => true,
            'datatable' => true,
            'form'=>true,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('educator_class.create',[
            'js' => 'js/educator_class/create.js',
            'form' => true,
        ]);
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
        }else{
            if($this->educatorClass->create($data)){
                $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
                $this->result['message'] = self::MSG_CREATE_OK;

            }else{
                $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
                $this->result['message'] = '添加失败';
            }
        }


        return response()->json($this->result);
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
        $educatorClass = $this->educatorClass->findOrFail($id)->toArray();
        return view('educator_class.edit', [
            'js' => 'js/educator_class/edit.js',
            'educatorClass' => $educatorClass,
            'form' => true
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EducatorClass  $educatorClass
     * @return \Illuminate\Http\Response
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
     * @param  \App\Models\EducatorClass  $educatorClass
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->educatorClass->findOrFail($id)->delete()) {
            $this->result['message'] = self::MSG_DEL_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '删除失败';
        }
        return response()->json($this->result);
    }
}
