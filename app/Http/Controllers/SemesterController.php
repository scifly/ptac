<?php
namespace App\Http\Controllers;

use App\Http\Requests\SemesterRequest;
use App\Models\Semester;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 学期
 *
 * Class SemesterController
 * @package App\Http\Controllers
 */
class SemesterController extends Controller {
    
    function __construct() {
    
        $this->middleware(['auth', 'checkrole']);
    
    }
    
    /**
     * 学期列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(Semester::datatable());
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建学期
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        $this->authorize('c', Semester::class);
        
        return $this->output();
        
    }
    
    /**
     * 保存学期
     *
     * @param SemesterRequest $request
     * @return Semester|Model
     * @throws AuthorizationException
     */
    public function store(SemesterRequest $request) {
        
        $this->authorize('c', Semester::class);
        
        return Semester::create($request->all());
        
    }
   
    /**
     * 编辑学期
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $semester = Semester::find($id);
        $this->authorize('rud', $semester);
        
        return $this->output(['semester' => $semester]);
        
    }
    
    /**
     * 更新学期
     *
     * @param SemesterRequest $request
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(SemesterRequest $request, $id) {
        
        $semester = Semester::find($id);
        $this->authorize('rud', $semester);
        
        return $this->result($semester->update($request->all()));
        
    }
    
    /**
     * 删除学期
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $semester = Semester::find($id);
        $this->authorize('rud', $semester);
        
        return $this->result($semester->delete());
        
    }
    
}
