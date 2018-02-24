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
 * 卡片
 *
 * Class TabController
 * @package App\Http\Controllers
 */
class TabController extends Controller {
    
    protected $tab;
    
    function __construct(Tab $tab) {
    
        $this->middleware(['auth', 'checkrole']);
        $this->tab = $tab;
        
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
            return response()->json(
                $this->tab->datatable()
            );
        }
        abort_if(!$this->tab->scan(), HttpStatusCode::NOT_FOUND);
        
        return $this->output();
        
    }
    
    /**
     * 创建卡片
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output([
            'menus' => Menu::leaves(1)
        ]);
        
    }
    
    /**
     * 保存卡片
     *
     * @param TabRequest $request
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function store(TabRequest $request) {
        
        return $this->result(
            $this->tab->store($request->all())
        );
        
    }
    
    /**
     * 编辑卡片
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $tab = Tab::find($id);
        abort_if(!$tab, HttpStatusCode::NOT_FOUND);
        $tabMenus = $tab->menus;
        $selectedMenus = [];
        foreach ($tabMenus as $menu) {
            $selectedMenus[$menu->id] = $menu->name;
        }
        
        return $this->output([
            'tab'           => $tab,
            'menus'         => Menu::leaves(1),
            'selectedMenus' => $selectedMenus,
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
    public function update(TabRequest $request, $id) {
        
        $tab = Tab::find($id);
        abort_if(!$tab, HttpStatusCode::NOT_FOUND);
        
        return $this->result(
            $this->tab->modify($request->all(), $id)
        );
        
    }
    
    /**
     * 删除卡片
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function destroy($id) {
        
        $tab = Tab::find($id);
        abort_if(!$tab, HttpStatusCode::NOT_FOUND);
        
        return $this->result(
            $this->tab->remove($id)
        );
        
    }
    
}
