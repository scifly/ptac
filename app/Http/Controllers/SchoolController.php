<?php
namespace App\Http\Controllers;

use App\Http\Requests\SchoolRequest;
use App\Jobs\CreateWechatDepartment;
use App\Models\Menu;
use App\Models\School as School;
use Exception;
use Illuminate\Http\JsonResponse;
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
        
        return parent::output(__METHOD__);
        
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
        if ($id){
            $school = $this->school->find($id);
            if (!$school) { return parent::notFound(); }
            return parent::output(__METHOD__, ['school' => $school]);
        }else{
            $menuId = Request::input('menuId');
            $schoolMenuId = $this->menu->getSchoolMenuId($menuId);
            $school = $this->school->where('menu_id', $schoolMenuId)->first();
            return response()->json([
                'statusCode' => 200,
                'html'       => view('school.show', ['school' => $school])->render(),
                // 'js'         => 'js/school/show',
                'uri'        => Request::path(),
                'title'      => '学校设置',
            ]);
        }
    }
    
    /**
     * 编辑学校
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $school = $this->school->find($id);
        if (!$school) {
            return parent::notFound();
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
