<?php
namespace App\Http\Controllers;

use App\Models\Consumption;
use App\Policies\ConsumptionStat;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Request;
use PhpOffice\PhpSpreadsheet\Exception;
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
        
        $this->middleware(['auth', 'checkrole']);
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
                $this->consumption->index()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 统计消费记录
     *
     * @throws Throwable
     */
    public function show() {
        
        $this->authorize(
            'show', ConsumptionStat::class
        );
        
        return $this->output();
        
    }
    
    /**
     * 消费记录统计
     *
     * @param Request $request
     * @return array
     * @throws AuthorizationException
     */
    public function stat(Request $request) {
        
        $this->authorize(
            'stat',
            new ConsumptionStat($request::all())
        );
        $detail = Request::query('detail');
        if (!isset($detail)) {
            list($consumption, $charge) = $this->consumption->stat(
                $request::all()
            );
            $this->result['consumption'] = $consumption;
            $this->result['charge'] = $charge;
        } else {
            $this->result['details'] = $this->consumption->stat(
                $request::all(), Request::query('detail')
            );
        }
        
        return response()->json(
            $this->result
        );
        
    }
    
    /**
     * 导出消费记录
     *
     * @return mixed
     * @throws AuthorizationException
     * @throws Exception
     */
    public function export() {
        
        $this->authorize(
            'export', ConsumptionStat::class
        );
        $detail = Request::query('detail');
        
        return !isset($detail)
            ? $this->consumption->export()
            : $this->consumption->export($detail, Request::all());
        
    }
    
}
