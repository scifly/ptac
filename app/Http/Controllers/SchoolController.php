<?php
namespace App\Http\Controllers;

use App\Http\Requests\SchoolRequest;
use App\Jobs\CreateWechatDepartment;
use App\Models\Menu;
use App\Models\School as School;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 学校
 *
 * Class SchoolController
 * @package App\Http\Controllers
 */
class SchoolController extends Controller {
    
    protected $school, $menu;
    
    function __construct(School $school, Menu $menu) {
    
        $this->middleware(['auth']);
        $this->school = $school;
        $this->menu = $menu;
    
    }
    
    /**
     * 学校列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        if (Request::get('draw')) {
            return response()->json($this->school->datatable());
        }
        $user = Auth::user();
        $menuId = Request::input('menuId');
        if(!$menuId){
            return parent::output(__METHOD__);
        }
        $schoolMenuId = $this->menu->getSchoolMenuId($menuId);
        $show = true;
        if($user->group->name == '运营' || $user->group->name == '企业'){
            if ($schoolMenuId){
                $school = $this->school->where('menu_id', $schoolMenuId)->first();
                return parent::output('App\Http\Controllers\SchoolController::show', ['school' => $school, 'show' => $show]);
            } else {
                return parent::output(__METHOD__);
            }
         } else {
            $school = $this->school->where('menu_id', $schoolMenuId)->first();
            return parent::output('App\Http\Controllers\SchoolController::show', ['school' => $school, 'show' => $show]);
        }
    }
    
    /**
     * 创建学校
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return parent::output(__METHOD__);
        
    }
    
    /**
     * 保存学校
     *
     * @param SchoolRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(SchoolRequest $request) {
        
        return $this->school->store($request->all(), true)
            ? parent::succeed() : parent::fail();
        
    }
    
    /**
     * 学校详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id = null) {
            $school = $this->school->find($id);
            if (!$school) {
                return parent::notFound();
            }
            
            return parent::output(__METHOD__, ['school' => $school]);
    }
    
    /**
     * 编辑学校
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
    
        $menuId = Request::input('menuId');
        if(!$menuId){
            return parent::output(__METHOD__);
        }
        $school = $this->school->find($id);
        if (!$school) {
            return parent::notFound();
        }
        $schoolMenuId = $this->menu->getSchoolMenuId($menuId);
        if ($schoolMenuId){
            $show = true;
            return parent::output(__METHOD__, ['school' => $school, 'show' => $show]);
        }
        return parent::output(__METHOD__, ['school' => $school]);
        
    }
    
    /**
     * 更新学校
     *
     * @param SchoolRequest|\Illuminate\Http\Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(SchoolRequest $request, $id) {
        
        if (!$this->school->find($id)) { return parent::notFound(); }
        return $this->school->modify($request->all(), $id, true)
            ? parent::succeed() : parent::fail();
        
    }
    
    /**
     * 删除学校
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        if (!$this->school->find($id)) {
            return parent::notFound();
        }
        
        return $this->school->remove($id, true)
            ? parent::succeed() : parent::fail();
        
    }
    
}
