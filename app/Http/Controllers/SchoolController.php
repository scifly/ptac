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
    
        return $this->output();
    
        // $user = Auth::user();
        // $menuId = Request::input('menuId');
        // if(!$menuId){
        //     return $this->output();
        // }
        // $schoolMenuId = $this->menu->getSchoolMenuId($menuId);
        // $show = true;
        // if($user->group->name == '运营' || $user->group->name == '企业'){
        //     if ($schoolMenuId){
        //         $school = $this->school->where('menu_id', $schoolMenuId)->first();
        //         return $this->output('App\Http\Controllers\SchoolController::show', ['school' => $school, 'show' => $show]);
        //     } else {
        //         return $this->output();
        //     }
        //  } else {
        //     $school = $this->school->where('menu_id', $schoolMenuId)->first();
        //     return $this->output('App\Http\Controllers\SchoolController::show', ['school' => $school, 'show' => $show]);
        // }
        
    }
    
    /**
     * 创建学校
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
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
    public function show($id) {
        $school = $this->school->find($id);
        if (!$school) {
        
            return parent::notFound();
        }
    
        return $this->output(['school' => $school]);
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
    
        return $this->output(['school' => $school]);
        
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
    
    /**
     * 学校设置详情
     * @return \Illuminate\Contracts\View\Factory|JsonResponse|\Illuminate\View\View
     * @throws Throwable
     */
    public function showInfo(){
        $menuId = Request::input('menuId');
        $menu = $this->menu->find($menuId);
        if (!$menu) {
            $menuId = $this->menu->where('uri', 'schools/show')->first()->id;
            session(['menuId' => $menuId]);
        
            return view('home.home', [
                'menu'    => $this->menu->getMenuHtml($this->menu->rootMenuId()),
                'content' => view('home.' . 'school'),
                'js'      => 'js/home/page.js',
                'user'    => Auth::user(),
            ]);
        }
        $schoolMenuId = $this->menu->getSchoolMenuId($menuId);
        $school = $this->school->where('menu_id', $schoolMenuId)->first();
        session(['menuId' => $menuId]);
            return response()->json([
                'statusCode' => 200,
                'html'       => view('school.show_info', ['school' => $school, 'js' => 'js/school/show_info.js', 'breadcrumb' => '学校设置'])->render(),
                'uri'        => Request::path(),
                'title'      => '学校设置',
            ]);
    }
}
