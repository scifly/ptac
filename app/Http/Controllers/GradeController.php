<?php
namespace App\Http\Controllers;

use App\Http\Requests\GradeRequest;
use App\Models\Educator;
use App\Models\Grade;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;

/**
 * 年级
 *
 * Class GradeController
 * @package App\Http\Controllers
 */
class GradeController extends Controller {
    
    protected $grade;
    
    function __construct(Grade $grade) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->grade = $grade;
        
    }
    
    /**
     * 年级列表
     *
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->grade->datatable()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建年级
     *
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function create() {
        
        $this->authorize('c', Grade::class);
        
        return $this->output();
        
    }
    
    /**
     * 保存年级
     *
     * @param GradeRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(GradeRequest $request) {
        
        $this->authorize('c', Grade::class);
        
        return $this->result(
            $this->grade->store($request->all(), true)
        );
        
    }
    
    /**
     * 编辑年级
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws \Throwable
     */
    public function edit($id) {
        
        $grade = Grade::find($id);
<<<<<<< HEAD
        abort_if(!$grade, self::NOT_FOUND);
=======
>>>>>>> a8b77c532a4d09f2fe4f9feaadd84ba5d5a4fd12
        $this->authorize('rud', $grade);
        
        $selectedEducators = [];
        if ($grade->educator_ids != '0') {
            $selectedEducators = Educator::educatorList(
                explode(",", rtrim($grade->educator_ids,","))
            );
        }
        
        return $this->output([
            'grade'             => $grade,
            'selectedEducators' => $selectedEducators,
        ]);
        
    }
    
    /**
     * 更新年级
     *
     * @param GradeRequest $request
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(GradeRequest $request, $id) {
        
        $grade = Grade::find($id);
<<<<<<< HEAD
        abort_if(!$grade, self::NOT_FOUND);
=======
>>>>>>> a8b77c532a4d09f2fe4f9feaadd84ba5d5a4fd12
        $this->authorize('rud', $grade);
        
        return $this->result(
            $grade->modify($request->all(), $id, true)
        );
        
    }
    
    /**
     * 删除年级
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
       
        $grade = Grade::find($id);
<<<<<<< HEAD
        abort_if(!$grade, self::NOT_FOUND);
=======
>>>>>>> a8b77c532a4d09f2fe4f9feaadd84ba5d5a4fd12
        $this->authorize('rud', $grade);
        
        return $this->result(
            $grade->remove($id, true)
        );
        
    }
    
}
