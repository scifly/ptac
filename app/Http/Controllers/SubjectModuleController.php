<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubjectModuleRequest;
use App\Models\SubjectModule;
use Illuminate\Support\Facades\Request;

class SubjectModuleController extends Controller {
    protected $subjectModule;
    
    /**
     * SubjectModulesController constructor.
     * @param SubjectModule $subjectModule
     */
    function __construct(SubjectModule $subjectModule) { $this->subjectModule = $subjectModule; }
    
    /**
     * 显示次分类列表.
     * @return \Illuminate\Http\Response
     */
    public function index() {
        if (Request::get('draw')) {
            return response()->json($this->subjectModule->datatable());
        }
        return view('subject_module.index', [
            'js' => 'js/subject_module/index.js',
            'dialog' => true,
            'datatable' => true,
            'form' => true,
        ]);
    }
    
    /**
     * 显示创建新的次分类
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('subject_module.create', [
            'js' => 'js/subject_module/create.js',
            'form' => true
        ]);
    }
    
    /**
     * 添加新次分类.
     * @param SubjectModuleRequest|\Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(SubjectModuleRequest $request) {
        $data = $request->except('_token');
        
        if ($this->subjectModule->create($data)) {
            return response()->json([
                'statusCode' => self::HTTP_STATUSCODE_OK, 'message' => self::MSG_CREATE_OK,
            ]);
        } else {
            return response()->json([
                'statusCode' => self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR,
                'message' => '添加失败'
            ]);
            
        }
    }
    
    /**
     * Display the specified resource.
     * @param  \App\Models\SubjectModule $subjectModule
     * @return \Illuminate\Http\Response
     */
    public function show(SubjectModule $subjectModule) {
    
    }
    
    /**
     * 编辑次分类.
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param SubjectModule $subjectModule
     */
    public function edit($id) {
        $subjectModules = $this->subjectModule->findOrFail($id)->toArray();
        
        return view('subject_module.edit', [
            'js' => 'js/subject_module/edit.js',
            'form' => true,
            'subjectModules' => $subjectModules,
        
        ]);
    }
    
    /**
     * 更改次分类
     * @param SubjectModuleRequest|\Illuminate\Http\Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param SubjectModule $subjectModule
     */
    public function update(SubjectModuleRequest $request, $id) {
        $data = $request->all();
        $subject = $this->subjectModule->findOrFail($id);
        if ($subject->update($data)) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['message'] = self::MSG_EDIT_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '更新失败';
        }
        return response()->json($this->result);
    }
    
    /**
     * 删除次分类
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param SubjectModule $subjectModule
     */
    public function destroy($id) {
        if ($this->subjectModule->findOrFail($id)->delete()) {
            $this->result['message'] = self::MSG_DEL_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '删除失败';
        }
        return response()->json($this->result);
    }
}
