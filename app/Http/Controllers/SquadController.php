<?php
namespace App\Http\Controllers;

use App\Http\Requests\SquadRequest;
use App\Models\Educator;
use App\Models\Squad;
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
        
        $this->class = $class;
        $this->educator = $educator;
        
    }
    
    /**
     * 班级列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->class->datatable());
        }
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 创建班级
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存班级
     *
     * @param SquadRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SquadRequest $request) {
        
        return $this->class->store($request->all(), true)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 班级详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $class = $this->class->find($id);
        if (!$class) {
            return $this->notFound();
        }
        $educatorIds = explode(",", $class->educator_ids);
        return $this->output(__METHOD__, [
            'class'     => $class,
            'educators' => $this->educator->educators($educatorIds),
        ]);
        
    }
    
    /**
     * 编辑班级
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        
        $class = $this->class->find($id);
        if (!$class) {
            return $this->notFound();
        }
        $educatorIds = explode(",", $class->educator_ids);
        return $this->output(__METHOD__, [
            'class'             => $class,
            'selectedEducators' => $this->educator->educators($educatorIds),
        ]);
        
    }
    
    /**
     * 更新班级
     *
     * @param SquadRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(SquadRequest $request, $id) {
        
        if (!$this->class->find($id)) {
            return $this->notFound();
        }
        return $this->class->modify($request->all(), $id, true)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除班级
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        if (!$this->class->find($id)) {
            return $this->notFound();
        }
        return $this->class->remove($id, true)
            ? $this->succeed() : $this->fail();
        
    }
    
}
