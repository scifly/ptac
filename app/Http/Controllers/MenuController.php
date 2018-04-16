<?php
namespace App\Http\Controllers;

use Exception;
use Throwable;
use App\Models\Tab;
use App\Models\Menu;
use App\Models\MenuTab;
use App\Helpers\HttpStatusCode;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\MenuRequest;
use Illuminate\Support\Facades\Request;

/**
 * 菜单
 *
 * Class MenuController
 * @package App\Http\Controllers
 */
class MenuController extends Controller {
    
    protected $menu, $mt;
    
    function __construct(Menu $menu, MenuTab $mt) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->menu = $menu;
        $this->mt = $mt;
        $this->approve($menu);
        
    }
    
    /**
     * 菜单列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::method() === 'POST') {
            return $this->menu->tree(
                $this->menu->rootMenuId(true)
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建菜单
     *
     * @param $parentId integer 上级菜单ID
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create($parentId) {
        
        return $this->output([
            'parentId' => $parentId,
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
        
        return $this->result(
            $this->menu->store($request)
        );
        
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
        
        return $this->result(
            $this->menu->modify($request, $id)
        );
        
    }
    
    /**
     * 更新菜单所处位置
     *
     * @param $id
     * @param $parentId
     * @return JsonResponse
     */
    public function move($id, $parentId = null) {
        
        abort_if(
            !$this->menu->find($id) || !$this->menu->find($parentId),
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        if ($this->menu->movable($id, $parentId)) {
            return $this->result(
                $this->menu->move($id, $parentId, true)
            );
        }
        
        return abort(
            HttpStatusCode::NOT_ACCEPTABLE,
            '非法操作'
        );
        
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
        
        return $this->result(
            $this->menu->remove($id)
        );
        
    }
    
    /**
     * 保存菜单排列顺序
     *
     */
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
        
        # todo: needs to be moved to index method
        $tabRanks = MenuTab::whereMenuId($id)
            ->get()
            ->sortBy('tab_order')
            ->toArray();
        $tabs = [];
        foreach ($tabRanks as $rank) {
            $tabs[] = Tab::find($rank['tab_id']);
        }
        
        return $this->output([
            'tabs'   => $tabs,
            'menuId' => $id,
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
    
        # todo: needs to be moved to index method
        $ranks = Request::get('data');
        
        return $this->result(
            $this->mt->storeTabRanks($id, $ranks)
        );
        
    }
    
}
