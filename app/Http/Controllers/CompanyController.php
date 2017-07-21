<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Support\Facades\Request;


class CompanyController extends Controller
{

    protected $company;

    function __construct(Company $company) { $this->company = $company; }

    /**
     * 显示运营者公司列表
     * @return \Illuminate\Http\Response
     * @internal param null $arg
     * @internal param Request $request
     */
    public function index() {

        /*if (Request::ajax() && !$arg) {
            return response()->json($this->school->datatable());
        } elseif ($arg) {
            return view('school.index', ['js' => 'js/school/index.js']);
        } else {
            return response()->json($this->school->datatable());
        }*/

        if (Request::get('draw')) {
            return response()->json($this->company->datatable());
        }
        return view('company.index', ['js' => 'js/company/index.js']);

    }

    /**
     * 显示创建运营者公司记录的表单
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新创建的运营者公司记录
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * 显示运营者公司记录详情
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
        //
    }

    /**
     * 显示编辑运营者公司记录的表单
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function edit(Company $company)
    {
        //
    }

    /**
     * 更新指定运营者公司记录
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Company $company)
    {
        //
    }

    /**
     * 删除指定运营者公司记录
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        //
    }
}
