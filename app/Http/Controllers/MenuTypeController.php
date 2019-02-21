<?php
namespace App\Http\Controllers;

use App\Models\MenuType;
use Illuminate\Http\JsonResponse;
use Throwable;

/**
 * 菜单类型
 *
 * Class MenuTypeController
 * @package App\Http\Controllers
 */
class MenuTypeController extends Controller {
    
    protected $mt;
    
    /**
     * MenuTypeController constructor.
     * @param MenuType $mt
     */
    function __construct(MenuType $mt) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->mt = $mt;
        $this->approve($mt);
        
    }

    /**
     * 删除菜单类型
     *
     * @param $id
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->mt->remove($id)
        );
        
    }
    
}
