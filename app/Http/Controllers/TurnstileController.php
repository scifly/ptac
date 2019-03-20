<?php
namespace App\Http\Controllers;

use App\Http\Requests\TurnstileRequest;
use App\Models\Turnstile;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;

/**
 * 门禁设备
 *
 * Class TurnstileController
 * @package App\Http\Controllers
 */
class TurnstileController extends Controller {
    
    protected $turnstile;
    
    /**
     * TurnstileController constructor.
     * @param Turnstile $turnstile
     */
    function __construct(Turnstile $turnstile) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->turnstile = $turnstile;
        Request::has('ids') ?: $this->approve($turnstile);
        
    }
    
    /**
     * 门禁设备列表
     *
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->turnstile->index())
            : $this->output();
        
    }
    
    public function store() {
    
    
    
    }
    
    /**
     * 编辑门禁设备
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'turnstile' => $this->turnstile->find($id),
        ]);
        
    }
    
    /**
     * 更新门禁设备
     *
     * @param TurnstileRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function update(TurnstileRequest $request, $id = null) {
        
        return $this->result(
            $this->turnstile->modify(
                $request->all(), $id
            )
        );
        
    }
    
}
