<?php
namespace App\Http\Controllers;

use App\Http\Requests\GroupRequest;
use App\Models\Group;
use App\Models\Menu;
use App\Models\School;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 角色
 *
 * Class GroupController
 * @package App\Http\Controllers
 */
class GroupController extends Controller {
    
    function __construct() {
    
        $this->middleware(['auth', 'checkrole']);
        
    }
    
    /**
     * 角色列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(Group::datatable());
        }

        return $this->output();
        
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
            $menuId = School::find($schoolId)->menu_id;
            return Menu::schoolTree($menuId);
        }

        return $this->output();
        
    }
    
    /**
     * 保存角色
     *
     * @param GroupRequest $request
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function store(GroupRequest $request) {
        
        return $this->result(Group::store($request->all()));
        
    }
    
    /**
     * 编辑角色
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $group = Group::find($id);
        if (!$group) { return $this->notFound(); }
        if (Request::method() === 'POST') {
            $schoolId = Request::query('schoolId');
            $menuId = School::find($schoolId)->menu_id;
            return Menu::schoolTree($menuId);
        }
        
        return $this->output(['group' => $group]);
        
    }
    
    /**
     * 更新角色
     *
     * @param GroupRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function update(GroupRequest $request, $id) {
        
        $group = Group::find($id);
        if (!$group) { return $this->notFound(); }
        
        return $this->result($group->modify($request->all(), $id));
        
    }
    
    /**
     * 删除角色
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $group = Group::find($id);
        if (!$group) { return $this->notFound(); }
        
        return $this->result($group->remove($id));
        
    }
    
}
