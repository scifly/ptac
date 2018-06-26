<?php
namespace App\Http\Controllers;

use App\Http\Requests\GroupRequest;
use App\Models\Group;
use App\Models\Menu;
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
    
    protected $group, $menu;
    
    function __construct(Group $group, Menu $menu) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->group = $group;
        $this->menu = $menu;
        $this->approve($group);
        
    }
    
    /**
     * 角色列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->group->index()
            );
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
            return $this->group->menuTree();
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
        
        return $this->result(
            $this->group->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑角色
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        if (Request::method() === 'POST') {
            return $this->group->menuTree();
        }
        
        return $this->output([
            'group' => Group::find($id),
        ]);
        
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
        
        return $this->result(
            $this->group->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除角色
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        return $this->result(
            $this->group->remove($id)
        );
        
    }
    
}
