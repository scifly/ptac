<?php
namespace App\Http\Controllers;

use App\Http\Requests\SchoolRequest;
use App\Models\Menu;
use App\Models\School as School;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\View\View;
use Throwable;

/**
 * 学校
 *
 * Class SchoolController
 * @package App\Http\Controllers
 */
class SchoolController extends Controller {
    
    protected $school;
    
    function __construct(School $school) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->school = $school;
        
    }
    
    /**
     * 学校列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->school->datatable()
            );
        }
    
        return $this->output();
    
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
     * @return JsonResponse|string
     */
    public function store(SchoolRequest $request) {
        
        return $this->result(
            $this->school->store($request->all(), true)
        );
        
    }
    
    /**
     * 学校详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        $school = School::find($id);
        abort_if(!$school, self::NOT_FOUND);
    
        return $this->output([
            'school' => $school,
        ]);
        
    }
    
    /**
     * 编辑学校
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $school = School::find($id);
        abort_if(!$school, self::NOT_FOUND);
    
        return $this->output([
            'school' => $school,
        ]);
        
    }
    
    /**
     * 更新学校
     *
     * @param SchoolRequest|\Illuminate\Http\Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(SchoolRequest $request, $id) {
        
        $school = School::find($id);
        abort_if(!$school, self::NOT_FOUND);
        
        return $this->result(
            $school->modify($request->all(), $id, true)
        );
        
    }
    
    /**
     * 删除学校
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $school = School::find($id);
        abort_if(!$school, self::NOT_FOUND);
        
        return $this->result(
            $school->remove($id, true)
        );
        
    }
    
    /**
     * 学校设置详情
     * @return Factory|JsonResponse|View
     * @throws Throwable
     */
    public function showInfo(){
        $menuId = Request::input('menuId');
        $menu = Menu::find($menuId);
        if (!$menu) {
            $menuId = Menu::whereUri('schools/showInfo')->first()->id;
            session(['menuId' => $menuId]);
        
            return view('home.home', [
                'menu'    => Menu::menuHtml(Menu::rootMenuId()),
                'content' => view('home.' . 'school'),
                'js'      => 'js/home/page.js',
                'user'    => Auth::user(),
            ]);
        }
        session(['menuId' => $menuId]);
        $school = School::find(School::schoolId());

        return response()->json([
            'statusCode' => self::OK,
            'html'       => view('school.show_info', [
                'school' => $school,
                'js' => 'js/school/show_info.js',
                'breadcrumb' => '学校设置'
            ])->render(),
            'uri'        => Request::path(),
            'title'      => '学校设置',
        ]);

    }

}
