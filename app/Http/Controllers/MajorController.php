<?php
namespace App\Http\Controllers;

use App\Http\Requests\MajorRequest;
use App\Models\Major;
use App\Models\Subject;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 专业
 *
 * Class MajorController
 * @package App\Http\Controllers
 */
class MajorController extends Controller {
    
    protected $major;
    
    function __construct(Major $major) {
    
        $this->middleware(['auth', 'checkrole']);
        $this->major = $major;
        
    }
    
    /**
     * 专业列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->major->datatable()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建专业
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存专业
     *
     * @param MajorRequest $request
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function store(MajorRequest $request) {
        
        return $this->result(
            $this->major->store($request)
        );
        
    }

    /**
     * 编辑专业
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $major = Major::find($id);
<<<<<<< HEAD
        abort_if(!$major, self::NOT_FOUND);

=======
        $this->authorize('rud', $major);
>>>>>>> a8b77c532a4d09f2fe4f9feaadd84ba5d5a4fd12
        $majorSubjects = $major->subjects;
        $selectedSubjects = [];
        foreach ($majorSubjects as $subject) {
            $selectedSubjects[$subject->id] = $subject->name;
        }
        
        return $this->output([
            'major'            => $major,
            'selectedSubjects' => $selectedSubjects,
        ]);
        
    }
    
    /**
     * 更新专业
     *
     * @param MajorRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function update(MajorRequest $request, $id) {
        
        $major = Major::find($id);
<<<<<<< HEAD
        abort_if(!$major, self::NOT_FOUND);
=======
        $this->authorize('rud', $major);
>>>>>>> a8b77c532a4d09f2fe4f9feaadd84ba5d5a4fd12
        
        return $this->result(
            $major->modify($request, $id)
        );
        
    }
    
    /**
     * 删除专业
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function destroy($id) {
        
        $major = Major::find($id);
<<<<<<< HEAD
        abort_if(!$major, self::NOT_FOUND);

=======
        $this->authorize('rud', $major);
        
>>>>>>> a8b77c532a4d09f2fe4f9feaadd84ba5d5a4fd12
        return $this->result(
            $major->remove($id)
        );
        
    }
    
}
