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
        echo 123;exit;
        $custodian = $this->custodian->find($id);
        if (!$custodian) { return $this->notFound(); }
        return $this->output(__METHOD__, ['custodian' => $custodian]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Custodian  $custodian
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Custodian $custodian)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Custodian  $custodian
     * @return \Illuminate\Http\Response
     */
    public function destroy(Custodian $custodian)
    {
        //
    }
}
