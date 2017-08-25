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
        return parent::output(__METHOD__);

    }
    
    /**
     * 显示创建新的次分类
     * @return \Illuminate\Http\Response
     */
    public function create() {

        return parent::output(__METHOD__);
    }

    /**
     * 添加新次分类.
     *
     * @param SubjectModuleRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(SubjectModuleRequest $request) {
        $data = $request->except('_token');
        if ($this->subjectModule->existed($request)) {
            return $this->fail('已经有此记录');
        }
        return $this->subjectModule->create($data) ? parent::succeed() : parent::fail();

    }
    
    /**
     * Display the specified resource.
     * @param  \App\Models\SubjectModule $subjectModule
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $subjectModule = $this->subjectModule->whereId($id)
            ->first(['subject_id','name','weight','created_at','updated_at','enabled']);
        $subjectModule->subject_id = $subjectModule->subject->name;
        $subjectModule->enabled = $subjectModule->enabled==1 ? '已启用' : '已禁用' ;
        if ($subjectModule) {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_OK;
            $this->result['showData'] = $subjectModule;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
//        return view('subject_module.show',[
//            'subjectModule' => $this->subjectModule->FindOrfail($id)
//        ]);
    }
    
    /**
     * 编辑次分类.
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param SubjectModule $subjectModule
     */
    public function edit($id) {
        $subjectModules = $this->subjectModule->findOrFail($id)->toArray();

        if (!$subjectModules) { return parent::notFound(); }
        return parent::output(__METHOD__, [
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
        $subjectModule = $this->subjectModule->find($id);
        if (!$subjectModule) { return $this->notFound(); }
        if ($this->subjectModule->existed($request, $id)) {
            return $this->fail('已经有此记录');
        }
        return $subjectModule->update($request->all()) ? $this->succeed() : $this->fail();
    }
    
    /**
     * 删除次分类
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param SubjectModule $subjectModule
     */
    public function destroy($id) {
        $subjectModule = $this->find($id);
        if (!$subjectModule) { return parent::notFound(); }
        return $subjectModule->delete() ? parent::succeed() : parent::fail();
    }
}
