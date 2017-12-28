<?php
namespace App\Http\Controllers;

use App\Http\Requests\SquadRequest;
use App\Models\Educator;
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
    
    public function __construct() {
    
        $this->middleware(['auth', 'checkrole']);
        
    }
    
    /**
     * 班级列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(Squad::datatable());
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
        
        return $this->result(Squad::store($request->all(), true));
        
    }
    
    /**
     * 编辑班级
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $class = Squad::find($id);
        $selectedEducators = [];
        if (!$class) { return $this->notFound(); }
        if ($class->educator_ids != '0') {
            $selectedEducators = Educator::educatorList(
                explode(",", $class->educator_ids)
            );
        }
        
        return $this->output([
            'class'             => $class,
            'selectedEducators' => $selectedEducators
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
        
        $class = Squad::find($id);
        if (!$class) { return $this->notFound(); }
        
        return $this->result(Squad::modify($request->all(), $id, true));
        
    }
    
    /**
     * 删除班级
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $class = Squad::find($id);
        if (!$class) { return $this->notFound(); }
        
        return $this->result($class->remove($id, true));
        
    }
    
}
