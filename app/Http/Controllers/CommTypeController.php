<?php
namespace App\Http\Controllers;

use App\Models\CommType;
use Illuminate\Http\JsonResponse;
use Throwable;

/**
 * 通信方式
 *
 * Class CommTypeController
 * @package App\Http\Controllers
 */
class CommTypeController extends Controller {
    
    protected $ct;
    
    /**
     * CommTypeController constructor.
     * @param CommType $ct
     */
    function __construct(CommType $ct) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->ct = $ct;
        $this->approve($ct);
        
    }

    /**
     * 删除通信方式
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->ct->remove($id)
        );
        
    }
    
}
