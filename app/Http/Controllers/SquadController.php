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
    
    protected $class, $educator;
    
    public function __construct(Squad $class, Educator $educator) {
    
        $this->middleware(['auth']);
        $this->class = $class;
        $this->educator = $educator;
        
    }
    
    /**
     * 班级列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->class->datatable());
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
        
        return $this->class->store($request->all(), true)
            ? $this->succeed() : $this->fail();
        
    }
    
    // /**
    //  * 班级详情
    //  *
    //  * @param $id
    //  * @return bool|\Illuminate\Http\JsonResponse
    //  */
    // public function show($id) {
    //
    //     $class = $this->class->find($id);
    //     if (!$class) {
    //         return $this->notFound();
    //     }
    //     $educatorIds = explode(",", $class->educator_ids);
    //     return $this->output([
    //         'class'     => $class,
    //         'educators' => $this->educator->educators($educatorIds),
    //     ]);
    //
    // }
    /**
     * 编辑班级
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $class = $this->class->find($id);
        $selectedEducators = [];
        if (!$class) {
            return $this->notFound();
        }
        if ($class->educator_ids != '0') {
            $selectedEducators = $this->educator->getEducatorListByIds(explode(",", $class->educator_ids));
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
        
        if (!$this->class->find($id)) { return $this->notFound(); }
        
        return $this->class->modify($request->all(), $id, true)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除班级
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        if (!$this->class->find($id)) { return $this->notFound(); }
        
        return $this->class->remove($id, true) ? $this->succeed() : $this->fail();
        
    }
    
}
