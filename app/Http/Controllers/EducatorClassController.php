<?php

namespace App\Http\Controllers;

use App\Http\Requests\EducatorClassRequest;

use App\Models\EducatorClass;
use Illuminate\Support\Facades\Request;

class EducatorClassController extends Controller
{
    protected $educatorClass;

    protected $message;



    /**
     * SubjectModulesController constructor.
     * @param EducatorClass $educatorClass
     */
    function __construct(EducatorClass $educatorClass )
    {

        $this->educatorClass = $educatorClass;

        $this->message = [
            'statusCode' => 200,
            'message' => ''
        ];
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EducatorClassRequest $request)
    {
        $data = $request->except('_token');

        if($this->educatorClass->create($data)){
            $this->message['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->message['message'] = self::MSG_CREATE_OK;

        }else{

            $this->message['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->message['message'] = '添加失败';
        }

        return response()->json($this->message);
    }

    /**
     * Display the specified resource.
     * @param  \App\Models\EducatorClass  $educatorClass
     * @return \Illuminate\Http\Response
     */
    public function show(EducatorClass $educatorClass)
    {

    }

    /**
     * Show the form for editing the specified resource.
     * @param  \App\Models\EducatorClass  $educatorClass
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        return view('educator_class.edit', [
            'js' => 'js/educator_class/edit.js',
            'educatorClass' => $this->educatorClass->findOrFail($id),
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
        if ($this->educatorClass->findOrFail($id)->update($request->all()))
        {
            $this->message['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->message['message'] = self::MSG_EDIT_OK;


        } else {
            $this->message['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->message['message'] = '更新失败';
        }

        return response()->json($this->message);


    }

    /**
     * 删除教职员工
     * @param  \App\Models\EducatorClass  $educatorClass
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->educatorClass->findOrFail($id)->delete()) {
            $this->message['message'] = self::MSG_DEL_OK;
        } else {
            $this->message['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->message['message'] = '删除失败';
        }
        return response()->json($this->message);
    }
}
