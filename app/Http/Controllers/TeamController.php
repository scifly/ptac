<?php
namespace App\Http\Controllers;

use App\Http\Requests\TeamRequest;
use App\Models\Team;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 教职员工组
 *
 * Class TeamController
 * @package App\Http\Controllers
 */
class TeamController extends Controller {
    
    protected $team;
    
    public function __construct(Team $team) {
    
        $this->middleware(['auth']);
        $this->team = $team;
    
    }
    
    /**
     * 教职员工组列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->team->datatable());
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建教职员工组
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        return $this->output();
        
    }
    
    /**
     * 保存教职员工组
     *
     * @param TeamRequest $request
     * @return JsonResponse
     */
    public function store(TeamRequest $request) {
        return $this->team->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 编辑教职员工组
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        $team = $this->team->find($id);
        if (!$team) {
            return $this->notFound();
        }
        
        return $this->output(['team' => $team]);
        
    }
    
    /**
     * 更新教职员工组
     *
     * @param TeamRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(TeamRequest $request, $id) {
        
        $team = $this->team->find($id);
        if (!$team) { return $this->notFound(); }
        
        return $team->update($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除教职员工组
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $team = $this->team->find($id);
        if (!$team) { return $this->notFound(); }
        
        return $team->delete() ? $this->succeed() : $this->fail();
        
    }
    
}
