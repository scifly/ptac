<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
use App\Http\Requests\MenuRequest;
use App\Models\Menu;
use App\Models\MenuTab;
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
     * @param null $menuId
     * @param null $parentMenuId
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index($menuId = null, $parentMenuId = null) {
        
        return Request::method() == 'POST'
            ? $this->menu->index($menuId, $parentMenuId)
            : $this->output();
        
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
            $this->menu->store(
                $request
            )
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
        
        return $this->output([
            'menu' => $this->menu->find($id),
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
            $this->menu->modify(
                $request, $id
            )
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
     * 排序菜单卡片
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function sort($id) {
        
        return Request::method() == 'POST'
            ? $this->result($this->mt->storeTabRanks($id, Request::get('data')))
            : $this->output();

    }
    
}
