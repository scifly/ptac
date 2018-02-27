<?php
namespace App\Http\Controllers;

use App\Http\Requests\PqChoiceRequest;
use App\Models\PollQuestionnaireSubjectChoice;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 题目选项
 *
 * Class PqChoiceController
 * @package App\Http\Controllers
 */
class PollQuestionnaireSubjectChoiceController extends Controller {
    
    function __construct() {
        
        $this->middleware(['auth', 'checkrole']);
        
    }
    
    /**
     * 选项列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(PollQuestionnaireSubjectChoice::datatable());
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建选项
     *
     * @return bool|\Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存选项
     *
     * @param PqChoiceRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PqChoiceRequest $request) {
        
        return $this->result(PollQuestionnaireSubjectChoice::create($request->all()));
        
    }
    
    /**
     * 选项详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function show($id) {
        
        $pqChoice = PollQuestionnaireSubjectChoice::find($id);
        if (!$pqChoice) { return $this->notFound(); }
        
        return $this->output(['pqChoice' => $pqChoice]);
        
    }
    
    /**
     * 编辑选项
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function edit($id) {
        
        $pqChoice = PollQuestionnaireSubjectChoice::find($id);
        if (!$pqChoice) { return $this->notFound(); }
        
        return $this->output(['pqChoice' => $pqChoice]);
        
    }
    
    /**
     * 更新选项
     *
     * @param PqChoiceRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PqChoiceRequest $request, $id) {
    
        $pqChoice = PollQuestionnaireSubjectChoice::find($id);
        if (!$pqChoice) { return $this->notFound(); }
    
        return $this->result($pqChoice->update($request->all()));
        
    }
    
    /**
     * 删除选项
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $pqChoice = PollQuestionnaireSubjectChoice::find($id);
        if (!$pqChoice) { return $this->notFound(); }
        
        return $this->result($pqChoice->delete());
        
    }
    
}
