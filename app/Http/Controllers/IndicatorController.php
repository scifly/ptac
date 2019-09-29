<?php
namespace App\Http\Controllers;

use App\Http\Requests\IndicatorRequest;
use App\Models\Indicator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request as Request;
use Throwable;

/**
 * 考核项
 *
 * Class IndicatorController
 * @package App\Http\Controllers
 */
class IndicatorController extends Controller {
    
    protected $indicator;
    
    /**
     * IndicatorController constructor.
     * @param Indicator $indicator
     */
    function __construct(Indicator $indicator) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->approve($this->indicator = $indicator);
        
    }
    
    /**
     * 考核项列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->indicator->index())
            : $this->output();
        
    }
    
    /**
     * 创建考核项
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存考核项
     *
     * @param IndicatorRequest $request
     * @return JsonResponse
     */
    public function store(IndicatorRequest $request) {
        
        return $this->result(
            $this->indicator->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑考核项
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'indicator' => $this->indicator->find($id),
        ]);
        
    }
    
    /**
     * 更新考核项
     *
     * @param IndicatorRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(IndicatorRequest $request, $id) {
        
        return $this->result(
            $this->indicator->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除考核项
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->indicator->remove($id)
        );
        
    }
    
}
