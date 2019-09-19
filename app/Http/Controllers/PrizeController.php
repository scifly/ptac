<?php
namespace App\Http\Controllers;

use App\Http\Requests\PrizeRequest;
use App\Models\Prize;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request as Request;
use Throwable;

/**
 * 奖励与处罚
 *
 * Class PrizeController
 * @package App\Http\Controllers
 */
class PrizeController extends Controller {
    
    protected $prize;
    
    /**
     * IndicatorController constructor.
     * @param Prize $prize
     */
    function __construct(Prize $prize) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->prize = $prize;
        $this->approve($prize);
        
    }
    
    /**
     * 奖励与处罚列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->prize->index())
            : $this->output();
        
    }
    
    /**
     * 创建奖励与处罚
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存奖励与处罚
     *
     * @param PrizeRequest $request
     * @return JsonResponse
     */
    public function store(PrizeRequest $request) {
        
        return $this->result(
            $this->prize->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑奖励与处罚
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'prize' => $this->prize->find($id),
        ]);
        
    }
    
    /**
     * 更新奖励与处罚
     *
     * @param PrizeRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(PrizeRequest $request, $id) {
        
        return $this->result(
            $this->prize->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除奖励与处罚
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->prize->remove($id)
        );
        
    }
    
}
