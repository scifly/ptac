<?php
namespace App\Http\Controllers;

use App\Models\PassageLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 门禁通行记录
 *
 * Class PassageLogController
 * @package App\Http\Controllers
 */
class PassageLogController extends Controller {
    
    protected $pl;
    
    /**
     * PassageLogController constructor.
     * @param PassageLog $pl
     */
    function __construct(PassageLog $pl) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->pl = $pl;
        $this->approve($pl);
        
    }
    
    /**
     * 列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->pl->index())
            : $this->output();
        
    }
    
    /**
     * 采集数据
     *
     * @return JsonResponse|string
     */
    public function store() {
        
        return $this->result(
            $this->pl->store()
        );
        
    }
    
    /**
     * 批量导出
     *
     * @return JsonResponse|string
     */
    public function export() {
        
        return $this->result(
            $this->pl->export()
        );
        
    }
    
}
