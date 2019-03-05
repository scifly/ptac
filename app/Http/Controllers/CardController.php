<?php
namespace App\Http\Controllers;

use App\Http\Requests\CardRequest;
use App\Models\Card;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 一卡通
 *
 * Class CorpController
 * @package App\Http\Controllers
 */
class CardController extends Controller {
    
    protected $card;
    
    /**
     * CorpController constructor.
     * @param Card $card
     */
    function __construct(Card $card) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->card = $card;
        $this->approve($card);
        
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
     * 编辑一卡通
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
     * @param CardRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(CardRequest $request) {
        
        return $this->result(
            $this->card->modify($request)
        );
        
    }
    
    /**
     * 删除一卡通
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function destroy($id = null) {
        
        return $this->result(
            $this->card->remove($id)
        );
        
    }
    
}
