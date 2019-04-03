<?php
namespace App\Http\Controllers;

use App\Models\Turnstile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

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
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->turnstile->index())
            : $this->output();
        
    }
    
    /**
     * 更新门禁设备
     *
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function store() {
    
        return $this->result(
            $this->turnstile->store()
        );
    
    }
    
}
