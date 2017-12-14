<?php
namespace App\Http\Controllers;

use App\Http\Requests\GroupRequest;
use App\Models\Action;
use App\Models\Corp;
use App\Models\Group;
use App\Models\Menu;
use App\Models\School;
use App\Models\Tab;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 角色
 *
 * Class GroupController
 * @package App\Http\Controllers
 */
class GroupController extends Controller {
    
    protected $group, $menu, $tab, $action, $corp, $school;
    
    function __construct(
        Group $group, Menu $menu, Tab $tab,
        Action $action, Corp $corp, School $school
    ) {
    
        $this->middleware(['auth']);
        $this->group = $group;
        $this->menu = $menu;
        $this->tab = $tab;
        $this->action = $action;
        $this->corp = $corp;
        $this->school = $school;
        
    }
    
    /**
     * 角色列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->group->datatable());
        }

        return $this->output(__METHOD__);
        
    }
    
    /**
     * 创建角色
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {

        if (Request::method() === 'POST') {
            $schoolId = Request::query('schoolId');
            $menuId = School::whereId($schoolId)->first()->menu_id;
            return $this->menu->getTree($menuId);
        }

        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存角色
     *
     * @param GroupRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(GroupRequest $request) {
        
        return $this->group->store($request->all())
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 编辑角色
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $group = $this->group->find($id);
        if (!$group) { return $this->notFound(); }
        if (Request::method() === 'POST') {
            $schoolId = Request::query('schoolId');
            $menuId = School::whereId($schoolId)->first()->menu_id;
            return $this->menu->getTree($menuId);
        }
        
        return $this->output(__METHOD__, ['group' => $group]);
        
    }
    
    /**
     * 更新角色
     *
     * @param GroupRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function update(GroupRequest $request, $id) {
        
        $group = $this->group->find($id);
        if (!$group) { return $this->notFound(); }
        return $group->modify($request->all(), $id)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除角色
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $group = $this->group->find($id);
        if (!$group) { return $this->notFound(); }
        return $group->remove($id) ? $this->succeed() : $this->fail();
        
    }
    
}
