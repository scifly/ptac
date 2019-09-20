<?php
namespace App\Http\Controllers;

use App\Models\Consumption;
use Exception;
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
    
    /**
     * ConsumptionController constructor.
     * @param Consumption $consumption
     */
    function __construct(Consumption $consumption) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->approve($this->consumption = $consumption);
        
    }
    
    /**
     * 消费记录列表
     *
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->consumption->index())
            : $this->output();
        
    }
    
    /**
     * 统计消费记录
     *
     * @throws Throwable
     */
    public function show() {
        
        return $this->output();
        
    }
    
    /**
     * 消费记录统计
     *
     * @return JsonResponse
     */
    public function stat() {
        
        return response()->json(
            $this->consumption->stat()
        );
        
    }
    
    /**
     * 导出消费记录
     *
     * @return mixed
     * @throws Exception
     */
    public function export() {
        
        return $this->consumption->export();
        
    }
    
}
