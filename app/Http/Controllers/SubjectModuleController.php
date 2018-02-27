<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
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
    
    protected $sm;
    
    function __construct(SubjectModule $sm) {
    
        $this->middleware(['auth', 'checkrole']);
        $this->sm = $sm;
        
    }
    
    /**
     * 科目次分类列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->sm->datatable()
            );
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
        
        return $this->result(
            $this->sm->create($request->all())
        );
        
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
        abort_if(!$sm, HttpStatusCode::NOT_FOUND);
        
        return $this->output([
            'sm' => $sm,
        ]);
        
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
        abort_if(!$sm, HttpStatusCode::NOT_FOUND);
        
        return $this->result(
            $sm->update($request->all())
        );
        
    }
    
    /**
     * 删除科目次分类
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $sm = $this->sm->find($id);
        abort_if(!$sm, HttpStatusCode::NOT_FOUND);
        
        return $this->result(
            $sm->delete()
        );
        
    }
    
}
