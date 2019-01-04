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
    
    protected $sm;
    
    /**
     * SubjectModuleController constructor.
     * @param SubjectModule $sm
     */
    function __construct(SubjectModule $sm) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->sm = $sm;
        $this->approve($sm);
        
    }
    
    /**
     * 科目次分类列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->sm->index())
            : $this->output();
        
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
            $this->sm->store(
                $request->all()
            )
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
        
        return $this->output([
            'sm' => $this->sm->find($id),
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
        
        return $this->result(
            $this->sm->modify(
                $request->all(), $id
            )
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
        
        return $this->result(
            $this->sm->remove($id)
        );
        
    }
    
}
