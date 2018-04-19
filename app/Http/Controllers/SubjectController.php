<?php
namespace App\Http\Controllers;

use Exception;
use Throwable;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\SubjectRequest;
use Illuminate\Support\Facades\Request;

/**
 * 科目
 *
 * Class SubjectController
 * @package App\Http\Controllers
 */
class SubjectController extends Controller {
    
    protected $subject;
    
    function __construct(Subject $subject) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->subject = $subject;
        $this->approve($subject);
        
    }
    
    /**
     * 科目列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->subject->datatable()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建科目
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存科目
     *
     * @param SubjectRequest $request
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function store(SubjectRequest $request) {
        
        return $this->result(
            $this->subject->store($request)
        );
        
    }
    
    /**
     * 编辑科目
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'subject' => Subject::find($id),
        ]);
        
    }
    
    /**
     * 更新科目
     *
     * @param SubjectRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function update(SubjectRequest $request, $id) {
        
        return $this->result(
            $this->subject->modify($request, $id)
        );
        
    }
    
    /**
     * 删除科目
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->subject->remove($id)
        );
        
    }
    
}
