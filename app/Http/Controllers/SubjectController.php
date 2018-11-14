<?php
namespace App\Http\Controllers;

use App\Http\Requests\SubjectRequest;
use App\Models\Subject;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 科目
 *
 * Class SubjectController
 * @package App\Http\Controllers
 */
class SubjectController extends Controller {
    
    protected $subject;
    
    /**
     * SubjectController constructor.
     * @param Subject $subject
     */
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
                $this->subject->index()
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
            $this->subject->store(
                $request->all()
            )
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
            '_subject' => Subject::find($id),
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
            $this->subject->modify(
                $request->all(), $id
            )
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
