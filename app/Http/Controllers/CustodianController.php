<?php
namespace App\Http\Controllers;

use App\Helpers\HttpStatusCode;
use App\Http\Requests\CustodianRequest;
use App\Models\Custodian;
use App\Models\CustodianStudent;
use App\Models\Grade;
use App\Models\Squad;
use App\Models\Student;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 监护人
 *
 * Class CustodianController
 * @package App\Http\Controllers
 */
class CustodianController extends Controller {
    
    protected $custodian, $student, $grade, $class;
    
    function __construct(
        Custodian $custodian, Student $student,
        Grade $grade, Squad $class
    ) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->custodian = $custodian;
        $this->student = $student;
        $this->grade = $grade;
        $this->class = $class;
        
    }
    
    /**
     * 监护人列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->custodian->datatable()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建监护人
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        $this->authorize(
            'create', Custodian::class
        );
        if (Request::method() === 'POST') {
            abort_if(
                !Request::input('field') ||
                !Request::input('id') ||
                !in_array(Request::input('field'), ['grade', 'class']),
                HttpStatusCode::NOT_ACCEPTABLE,
                __('messages.not_acceptable')
            );
            $id = Request::input('id');
            if (Request::input('field') == 'grade') {
                list($classes, $classId) = $this->grade->classList($id);
                $this->result['html'] = [
                    'classes'  => $classes,
                    'students' => $this->class->studentList($classId),
                ];
            } else {
                $this->result['html']['students'] = $this->class->studentList($id);
            }
            
            return response()->json($this->result);
        }
        
        return $this->output();
        
    }
    
    /**
     * 保存监护人
     *
     * @param CustodianRequest $request
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function store(CustodianRequest $request) {
        
        $this->authorize(
            'store', Custodian::class
        );
        
        return $this->result(
            $this->custodian->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 监护人详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        $custodian = $this->custodian->find($id);
        $this->authorize('show', $custodian);
        
        return $this->output([
            'custodian' => $custodian,
        ]);
        
    }
    
    /**
     * 编辑监护人
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $custodian = $this->custodian->find($id);
        $this->authorize('edit', $custodian);
        if (Request::method() === 'POST') {
            abort_if(
                !Request::input('field') ||
                !Request::input('id') ||
                !in_array(Request::input('field'), ['grade', 'class']),
                HttpStatusCode::NOT_ACCEPTABLE,
                __('messages.not_acceptable')
            );
            $id = Request::input('id');
            if (Request::input('field') == 'grade') {
                list($classes, $classId) = $this->grade->classList($id);
                $this->result['html'] = [
                    'classes'  => $classes,
                    'students' => $this->class->studentList($classId),
                ];
            } else {
                $this->result['html']['students'] = $this->class->studentList($id);
            }
            
            return response()->json($this->result);
        }
        $relations = CustodianStudent::whereCustodianId($id)->get();
        
        return $this->output([
            'mobiles'   => $custodian->user->mobiles,
            'custodian' => $custodian,
            'relations' => $relations,
        ]);
        
    }
    
    /**
     * 更新监护人
     *
     * @param CustodianRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(CustodianRequest $request, $id) {
        
        $custodian = $this->custodian->find($id);
        $this->authorize('update', $custodian);
        
        return $this->result(
            $custodian->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除指定的监护人
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function destroy($id) {
        
        $custodian = $this->custodian->find($id);
        $this->authorize('destroy', $custodian);
        
        return $this->result(
            $custodian->remove($id)
        );
        
    }
    
    /**
     * @return array
     * @throws AuthorizationException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export() {
        
        $this->authorize(
            'export', Custodian::class
        );
        
        return $this->custodian->export();
        
    }
    
}
