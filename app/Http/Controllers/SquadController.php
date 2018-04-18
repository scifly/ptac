<?php
namespace App\Http\Controllers;

use App\Http\Requests\SquadRequest;
use App\Models\Educator;
use App\Models\Squad;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
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
    
    protected $class, $educator;
    
    public function __construct(Squad $class, Educator $educator) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->class = $class;
        $this->educator = $educator;
        $this->approve($class);
        
    }
    
    /**
     * 班级列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->class->datatable()
            );
        }
        
        return $this->output();
        
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
     */
    public function store(SquadRequest $request) {
        
        return $this->result(
            $this->class->store(
                $request->all(), true
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
            'class' => $this->class->find($id)
        ]);
        
    }
    
    /**
     * 更新班级
     *
     * @param SquadRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(SquadRequest $request, $id) {
        
        return $this->result(
            $this->class->modify(
                $request->all(), $id, true
            )
        );
        
    }
    
    /**
     * 删除班级
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        return $this->result(
            $this->class->remove(
                $id, true
            )
        );
        
    }
    
}
