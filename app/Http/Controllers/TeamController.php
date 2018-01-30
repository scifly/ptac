<?php
namespace App\Http\Controllers;

use App\Http\Requests\TeamRequest;
use App\Models\EducatorTeam;
use App\Models\Team;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
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
    
    public function __construct() {
    
        $this->middleware(['auth', 'checkrole']);
    
    }
    
    /**
     * 教职员工组列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(Team::datatable());
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
        
        $this->authorize('c', Team::class);
        
        return $this->output();
        
    }
    
    /**
     * 保存教职员工组
     *
     * @param TeamRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(TeamRequest $request) {
        
        $this->authorize('c', Team::class);
        
        return $this->result(Team::create($request->all()));
        
    }
    
    /**
     * 编辑教职员工组
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $team = Team::find($id);
        $this->authorize('rud', $team);
        
        return $this->output(['team' => $team]);
        
    }
    
    /**
     * 更新教职员工组
     *
     * @param TeamRequest $request
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(TeamRequest $request, $id) {
        
        $team = Team::find($id);
        $this->authorize('rud', $team);
        
        return $this->result($team->update($request->all()));
        
    }
    
    /**
     * 删除教职员工组
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $team = Team::find($id);
        $this->authorize('rud', $team);
        $educators = EducatorTeam::whereTeamId($id);
        if ($educators) {
            return $this->result(false, '', '该组下包含教师，无法删除！');
        }
        return $this->result($team->delete());
        
    }
    
}
