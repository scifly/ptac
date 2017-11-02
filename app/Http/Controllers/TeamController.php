<?php
namespace App\Http\Controllers;

use App\Http\Requests\TeamRequest;
use App\Models\Team;
use Illuminate\Support\Facades\Request;

/**
 * 教职员工组
 *
 * Class TeamController
 * @package App\Http\Controllers
 */
class TeamController extends Controller {
    
    protected $team;
    
    public function __construct(Team $team) { $this->team = $team; }
    
    /**
     * 教职员工组列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->team->datatable());
        }
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 创建教职员工组
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存教职员工组
     *
     * @param TeamRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(TeamRequest $request) {
        
        return $this->team->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 教职员工组详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $team = $this->team->find($id);
        if (!$team) {
            return $this->notFound();
        }
        
        return $this->output(__METHOD__, ['team' => $team]);
        
    }
    
    /**
     * 编辑教职员工组
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        
        $team = $this->team->find($id);
        if (!$team) {
            return $this->notFound();
        }
        
        return $this->output(__METHOD__, ['team' => $team]);
        
    }
    
    /**
     * 更新教职员工组
     *
     * @param TeamRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TeamRequest $request, $id) {
        
        $team = $this->team->find($id);
        if (!$team) {
            return $this->notFound();
        }
        
        return $team->update($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除教职员工组
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        $team = $this->team->find($id);
        if (!$team) {
            return $this->notFound();
        }
        
        return $team->delete() ? $this->succeed() : $this->fail();
        
    }
    
}
