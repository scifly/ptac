<?php
namespace App\Http\Controllers;

use App\Http\Requests\ConsumptionRequest;
use App\Models\Consumption;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 消费记录
 *
 * Class ConsumptionController
 * @package App\Http\Controllers
 */
class ConsumptionController extends Controller {
    
    protected $consumption;
    
    function __construct(Consumption $consumption) {
        
        // $this->middleware(['auth', 'checkrole']);
        $this->consumption = $consumption;
        
    }
    
    /**
     * 消费记录列表
     *
     * @throws Throwable
     */
    public function index() {
    
        if (Request::get('draw')) {
            return response()->json(
                $this->consumption->datatable()
            );
        }
        
        return $this->output();
    
    }
    
    /**
     * 保存消费记录
     *
     * @param ConsumptionRequest $request
     * @return JsonResponse|string
     */
    public function store(ConsumptionRequest $request) {

        return response()->json([
            'success' => 'ok'
        ]);
        return $this->result(
            $this->consumption->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 统计消费记录
     *
     * @throws Throwable
     */
    public function stat() {
    
        return $this->output();
        
    }
    
    /**
     * 导出消费记录
     */
    public function export() {
    
    
    
    }
    
}
