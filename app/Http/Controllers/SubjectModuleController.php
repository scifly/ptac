<?php
namespace App\Http\Controllers;

use App\Http\Requests\SubjectModuleRequest;
use App\Models\SubjectModule;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 科目次分类
 *
 * Class SubjectModuleController
 * @package App\Http\Controllers
 */
class SubjectModuleController extends Controller {
    
    protected $subjectModule;
    
    function __construct(SubjectModule $subjectModule) {
    
        $this->middleware(['auth']);
        $this->subjectModule = $subjectModule;
        
    }
    
    /**
     * 科目次分类列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(SubjectModule::datatable());
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建科目次分类
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存科目次分类
     *
     * @param SubjectModuleRequest $request
     * @return JsonResponse
     */
    public function store(SubjectModuleRequest $request) {
        
        return $this->result(SubjectModule::create($request->all()));
        
    }
    
    /**
     * 编辑目次分类
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function edit($id) {
        
        $sm = SubjectModule::find($id);
        if (!$sm) { return $this->notFound(); }
        
        return $this->output(['subjectModules' => $sm]);
        
    }
    
    /**
     * 更新科目次分类
     *
     * @param SubjectModuleRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(SubjectModuleRequest $request, $id) {
        
        $sm = SubjectModule::find($id);
        if (!$sm) { return $this->notFound(); }
        
        return $this->result($sm->update($request->all()));
        
    }
    
    /**
     * 删除科目次分类
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
<<<<<<< HEAD
        $sm = $this->subjectModule->find($id);
        if (!$sm) { return $this->notFound(); }
=======
        $sm = $this->sm->find($id);
        abort_if(!$sm, HttpStatusCode::NOT_FOUND);
>>>>>>> a8b77c532a4d09f2fe4f9feaadd84ba5d5a4fd12
        
        return $this->result(
            $sm->delete()
        );
        
    }
    
}
