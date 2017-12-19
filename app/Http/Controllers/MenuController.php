<?php
namespace App\Http\Controllers;

use App\Http\Requests\MenuRequest;
use App\Models\Menu;
use App\Models\MenuTab;
use App\Models\MenuType;
use App\Models\Tab;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 菜单
 *
 * Class MenuController
 * @package App\Http\Controllers
 */
class MenuController extends Controller {
    
    protected $menu, $menuType, $menuTab;

    function __construct(Menu $menu, MenuType $menuType, MenuTab $menuTab) {

        $this->middleware(['auth']);
        $this->menu = $menu;
        $this->menuType = $menuType;
        $this->menuTab = $menuTab;

    }
    
    /**
     * 菜单列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     * @throws Throwable
     */
    public function index() {
        
        if (Request::method() === 'POST') {
            return $this->menu->tree($this->menu->rootMenuId());
        }

        return $this->output(__METHOD__);

    }
    
    /**
     * 创建菜单
     *
     * @param $id integer 上级菜单ID
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create($id) {

        return $this->output(__METHOD__, [
            'parentId'   => $id,
            'menuTypeId' => MenuType::whereName('其他')->first()->id,
        ]);

    }
    
    /**
     * 保存菜单
     *
     * @param MenuRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(MenuRequest $request) {

        return $this->menu->store($request)
            ? $this->succeed() : $this->fail();

    }
    
    /**
     * 编辑菜单
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {

        $menu = $this->menu->find($id);
        if (!$menu) { return $this->notFound(); }
        # 获取已选定的卡片
        $menuTabs = $menu->tabs;
        $selectedTabs = [];
        foreach ($menuTabs as $tab) {
            $selectedTabs[$tab->id] = $tab->name;
        }

        return $this->output(__METHOD__, [
            'menu'         => $menu,
            'selectedTabs' => $selectedTabs,
        ]);

    }
    
    /**
     * 更新菜单
     *
     * @param MenuRequest $request
     * @param integer $id 菜单ID
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function update(MenuRequest $request, $id) {

        $menu = $this->menu->find($id);
        if (!$menu) { return $this->notFound(); }

        return $this->menu->modify($request, $id)
            ? $this->succeed() : $this->fail();

    }

    /**
     * 更新菜单所处位置
     *
     * @param $id
     * @param $parentId
     * @return JsonResponse
     */
    public function move($id, $parentId = null) {

        if (!$parentId) {
            return $this->fail('非法操作');
        }
        $menu = $this->menu->find($id);
        $parentMenu = $this->menu->find($parentId);
        if (!$menu || !$parentMenu) {
            return $this->notFound();
        }
        if ($this->menu->movable($id, $parentId)) {
            return $this->menu->move($id, $parentId, true)
                ? $this->succeed() : $this->fail();
        }

        return $this->fail('非法操作');

    }
    
    /**
     * 删除菜单
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function destroy($id) {

        $menu = $this->menu->find($id);
        if (!$menu) { return $this->notFound(); }

        return $this->menu->remove($id) ? $this->succeed() : $this->fail();

    }

    /** 保存菜单排列顺序 */
    public function sort() {

        $positions = Request::get('data');
        foreach ($positions as $id => $pos) {
            $menu = $this->menu->find($id);
            if (isset($menu)) {
                $menu->position = $pos;
                $menu->save();
            }
        }

    }
    
    /**
     * 菜单包含的卡片
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function menuTabs($id) {

        $menu = $this->menu->find($id);
        if (!$menu) { return $this->notFound(); }
        $tabRanks = MenuTab::whereMenuId($id)
            ->get()
            ->sortBy('tab_order')
            ->toArray();
        $tabs = [];
        foreach ($tabRanks as $rank) {
            $tab = Tab::whereId($rank['tab_id'])->first();
            $tabs[] = $tab;
        }

        return $this->output(__METHOD__, ['tabs' => $tabs]);

    }
    
    /**
     * 保存菜单卡片排列顺序
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function rankTabs($id) {

        $menu = $this->menu->find($id);
        if (!$menu) {
            return $this->notFound();
        }
        $ranks = Request::get('data');

        return $this->menuTab->storeTabRanks($id, $ranks)
            ? $this->succeed() : $this->fail();

    }
    
}
