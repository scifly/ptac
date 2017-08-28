<?php

namespace App\Http\Controllers;

use App\Http\Requests\EducatorRequest;
use App\Models\Educator;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

/**
 * @property array message
 */
class EducatorController extends Controller {
    
    protected $educator;
    protected $team;

    public function __construct(Educator $educator, Team $team) {
        $this->educator = $educator;
        $this->team = $team;
    }
    
    /**
     * 显示教职员工列表
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
     * 显示创建教职员工记录的表单
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return $this->output(__METHOD__);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EducatorRequest $request
     * @return \Illuminate\Http\Response
     * @internal param EducatorRequest $educatorRequest
     * @internal param \Illuminate\Http\Request $request
     */
    public function store(EducatorRequest $request) {

        if ($this->educator->existed($request)) {
            return $this->fail('已经有此记录');
        }
        return $this->educator->create($request->all()) ? $this->succeed() : $this->fail();
    }
    
    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Educator $educator
     */
    public function show($id) {
        $educator = $this->educator->find($id);
        if (!$educator) { return $this->notFound(); }
        return $this->output(__METHOD__, [
            'educator' => $educator,
            'educators' => $this->educator->teams($educator->team_ids)
        ]);
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Educator $educator
     */
    public function edit($id) {
        $educator = $this->educator->find($id);
        if (!$educator) { return $this->notFound(); }
        return $this->output(__METHOD__, [
            'educator' => $educator,
            'selectedTeams' => $this->team->teams($educator->educator_ids)
        ]);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param EducatorRequest|\Illuminate\Http\Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Educator $educator
     */
    public function update(EducatorRequest $request, $id) {
        $educator = $this->educator->find($id);
        if (!$educator) { return $this->notFound(); }
        if ($this->educator->existed($request, $id)) {
            return $this->fail('已经有此记录');
        }
        return $educator->update($request->all()) ? $this->succeed() : $this->fail();
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Educator $educator
     */
    public function destroy($id) {
        $educator = $this->educator->find($id);
        if (!$educator) { return $this->notFound(); }
        return $educator->delete() ? $this->succeed() : $this->fail();

    }
}
