<?php
namespace App\Http\Controllers;

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
    
    function __construct() {
    
        $this->middleware(['auth']);
        
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
            return response()->json(Tab::datatable());
        }
        if (!Tab::scan()) { return $this->notFound(); }
        
        return $this->output();
        
    }
    
    /**
     * 创建卡片
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output(['menus' => Menu::leaves(1)]);
        
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
        
        return Tab::store($request->all())
            ? $this->succeed() : $this->fail();
        
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
        if (!$tab) { return $this->notFound(); }
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
        if (!$tab) { return $this->notFound(); }
        
        return Tab::modify($request->all(), $id)
            ? $this->succeed() : $this->fail();
        
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
        if (!$tab) { return $this->notFound(); }
        
        return Tab::remove($id) ? $this->succeed() : $this->fail(); 
    }
    
}
