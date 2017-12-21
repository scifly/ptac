<?php
namespace App\Http\Controllers;

use App\Http\Requests\PqSubjectRequest;
use App\Models\PollQuestionnaireSubject;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 问卷题目
 *
 * Class PqSubjectController
 * @package App\Http\Controllers
 */
class PqSubjectController extends Controller {
    
    protected $pqSubject;
    
    function __construct(PollQuestionnaireSubject $pqSubject) {
    
        $this->middleware(['auth']);
        $this->pqSubject = $pqSubject;
        
    }
    
    /**
     * 题目列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->pqSubject->dataTable());
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建题目
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存题目
     *
     * @param PqSubjectRequest $request
     * @return JsonResponse
     */
    public function store(PqSubjectRequest $request) {
        
        return $this->pqSubject->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 题目详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        $pqSubject = $this->pqSubject->find($id);
        if (!$pqSubject) { return $this->notFound(); }
        return $this->output([
            'pqSubject' => $pqSubject,
        ]);
        
    }
    
    /**
     * 编辑题目
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $pqSubject = $this->pqSubject->find($id);
        if (!$pqSubject) { return $this->notFound(); }
        return $this->output(['pqSubject' => $pqSubject]);
        
    }
    
    /**
     * 更新题目
     *
     * @param PqSubjectRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(PqSubjectRequest $request, $id) {
        
        $pqSubject = $this->pqSubject->find($id);
        if (!$pqSubject) { return $this->notFound(); }
        return $pqSubject->update($request->all()) ? $this->succeed() : $this->fail();
    }
    
    /**
     * 删除题目
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $pqSubject = $this->pqSubject->find($id);
        if (!$pqSubject) { return $this->notFound(); }
        return $pqSubject->remove($id) 
            ? $this->succeed() : $this->fail('失败：该题目存在有效关联数据，不能删除');
        
    }
}
