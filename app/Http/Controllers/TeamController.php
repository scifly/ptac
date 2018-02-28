<?php
namespace App\Http\Controllers;

use App\Http\Requests\TeamRequest;
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
    
    protected $team;
    
    public function __construct(Team $team) {
    
        $this->middleware(['auth', 'checkrole']);
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
            return response()->json(
                $this->team->datatable()
            );
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
        
        return $this->result(
            $this->team->create($request->all())
        );
        
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
        
        return $this->output([
            'team' => $team,
        ]);
        
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
        
        return $this->result(
            $team->update($request->all())
        );
        
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
        
        return $this->result(
            $team->remove($id)
        );
        
    }
    
}
