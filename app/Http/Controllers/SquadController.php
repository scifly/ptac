<?php
namespace App\Http\Controllers;

use App\Http\Requests\SquadRequest;
use App\Models\Squad;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 班级
 *
 * Class SquadController
 * @package App\Http\Controllers
 */
class SquadController extends Controller {
    
    protected $class;
    
    /**
     * SquadController constructor.
     * @param Squad $class
     */
    public function __construct(Squad $class) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->approve($this->class = $class);
        
    }
    
    /**
     * 班级列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->class->index())
            : $this->output();
        
    }
    
    /**
     * 创建班级
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存班级
     *
     * @param SquadRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(SquadRequest $request) {
        
        return $this->result(
            $this->class->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑班级
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'class' => $this->class->find($id),
        ]);
        
    }
    
    /**
     * 更新班级
     *
     * @param SquadRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(SquadRequest $request, $id = null) {
        
        return $this->result(
            $this->class->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除班级
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->class->remove($id)
        );
        
    }
    
}
