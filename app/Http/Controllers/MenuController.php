<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
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
    
    function __construct() {

        $this->middleware(['auth', 'checkrole']);

    }
    
    /**
     * 菜单列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::method() === 'POST') {
            return Menu::tree(
                Menu::rootMenuId(true)
            );
        }

        return $this->output();

    }
    
    /**
     * 创建菜单
     *
     * @param $id integer 上级菜单ID
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create($id) {

        return $this->output([
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
     * @throws Throwable
     */
    public function store(MenuRequest $request) {

        return $this->result(Menu::store($request));

    }
    
    /**
     * 编辑菜单
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {

        $menu = Menu::find($id);
        if (!$menu) { return $this->notFound(); }
        # 获取已选定的卡片
        $menuTabs = $menu->tabs;
        $selectedTabs = [];
        foreach ($menuTabs as $tab) {
            $selectedTabs[$tab->id] = $tab->name;
        }

        return $this->output([
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

        $menu = Menu::find($id);
        if (!$menu) { return $this->notFound(); }

        return $this->result($menu->modify($request, $id));

    }

    /**
     * 更新菜单所处位置
     *
     * @param $id
     * @param $parentId
     * @return JsonResponse
     */
    public function move($id, $parentId = null) {

        if (!$parentId) { return $this->fail('非法操作'); }
        $menu = Menu::find($id);
        $parentMenu = Menu::find($parentId);
        if (!$menu || !$parentMenu) { return $this->notFound(); }
        if (Menu::movable($id, $parentId)) {
            return $this->result($menu::move($id, $parentId, true));
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

        $menu = Menu::find($id);
        abort_if(!$menu, HttpStatusCode::NOT_FOUND);

        return $this->result(
            $menu->remove($id)
        );

    }

    /** 保存菜单排列顺序 */
    public function sort() {

        $positions = Request::get('data');
        foreach ($positions as $id => $pos) {
            $menu = Menu::find($id);
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

        $menu = Menu::find($id);
        abort_if(!$menu, HttpStatusCode::NOT_FOUND);
        $tabRanks = MenuTab::whereMenuId($id)
            ->get()
            ->sortBy('tab_order')
            ->toArray();
        $tabs = [];
        foreach ($tabRanks as $rank) {
            $tabs[] = Tab::find($rank['tab_id']);
        }

        return $this->output([
            'tabs' => $tabs,
            'menuId' => $id
        ]);

    }
    
    /**
     * 保存菜单卡片排列顺序
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function rankTabs($id) {

        abort_if(!Menu::find($id), HttpStatusCode::NOT_FOUND);
        $ranks = Request::get('data');

        return $this->result(
            MenuTab::storeTabRanks($id, $ranks)
        );

    }
    
}
