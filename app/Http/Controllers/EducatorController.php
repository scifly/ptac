<?php

namespace App\Http\Controllers;

use App\Http\Requests\EducatorRequest;
use App\Models\Educator;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

/**
 * 教职员工
 *
 * Class EducatorController
 * @package App\Http\Controllers
 */
class EducatorController extends Controller {
    
    protected $educator;
    protected $team;

    public function __construct(Educator $educator, Team $team) {
        
        $this->educator = $educator;
        $this->team = $team;
        
    }
    
    /**
     * 教职员工列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json($this->educator->datatable());
        }
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 创建教职员工
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return $this->output(__METHOD__);
    }

    /**
     * 保存教职员工
     *
     * @param EducatorRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(EducatorRequest $request) {

        return $this->educator->create($request->all()) ? $this->succeed() : $this->fail();
        
    }

    /**
     * 教职员工详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $educator = $this->educator->find($id);
        if (!$educator) { return $this->notFound(); }
        return $this->output(__METHOD__, [
            'educator' => $educator,
            'educators' => $this->educator->teams()
        ]);
        
    }

    /**
     * 编辑教职员工
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        
        $educator = $this->educator->find($id);
        if (!$educator) { return $this->notFound(); }
        $teamIds = explode(",", $educator->team_ids);
        return $this->output(__METHOD__, [
            'educator' => $educator,
            'selectedTeams' => $this->team->teams($teamIds)
        ]);
        
    }

    /**
     * 更新教职员工
     *
     * @param EducatorRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(EducatorRequest $request, $id) {
        
        $educator = $this->educator->find($id);
        if (!$educator) { return $this->notFound(); }
        return $educator->update($request->all()) ? $this->succeed() : $this->fail();
        
    }

    /**
     * 删除教职员工
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        $educator = $this->educator->find($id);
        if (!$educator) { return $this->notFound(); }
        return $educator->delete() ? $this->succeed() : $this->fail();

    }
    
}
