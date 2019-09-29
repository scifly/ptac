<?php
namespace App\Http\Controllers;

use App\Models\Card;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 一卡通
 *
 * Class CardController
 * @package App\Http\Controllers
 */
class CardController extends Controller {
    
    protected $card;
    
    /**
     * CardController constructor.
     * @param Card $card
     */
    function __construct(Card $card) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->approve($this->card = $card);
        
    }
    
    /**
     * 一卡通列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->card->index())
            : $this->output();
        
    }
    
    /**
     * 批量发卡
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存发卡结果
     *
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function store() {
        
        return $this->result(
            $this->card->store()
        );
        
    }
    
    /**
     * 修改一卡通
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit() {
        
        return $this->output();
        
    }
    
    /**
     * 更新一卡通
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function update() {
        
        return $this->result(
            $this->card->modify()
        );
        
    }
    
    /**
     * 删除一卡通
     *
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function destroy() {
        
        return $this->result(
            $this->card->remove()
        );
        
    }
    
}
