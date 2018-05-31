<?php
namespace App\Http\Controllers;

use Exception;
use Throwable;
use App\Models\Squad;
use App\Models\Educator;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\SquadRequest;
use Illuminate\Support\Facades\Request;

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
     * @throws Exception
     */
    public function store(SquadRequest $request) {
        
        return $this->result(
            $this->class->store($request)
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
     * @throws Exception
     */
    public function update(SquadRequest $request, $id) {
        
        return $this->result(
            $this->class->modify($request, $id)
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
