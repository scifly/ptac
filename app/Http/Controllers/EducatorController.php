<?php

namespace App\Http\Controllers;

use App\Http\Requests\EducatorRequest;
use App\Models\Educator;
use App\Models\EducatorClass;
use App\Models\Mobile;
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
    protected $mobile;
    protected $educatorClass;

    public function __construct(Educator $educator, Mobile $mobile, EducatorClass $educatorClass) {
        
        $this->educator = $educator;
        $this->mobile = $mobile;
        $this->educatorClass = $educatorClass;

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

        return $this->educator->store($request) ? $this->succeed() : $this->fail();
        
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
        $mobiles = $this->mobile->where('user_id',$educator->user_id)->get();
//        dd($mobiles);die;
        return $this->output(__METHOD__, [
            'educator' => $educator,
            'mobiles' => $mobiles
        ]);
        
    }

    /**
     * 教职员工充值
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function recharge($id) {

        $educator = $this->educator->find($id);
        if (!$educator) { return $this->notFound(); }
        return $this->output(__METHOD__, [
            'educator' => $educator,
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
        dd($request->all());die;
        return $educator->update($request->all()) ? $this->succeed() : $this->fail();
        
    }

    /**
     * 充值教职员工
     *
     * @param EducatorRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function rechargeStore( $id) {

        $educator = $this->educator->find($id);
        if (!$educator) { return $this->notFound(); }
        $recharge= Request::get('recharge');
        $educator->sms_quote += $recharge;
        return $educator->save() ? $this->succeed() : $this->fail();

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
