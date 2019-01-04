<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
use App\Http\Requests\TabRequest;
use App\Models\Menu;
use App\Models\Tab;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 控制器
 *
 * Class TabController
 * @package App\Http\Controllers
 */
class TabController extends Controller {
    
    protected $tab, $menu;
    
    /**
     * TabController constructor.
     * @param Tab $tab
     * @param Menu $menu
     */
    function __construct(Tab $tab, Menu $menu) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->tab = $tab;
        $this->menu = $menu;
        if (!Request::has('ids')) {
            $this->approve($tab);
        }
        
    }
    
    /**
     * 控制器列表
     *
     * @return bool|JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->tab->index());
        }
        abort_if(
            !$this->tab->scan(),
            HttpStatusCode::NOT_ACCEPTABLE,
            __('messages.bad_request')
        );
        
        return $this->output();
        
    }
    
    /**
     * 编辑控制器
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
     * 更新控制器
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
