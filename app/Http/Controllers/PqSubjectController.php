<?php
namespace App\Http\Controllers;

use App\Http\Requests\PqSubjectRequest;
use App\Models\PollQuestionnaireSubject;
use Illuminate\Support\Facades\Request;

/**
 * 问卷题目
 *
 * Class PqSubjectController
 * @package App\Http\Controllers
 */
class PqSubjectController extends Controller {
    
    protected $pqSubject;
    
    function __construct(PollQuestionnaireSubject $pqSubject) {
        
        $this->pqSubject = $pqSubject;
    }
    
    /**
     * 题目列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->pqSubject->dataTable());
        }
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 创建题目
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存题目
     *
     * @param PqSubjectRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PqSubjectRequest $request) {
        
        return $this->pqSubject->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 题目详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $pqSubject = $this->pqSubject->find($id);
        if (!$pqSubject) {
            return $this->notFound();
        }
        
        return $this->output(__METHOD__, [
            'pqSubject' => $pqSubject,
        ]);
        
    }
    
    /**
     * 编辑题目
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        
        $pqSubject = $this->pqSubject->find($id);
        if (!$pqSubject) {
            return $this->notFound();
        }
        
        return $this->output(__METHOD__, ['pqSubject' => $pqSubject]);
        
    }
    
    /**
     * 更新题目
     *
     * @param PqSubjectRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PqSubjectRequest $request, $id) {
        
        $pqSubject = $this->pqSubject->find($id);
        if (!$pqSubject) {
            return $this->notFound();
        }
        
        return $pqSubject->update($request->all()) ? $this->succeed() : $this->fail();
    }
    
    /**
     * 删除题目
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        $pqSubject = $this->pqSubject->find($id);
        if (!$pqSubject) {
            return $this->notFound();
        }
        
        return $pqSubject->remove($id) ? $this->succeed() : $this->fail('失败：该题目存在有效关联数据，不能删除');
        
    }
}
