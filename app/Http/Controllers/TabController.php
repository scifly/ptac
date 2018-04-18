<?php
namespace App\Http\Controllers;

use Exception;
use Throwable;
use App\Models\Tab;
use App\Models\Menu;
use App\Helpers\HttpStatusCode;
use App\Http\Requests\TabRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;

/**
 * 卡片
 *
 * Class TabController
 * @package App\Http\Controllers
 */
class TabController extends Controller {
    
    protected $tab, $menu;
    
    function __construct(Tab $tab, Menu $menu) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->tab = $tab;
        $this->menu = $menu;
        $this->approve($tab);
        
    }
    
    /**
     * 卡片列表
     *
     * @return bool|JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->tab->datatable());
        }
        abort_if(!$this->tab->scan(), HttpStatusCode::NOT_FOUND);
        
        return $this->output();
        
    }
    
    /**
     * 编辑卡片
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'tab' => $this->tab->find($id),
        ]);
        
    }
    
    /**
     * 更新卡片
     *
     * @param TabRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function update(TabRequest $request, $id = null) {
        
        return $this->result(
            $this->tab->modify($request->all(), $id)
        );
    
    }
    
}
