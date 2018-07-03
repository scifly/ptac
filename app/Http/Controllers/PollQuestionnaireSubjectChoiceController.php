<?php
namespace App\Http\Controllers;

use App\Http\Requests\PqChoiceRequest;
use App\Models\PollQuestionnaireSubjectChoice;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 调查问卷题目选项
 *
 * Class PqChoiceController
 * @package App\Http\Controllers
 */
class PollQuestionnaireSubjectChoiceController extends Controller {
    
    protected $pqsc;
    
    /**
     * PollQuestionnaireSubjectChoiceController constructor.
     * @param PollQuestionnaireSubjectChoice $pqsc
     */
    function __construct(PollQuestionnaireSubjectChoice $pqsc) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->pqsc = $pqsc;
        $this->approve($pqsc);
        
    }
    
    /**
     * 选项列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->pqsc->index()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建选项
     *
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存选项
     *
     * @param PqChoiceRequest $request
     * @return JsonResponse
     */
    public function store(PqChoiceRequest $request) {
        
        return $this->result(
            $this->pqsc->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑选项
     * @param $id
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'pqsc' => $this->pqsc->find($id),
        ]);
        
    }
    
    /**
     * 更新选项
     *
     * @param PqChoiceRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(PqChoiceRequest $request, $id) {
        
        return $this->result(
            $this->pqsc->update(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除选项
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        return $this->result(
            $this->pqsc->remove($id)
        );
        
    }
    
}
