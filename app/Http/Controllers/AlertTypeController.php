<?php
namespace App\Http\Controllers;

use App\Models\AlertType;
use Illuminate\Http\JsonResponse;
use Throwable;

/**
 * 警告类型
 *
 * Class AlertTypeController
 * @package App\Http\Controllers
 */
class AlertTypeController extends Controller {
    
    protected $at;
    
    /**
     * AlertTypeController constructor.
     * @param AlertType $at
     */
    function __construct(AlertType $at) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->at = $at;
        $this->approve($at);
        
    }
    
    /**
     * 删除警告类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->at->remove($id)
        );
        
    }
    
}
