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
class PollQuestionnaireSubjectController extends Controller {
    
    function __construct() {
    
        $this->middleware(['auth', 'checkrole']);
        
    }
    
    /**
     * 题目列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                PollQuestionnaireSubject::dataTable()
            );
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
        
        return $this->result(
            PollQuestionnaireSubject::create($request->all())
        );
        
    }
    
    /**
     * 题目详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        $pqs = PollQuestionnaireSubject::find($id);
        if (!$pqs) { return $this->notFound(); }
        
        return $this->output(['pqSubject' => $pqs]);
        
    }
    
    /**
     * 编辑题目
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $pqs = PollQuestionnaireSubject::find($id);
        if (!$pqs) { return $this->notFound(); }
        
        return $this->output(['pqSubject' => $pqs]);
        
    }
    
    /**
     * 更新题目
     *
     * @param PqSubjectRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(PqSubjectRequest $request, $id) {
    
        $pqs = PollQuestionnaireSubject::find($id);
        if (!$pqs) { return $this->notFound(); }
    
        return $this->result($pqs->update($request->all()));
        
    }
    
    /**
     * 删除题目
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $pqs = PollQuestionnaireSubject::find($id);
        if (!$pqs) { return $this->notFound(); }
        
        return $this->result($pqs->remove($id));
        
    }
    
}
