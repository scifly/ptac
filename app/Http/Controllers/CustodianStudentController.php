<?php

namespace App\Http\Controllers;

use App\Models\CustodianStudent;
use Illuminate\Support\Facades\Request;

class CustodianStudentController extends Controller
{
    protected $custodianStudent;

    function __construct(CustodianStudent $custodianStudent)
    {
        $this->custodianStudent = $custodianStudent;
    }

    /**
     * 显示监护人列表
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Request::get('draw')) {
            return response()->json($this->custodianStudent->datatable());
        }
        return view('custodian_student.index', [
            'js' => 'js/custodian_student/index.js',
            'dialog' => true,
            'datatable' => true,
            'form'=>true,
        ]);
    }

    /**
     * 添加监护人页面
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('custodian_student.create',[
            'js' => 'js/custodian_student/create.js',
            'form' => true
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CustodianStudent  $custodianStudent
     * @return \Illuminate\Http\Response
     */
    public function show(CustodianStudent $custodianStudent)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CustodianStudent  $custodianStudent
     * @return \Illuminate\Http\Response
     */
    public function edit(CustodianStudent $custodianStudent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CustodianStudent  $custodianStudent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustodianStudent $custodianStudent)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CustodianStudent  $custodianStudent
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustodianStudent $custodianStudent)
    {
        //
    }
}
