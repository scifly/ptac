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

    protected $message;

    /**
     * SubjectModulesController constructor.
     * @param EducatorClass $educatorClass
     */
    function __construct(EducatorClass $educatorClass, Educator $educator)
    {
        $this->educatorClass = $educatorClass;
        $this->educator = $educator;
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
        $data = $request->all();
        $user_id = $request->get('educator_id');
        $educator = $this->educator->where('user_id',$user_id)->pluck('id');
        $data['educator_id'] = $educator[0];
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
        $educatorClass = $this->educatorClass->findOrFail($id)->toArray();
        $educator_id = $educatorClass['educator_id'];
        $educator = $this->educator->where('id',$educator_id)->pluck('user_id');
        $educatorClass['educator_id'] =$educator[0];

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
        $user_id = $request->get('educator_id');
        $educator = $this->educator->where('user_id',$user_id)->pluck('id');
        $data['educator_id'] = $educator[0];
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
