<?php

namespace App\Http\Controllers;

use App\Models\Corp;
use Illuminate\Support\Facades\Request;


class CorpController extends Controller
{
    protected $corp;

    function __construct(Corp $corp) { $this->corp = $corp; }
    /**
     * 显示企业列表
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /*if (Request::ajax() && !$arg) {
           return response()->json($this->school->datatable());
       } elseif ($arg) {
           return view('school.index', ['js' => 'js/school/index.js']);
       } else {
           return response()->json($this->school->datatable());
       }*/

        if (Request::get('draw')) {
            return response()->json($this->corp->datatable());
        }
        return view('corp.index', ['js' => 'js/corp/index.js']);
    }

    /**
     * 显示创建企业记录的表单
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新创建的企业记录
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * 显示企业记录详情
     *
     * @param  \App\Models\Corp  $corp
     * @return \Illuminate\Http\Response
     */
    public function show(Corp $corp)
    {
        //
    }

    /**
     * 显示编辑企业记录的表单
     *
     * @param  \App\Models\Corp  $corp
     * @return \Illuminate\Http\Response
     */
    public function edit(Corp $corp)
    {
        //
    }

    /**
     * 更新指定企业记录
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Corp  $corp
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Corp $corp)
    {
        //
    }

    /**
     *删除指定企业记录
     *
     * @param  \App\Models\Corp  $corp
     * @return \Illuminate\Http\Response
     */
    public function destroy(Corp $corp)
    {
        //
    }
}
