<?php
namespace App\Http\Controllers;

use App\Http\Requests\PassageRuleRequest;
use App\Models\PassageRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 门禁通行规则
 *
 * Class PassageRuleController
 * @package App\Http\Controllers
 */
class PassageRuleController extends Controller {
    
    protected $pr;
    
    /**
     * PassageRuleController constructor.
     * @param PassageRule $pr
     */
    function __construct(PassageRule $pr) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->approve($this->pr = $pr);
        
    }
    
    /**
     * 列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->pr->index())
            : $this->output();
        
    }
    
    /**
     * 创建通行规则
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存通行规则
     *
     * @param PassageRuleRequest $request
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function store(PassageRuleRequest $request) {
        
        return $this->result(
            $this->pr->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑通行规则
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'pr' => $this->pr->find($id),
        ]);
        
    }
    
    /**
     * 更新通行规则
     *
     * @param PassageRuleRequest $request
     * @param $id
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function update(PassageRuleRequest $request, $id) {
        
        return $this->result(
            $this->pr->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除通行规则
     *
     * @param $id
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->pr->remove($id)
        );
        
    }
    
    /**
     * 下发通行规则
     *
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function issue() {
        
        return $this->result(
            $this->pr->issue()
        );
        
    }
    
}
