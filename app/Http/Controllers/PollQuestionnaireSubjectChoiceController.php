<?php
namespace App\Http\Controllers;

use App\Http\Requests\PqChoiceRequest;
use App\Models\PollQuestionnaireSubjectChoice;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
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
    
    function __construct(PollQuestionnaireSubjectChoice $pqsc) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->pqsc = $pqsc;
        
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
                $this->pqsc->datatable()
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
        
        $this->authorize(
            'cs', PollQuestionnaireSubjectChoice::class
        );
        
        return $this->output();
        
    }
    
    /**
     * 保存选项
     *
     * @param PqChoiceRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(PqChoiceRequest $request) {
    
        $this->authorize(
            'cs', PollQuestionnaireSubjectChoice::class
        );
    
        return $this->result(
            $this->pqsc->create($request->all())
        );
        
    }
    
    /**
     * 编辑选项
     * @param $id
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function edit($id) {
        
        $pqsc = $this->pqsc->find($id);
        $this->authorize('eud', $pqsc);
        
        return $this->output([
            'pqsc' => $pqsc,
        ]);
        
    }
    
    /**
     * 更新选项
     *
     * @param PqChoiceRequest $request
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(PqChoiceRequest $request, $id) {
    
        $pqsc = $this->pqsc->find($id);
        $this->authorize('eud', $pqsc);
    
        return $this->result(
            $pqsc->update($request->all())
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
        
        $pqsc = $this->pqsc->find($id);
        $this->authorize('eud', $pqsc);
        
        return $this->result(
            $pqsc->delete()
        );
        
    }
    
}
