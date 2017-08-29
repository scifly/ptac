<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustodianRequest;
use App\Models\Custodian;
use Illuminate\Support\Facades\Request;

class CustodianController extends Controller
{
    protected $custodian;

    function __construct(Custodian $custodian) {

        $this->custodian = $custodian;

    }
    /**
     * 显示监护人列表.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Request::get('draw')) {
            return response()->json($this->custodian->datatable());
        }
        return parent::output(__METHOD__);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return parent::output(__METHOD__);
    }

    /**
     * 新增一个监护人.
     * @param CustodianRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustodianRequest $request)
    {
        $data = $request->except('_token');
        if ($this->custodian->existed($request)) {
            return $this->fail('已经有此记录');
        }
        return $this->custodian->create($data) ? $this->succeed() : $this->fail();

    }

    /**
     * Display the specified resource.
     * @param  \App\Models\Custodian  $custodian
     * @return \Illuminate\Http\Response
     */
    public function show(Custodian $custodian)
    {

    }

    /**
     * 编辑监护人.
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Custodian $custodian
     */
    public function edit($id)
    {

        $custodian = $this->custodian->find($id);
        if (!$custodian) { return $this->notFound(); }
        return $this->output(__METHOD__, ['custodian' => $custodian]);

    }

    /**
     * 更新监护人.
     * @param CustodianRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CustodianRequest $request,$id)
    {
        $custodian = $this->custodian->find($id);
        if (!$custodian) { return $this->notFound(); }
        if ($this->custodian->existed($request, $id)) {
            return $this->fail('已经有此记录');
        }
        return $custodian->update($request->all()) ? $this->succeed() : $this->fail();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Custodian  $custodian
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $custodian = $this->custodian->find($id);
        if (!$custodian) { return $this->notFound(); }
        return $custodian->delete() ? $this->succeed() : $this->fail();
    }
}
