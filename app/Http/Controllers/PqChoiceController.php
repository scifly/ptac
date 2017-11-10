<?php
namespace App\Http\Controllers;

use App\Http\Requests\PqChoiceRequest;
use App\Models\PollQuestionnaireChoice;
use Illuminate\Support\Facades\Request;

/**
 * 题目选项
 *
 * Class PqChoiceController
 * @package App\Http\Controllers
 */
class PqChoiceController extends Controller {
    
    protected $pqChoice;
    
    /**
     * PqChoiceController constructor.
     * @param PollQuestionnaireChoice $pqChoice
     */
    function __construct(PollQuestionnaireChoice $pqChoice) {
        
        $this->middleware(['auth']);
        $this->pqChoice = $pqChoice;
        
    }
    
    /**
     * 选项列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->pqChoice->datatable());
        }
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 创建选项
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存选项
     *
     * @param PqChoiceRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PqChoiceRequest $request) {
        
        return $this->pqChoice->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 选项详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $pqChoice = $this->pqChoice->find($id);
        if (!$pqChoice) {
            return $this->notFound();
        }
        
        return $this->output(__METHOD__, [
            'pqChoice' => $pqChoice,
        ]);
        
    }
    
    /**
     * 编辑选项
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        
        $pqChoice = $this->pqChoice->find($id);
        if (!$pqChoice) {
            return $this->notFound();
        }
        return $this->output(__METHOD__, [
            'pqChoice' => $pqChoice,
        ]);
        
    }
    
    /**
     * 更新选项
     *
     * @param PqChoiceRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PqChoiceRequest $request, $id) {
        
        $pqChoice = $this->pqChoice->find($id);
        if (!$pqChoice) {
            return $this->notFound();
        }
        
        return $pqChoice->update($request->all()) ? $this->succeed() : $this->fail();
    }
    
    /**
     * 删除选项
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        $pqChoice = $this->pqChoice->find($id);
        if (!$pqChoice) {
            return $this->notFound();
        }
        
        return $pqChoice->delete() ? $this->succeed() : $this->fail();
        
    }
}
