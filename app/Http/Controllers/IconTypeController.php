<?php
namespace App\Http\Controllers;

use App\Models\IconType;
use Illuminate\Http\JsonResponse;
use Throwable;

/**
 * 图标类型
 *
 * Class IconTypeController
 * @package App\Http\Controllers
 */
class IconTypeController extends Controller {
    
    protected $it;
    
    /**
     * IconTypeController constructor.
     * @param IconType $it
     */
    function __construct(IconType $it) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->it = $it;
        $this->approve($it);
        
    }
    
    /**
     * 删除图标类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->it->remove($id)
        );
        
    }
    
}