<?php
namespace App\Http\Controllers;

use App\Models\ActionType;
use Illuminate\Http\JsonResponse;
use Throwable;

/**
 * Http请求类型
 *
 * Class ActionTypeController
 * @package App\Http\Controllers
 */
class ActionTypeController extends Controller {
    
    protected $at;
    
    /**
     * ActionTypeController constructor.
     * @param ActionType $at
     */
    function __construct(ActionType $at) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->at = $at;
        $this->approve($at);
        
    }
    
    /**
     * 删除Http请求类型
     *
     * @param $id
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->at->remove($id)
        );
        
    }
    
}
