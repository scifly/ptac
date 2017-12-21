<?php
namespace App\Http\Controllers;

use App\Http\Requests\TabRequest;
use App\Models\Action;
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
    
    protected $tab, $action, $menu;
    
    function __construct(Tab $tab, Menu $menu, Action $action) {
    
        $this->middleware(['auth']);
        $this->tab = $tab;
        $this->menu = $menu;
        $this->action = $action;
        
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
        if (!$this->tab->scan()) { return parent::notFound(); }
        
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
            'menus' => $this->menu->leaves(1),
        ]);
        
    }
    
    /**
     * 保存卡片
     *
     * @param TabRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(TabRequest $request) {
        return $this->tab->store($request->all())
            ? parent::succeed() : parent::fail();
        
    }
    
    /**
     * 卡片详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        $tab = $this->tab->find($id);
        if (!$tab) { return parent::notFound(); };
        
        return $this->output(['tab' => $tab]);
        
    }
    
    /**
     * 编辑卡片
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $tab = $this->tab->find($id);
        if (!$tab) { return parent::notFound(); }
        $tabMenus = $tab->menus;
        $selectedMenus = [];
        foreach ($tabMenus as $menu) {
            $selectedMenus[$menu->id] = $menu->name;
        }
        return $this->output([
            'tab'           => $tab,
            'menus'         => $this->menu->leaves(1),
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
     */
    public function update(TabRequest $request, $id) {
        
        $tab = $this->tab->find($id);
        if (!$tab) { return parent::notFound(); }
        
        return $this->tab->modify($request->all(), $id)
            ? parent::succeed() : parent::fail();
        
    }
    
    /**
     * 删除卡片
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $tab = $this->tab->find($id);
        if (!$tab) { return parent::notFound(); }
        
        return $this->tab->remove($id)
            ? parent::succeed() : parent::fail();
        
    }
    
}
